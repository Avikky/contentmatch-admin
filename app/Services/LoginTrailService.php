<?php

namespace App\Services;

use App\Models\LoginTrail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LoginTrailService
{
    /**
     * Log an authentication activity
     */
    public function log(
        string $action,
        ?User $user = null,
        ?string $username = null,
        bool $success = true,
        ?string $reason = null,
        array $additionalData = [],
        ?Request $request = null
    ): LoginTrail {
        $request = $request ?? request();

        return LoginTrail::create([
            'user_id' => $user?->id,
            'username' => $username ?? $user?->username ?? $user?->email,
            'action' => $action,
            'success' => $success,
            'ip_address' => $this->getClientIpAddress($request),
            'user_agent' => $request->userAgent(),
            'additional_data' => $additionalData,
            'session_id' => $request->session()?->getId(),
            'device_type' => $this->getDeviceType($request->userAgent()),
            'browser' => $this->getBrowserInfo($request->userAgent()),
            'platform' => $this->getPlatformInfo($request->userAgent()),
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }

    /**
     * Log successful login
     */
    public function logLogin(User $user, array $additionalData = [], ?Request $request = null): LoginTrail
    {
        return $this->log(
            LoginTrail::ACTION_LOGIN,
            $user,
            null,
            true,
            null,
            array_merge($additionalData, ['login_time' => now()]),
            $request
        );
    }

    /**
     * Log failed login attempt
     */
    public function logFailedLogin(
        string $username,
        string $reason = 'Invalid credentials',
        array $additionalData = [],
        ?Request $request = null
    ): LoginTrail {
        return $this->log(
            LoginTrail::ACTION_FAILED_LOGIN,
            null,
            $username,
            false,
            $reason,
            array_merge($additionalData, ['attempt_time' => now()]),
            $request
        );
    }

    /**
     * Log logout
     */
    public function logLogout(User $user, array $additionalData = [], ?Request $request = null): LoginTrail
    {
        return $this->log(
            LoginTrail::ACTION_LOGOUT,
            $user,
            null,
            true,
            null,
            array_merge($additionalData, ['logout_time' => now()]),
            $request
        );
    }

    /**
     * Log password change
     */
    public function logPasswordChange(
        User $user,
        bool $success = true,
        ?string $reason = null,
        array $additionalData = [],
        ?Request $request = null
    ): LoginTrail {
        return $this->log(
            LoginTrail::ACTION_PASSWORD_CHANGE,
            $user,
            null,
            $success,
            $reason,
            array_merge($additionalData, ['change_time' => now()]),
            $request
        );
    }

    /**
     * Log password reset
     */
    public function logPasswordReset(
        User $user,
        bool $success = true,
        ?string $reason = null,
        array $additionalData = [],
        ?Request $request = null
    ): LoginTrail {
        return $this->log(
            LoginTrail::ACTION_PASSWORD_RESET,
            $user,
            null,
            $success,
            $reason,
            array_merge($additionalData, ['reset_time' => now()]),
            $request
        );
    }

    /**
     * Log password reset request
     */
    public function logPasswordResetRequest(
        ?User $user,
        string $email,
        bool $success = true,
        array $additionalData = [],
        ?Request $request = null
    ): LoginTrail {
        return $this->log(
            LoginTrail::ACTION_PASSWORD_RESET_REQUEST,
            $user,
            $email,
            $success,
            null,
            array_merge($additionalData, ['request_time' => now()]),
            $request
        );
    }

    /**
     * Get paginated login trails with optional filters
     */
    public function getTrails(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = LoginTrail::with('user')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (! empty($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        if (! empty($filters['action'])) {
            $query->byAction($filters['action']);
        }

        if (isset($filters['success'])) {
            $query->bySuccess($filters['success']);
        }

        if (! empty($filters['ip_address'])) {
            $query->byIpAddress($filters['ip_address']);
        }

        if (! empty($filters['date_from']) && ! empty($filters['date_to'])) {
            $query->dateRange($filters['date_from'], $filters['date_to']);
        }

        if (! empty($filters['days'])) {
            $query->recent($filters['days']);
        }

        if (! empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get recent activity for a specific user
     */
    public function getUserRecentActivity(int $userId, int $limit = 10): Collection
    {
        return LoginTrail::with('user')
            ->byUser($userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get failed login attempts for a user/IP in a time period
     */
    public function getFailedAttempts(
        ?string $username = null,
        ?string $ipAddress = null,
        int $minutes = 15
    ): Collection {
        $query = LoginTrail::byAction(LoginTrail::ACTION_FAILED_LOGIN)
            ->bySuccess(false)
            ->where('created_at', '>=', now()->subMinutes($minutes));

        if ($username) {
            $query->where('username', $username);
        }

        if ($ipAddress) {
            $query->byIpAddress($ipAddress);
        }

        return $query->get();
    }

    /**
     * Get login statistics
     */
    public function getStatistics(int $days = 30): array
    {
        $from = now()->subDays($days);

        return [
            'total_logins' => LoginTrail::byAction(LoginTrail::ACTION_LOGIN)
                ->where('created_at', '>=', $from)
                ->count(),

            'failed_attempts' => LoginTrail::byAction(LoginTrail::ACTION_FAILED_LOGIN)
                ->where('created_at', '>=', $from)
                ->count(),

            'unique_users' => LoginTrail::byAction(LoginTrail::ACTION_LOGIN)
                ->where('created_at', '>=', $from)
                ->distinct('user_id')
                ->count(),

            'password_changes' => LoginTrail::byAction(LoginTrail::ACTION_PASSWORD_CHANGE)
                ->where('created_at', '>=', $from)
                ->count(),

            'password_resets' => LoginTrail::byAction(LoginTrail::ACTION_PASSWORD_RESET)
                ->where('created_at', '>=', $from)
                ->count(),
        ];
    }

    /**
     * Clean old records (for maintenance)
     */
    public function cleanOldRecords(int $daysToKeep = 365): int
    {
        return LoginTrail::where('created_at', '<', now()->subDays($daysToKeep))
            ->delete();
    }

    /**
     * Get client IP address
     */
    protected function getClientIpAddress(Request $request): ?string
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];

        foreach ($ipKeys as $key) {
            if ($request->server($key) && filter_var($request->server($key), FILTER_VALIDATE_IP)) {
                return $request->server($key);
            }
        }

        return $request->ip();
    }

    /**
     * Get device type
     */
    protected function getDeviceType(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);

        if (preg_match('/mobile|android|iphone|ipad|phone/i', $userAgent)) {
            if (preg_match('/ipad|tablet/i', $userAgent)) {
                return 'tablet';
            }

            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Get browser information from user agent
     */
    protected function getBrowserInfo(string $userAgent): string
    {
        $browsers = [
            'Chrome' => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari' => 'Safari',
            'Opera' => 'Opera',
            'Edge' => 'Edge',
            'Internet Explorer' => 'MSIE',
        ];

        foreach ($browsers as $browser => $pattern) {
            if (preg_match("/{$pattern}/i", $userAgent)) {
                return $browser;
            }
        }

        return 'Unknown';
    }

    /**
     * Get platform information from user agent
     */
    protected function getPlatformInfo(string $userAgent): string
    {
        $platforms = [
            'Windows' => 'Windows',
            'Mac OS' => 'Mac',
            'Linux' => 'Linux',
            'Android' => 'Android',
            'iOS' => 'iPhone|iPad',
        ];

        foreach ($platforms as $platform => $pattern) {
            if (preg_match("/{$pattern}/i", $userAgent)) {
                return $platform;
            }
        }

        return 'Unknown';
    }
}
