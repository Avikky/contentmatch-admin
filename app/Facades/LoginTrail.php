<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Login Trail Facade for easy access to LoginTrailService
 * 
 * @method static \App\Models\LoginTrail log(string $action, ?\App\Models\User $user = null, ?string $username = null, bool $success = true, ?string $reason = null, array $additionalData = [], ?\Illuminate\Http\Request $request = null)
 * @method static \App\Models\LoginTrail logLogin(\App\Models\User $user, array $additionalData = [], ?\Illuminate\Http\Request $request = null)
 * @method static \App\Models\LoginTrail logFailedLogin(string $username, string $reason = 'Invalid credentials', array $additionalData = [], ?\Illuminate\Http\Request $request = null)
 * @method static \App\Models\LoginTrail logLogout(\App\Models\User $user, array $additionalData = [], ?\Illuminate\Http\Request $request = null)
 * @method static \App\Models\LoginTrail logPasswordChange(\App\Models\User $user, bool $success = true, ?string $reason = null, array $additionalData = [], ?\Illuminate\Http\Request $request = null)
 * @method static \App\Models\LoginTrail logPasswordReset(\App\Models\User $user, bool $success = true, ?string $reason = null, array $additionalData = [], ?\Illuminate\Http\Request $request = null)
 * @method static \App\Models\LoginTrail logPasswordResetRequest(?\App\Models\User $user, string $email, bool $success = true, array $additionalData = [], ?\Illuminate\Http\Request $request = null)
 * @method static \Illuminate\Pagination\LengthAwarePaginator getTrails(array $filters = [], int $perPage = 15)
 * @method static \Illuminate\Support\Collection getUserRecentActivity(int $userId, int $limit = 10)
 * @method static \Illuminate\Support\Collection getFailedAttempts(?string $username = null, ?string $ipAddress = null, int $minutes = 15)
 * @method static array getStatistics(int $days = 30)
 * @method static int cleanOldRecords(int $daysToKeep = 365)
 */
class LoginTrail extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'login-trail';
    }
}