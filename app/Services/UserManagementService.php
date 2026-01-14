<?php

namespace App\Services;

use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\Report;
use App\Models\User;
use App\Models\UserModerationLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserManagementService
{
    /**
     * Get paginated users with optional filters
     */
    public function getUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()
            ->withCount(['communities', 'receivedReports', 'allSubscriptions', 'contents', 'followers', 'following'])
            ->with(['activeSubscription'])
            ->when(! isset($filters['include_deleted']), fn (Builder $q) => $q->whereNull('deleted_at'));

        // Filter by status
        if (! empty($filters['status'])) {
            $query->status($filters['status']);
        }

        // Filter by premium status
        if (isset($filters['is_premium'])) {
            $query->where('is_premium', (bool) $filters['is_premium']);
        }

        // Filter by verification status
        if (isset($filters['is_verified'])) {
            $query->where('is_verified', (bool) $filters['is_verified']);
        }

        // Search by name, email, or username
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Date range filters
        if (! empty($filters['created_from'])) {
            $query->where('created_at', '>=', $filters['created_from']);
        }

        if (! empty($filters['created_to'])) {
            $query->where('created_at', '<=', $filters['created_to']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * Get detailed user information with all related data
     */
    public function getUserDetails(int $userId): array
    {

        $user = User::withTrashed()
            ->with([
                'communities' => fn ($q) => $q->withPivot('role', 'status', 'notification_enabled', 'last_activity_at'),
                'activeSubscription',
                'allSubscriptions',
                'moderationLogs' => fn ($q) => $q->with('admin')->latest()->limit(20),
                'receivedReports' => fn ($q) => $q->with('reporter')->latest()->limit(20),
                'recentActivities',
                'analytics',
            ])
            ->withCount([
                'communities',
                'receivedReports',
                'allSubscriptions',
                'blockedUsers',
                'blockedByUsers',
                'activities',
                'contents',
                'followers',
                'following',
            ])
            ->findOrFail($userId);



        // Get user activities (already loaded via relationship)
        $activities = $user->recentActivities;

        // Get blocks
        $blockedUsers = $user->blockedUsers()->select('users.id', 'users.username', 'users.full_name', 'users.email')->get();
        $blockedBy = $user->blockedByUsers()->select('users.id', 'users.username', 'users.full_name', 'users.email')->get();

        return [
            'user' => $user,
            'activities' => $activities,
            'blocked_users' => $blockedUsers,
            'blocked_by_users' => $blockedBy,
        ];
    }

    /**
     * Ban a user
     */
    public function banUser(int $userId, ?int $adminId = null, ?string $reason = null): User
    {
        return DB::transaction(function () use ($userId, $adminId, $reason) {
            $user = User::findOrFail($userId);

            $previousStatus = $user->status;
            $user->status = User::STATUS_BANNED;
            $user->save();

            // Log the moderation action
            UserModerationLog::create([
                'user_id' => $userId,
                'admin_id' => $adminId,
                'action' => 'ban',
                'status_before' => $previousStatus,
                'status_after' => User::STATUS_BANNED,
                'reason' => $reason,
            ]);

            return $user->fresh();
        });
    }

    /**
     * Suspend a user
     */
    public function suspendUser(int $userId, ?int $adminId = null, ?string $reason = null): User
    {
        return DB::transaction(function () use ($userId, $adminId, $reason) {
            $user = User::findOrFail($userId);

            $previousStatus = $user->status;
            $user->status = User::STATUS_SUSPENDED;
            $user->save();

            // Log the moderation action
            UserModerationLog::create([
                'user_id' => $userId,
                'admin_id' => $adminId,
                'action' => 'suspend',
                'status_before' => $previousStatus,
                'status_after' => User::STATUS_SUSPENDED,
                'reason' => $reason,
            ]);

            return $user->fresh();
        });
    }

    /**
     * Reactivate a user
     */
    public function reactivateUser(int $userId, ?int $adminId = null, ?string $reason = null): User
    {
        return DB::transaction(function () use ($userId, $adminId, $reason) {
            $user = User::findOrFail($userId);

            $previousStatus = $user->status;
            $user->status = User::STATUS_ACTIVE;
            $user->save();

            // Log the moderation action
            UserModerationLog::create([
                'user_id' => $userId,
                'admin_id' => $adminId,
                'action' => 'reactivate',
                'status_before' => $previousStatus,
                'status_after' => User::STATUS_ACTIVE,
                'reason' => $reason,
            ]);

            return $user->fresh();
        });
    }

    /**
     * Soft delete a user
     */
    public function removeUser(int $userId, ?int $adminId = null, ?string $reason = null): bool
    {
        return DB::transaction(function () use ($userId, $adminId, $reason) {
            $user = User::findOrFail($userId);

            // Log the moderation action before deletion
            UserModerationLog::create([
                'user_id' => $userId,
                'admin_id' => $adminId,
                'action' => 'delete',
                'status_before' => $user->status,
                'status_after' => 'deleted',
                'reason' => $reason,
            ]);

            return $user->delete();
        });
    }

    /**
     * Remove user from a specific community
     */
    public function removeUserFromCommunity(int $userId, int $communityId, ?int $adminId = null, ?string $reason = null): void
    {
        DB::transaction(function () use ($userId, $communityId, $adminId, $reason) {
            $user = User::findOrFail($userId);
            $community = Community::findOrFail($communityId);

            // CommunityMember::where('user_id', $userId)
            //     ->where('community_id', $communityId)
            //     ->delete();

            $user->communities()->detach($communityId);

            // Log the moderation action
            UserModerationLog::create([
                'user_id' => $userId,
                'admin_id' => $adminId,
                'action' => 'remove_from_community',
                'reason' => $reason,
                'meta' => [
                    'community_id' => $communityId,
                    'community_name' => $community->name,
                ],
            ]);
        });
    }

    /**
     * Get user's reports
     */
    public function getUserReports(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Report::where('reportable_type', User::class)
            ->where('reportable_id', $userId)
            ->with(['reporter', 'resolver'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get user's communities
     */
    public function getUserCommunities(int $userId): array
    {
        $user = User::findOrFail($userId);

        return $user->communities()
            ->withPivot('role', 'status', 'created_at')
            ->with('owner')
            ->withCount('members')
            ->get()
            ->toArray();
    }

    /**
     * Get user's subscriptions
     */
    public function getUserSubscriptions(int $userId): array
    {
        $user = User::findOrFail($userId);

        return [
            'active' => $user->activeSubscription,
            'all' => $user->allSubscriptions()->orderByDesc('created_at')->get(),
            'is_premium' => $user->is_premium,
            'trial_ends_at' => $user->trial_ends_at,
        ];
    }

    /**
     * Get moderation history for a user
     */
    public function getModerationHistory(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return UserModerationLog::where('user_id', $userId)
            ->with('admin')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get user statistics
     */
    public function getUserStatistics(): array
    {
        return [
            'total' => User::count(),
            'active' => User::status(User::STATUS_ACTIVE)->count(),
            'suspended' => User::status(User::STATUS_SUSPENDED)->count(),
            'banned' => User::status(User::STATUS_BANNED)->count(),
            'premium' => User::where('is_premium', true)->count(),
            'verified' => User::where('is_verified', true)->count(),
            'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'new_this_week' => User::where('created_at', '>=', now()->startOfWeek())->count(),
        ];
    }
}
