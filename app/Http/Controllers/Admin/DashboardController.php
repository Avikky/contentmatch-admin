<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Community;
use App\Models\Content;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $admin = $request->user('admin');

        // User Analytics
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $newUsersThisWeek = User::where('created_at', '>=', now()->subWeek())->count();
        $newUsersThisMonth = User::where('created_at', '>=', now()->subMonth())->count();
        $premiumUsers = User::where('is_premium', true)->count();
        $suspendedUsers = User::where('status', 'suspended')->count();
        $bannedUsers = User::where('status', 'banned')->count();

        // Community Analytics
        $totalCommunities = Community::count();
        $activeCommunities = Community::where('status', 'active')->count();
        $newCommunitiesThisWeek = Community::where('created_at', '>=', now()->subWeek())->count();
        $newCommunitiesThisMonth = Community::where('created_at', '>=', now()->subMonth())->count();
        $publicCommunities = Community::where('type', 'public')->count();
        $privateCommunities = Community::where('type', 'private')->count();

        // Content Analytics
        $totalContent = Content::count();
        $publishedContent = Content::where('status', 'published')->count();
        $newContentThisWeek = Content::where('created_at', '>=', now()->subWeek())->count();
        $newContentThisMonth = Content::where('created_at', '>=', now()->subMonth())->count();
        $pendingContent = Content::where('status', 'pending')->count();
        $flaggedContent = Content::where('status', 'archived')->count();

        // Admin Analytics (if superadmin)
        $adminStats = null;
        if ($admin->role === 'superadmin') {
            $adminStats = [
                'total' => Admin::count(),
                'active' => Admin::where('is_active', true)->count(),
                'superadmins' => Admin::where('role', 'superadmin')->count(),
                'moderators' => Admin::where('role', 'moderator')->count(),
            ];
        }

        // Reports Analytics
        $totalReports = Report::count();
        $pendingReports = Report::where('status', 'pending')->count();
        $resolvedReportsThisWeek = Report::where('status', 'resolved')
            ->where('updated_at', '>=', now()->subWeek())
            ->count();

        // Recent Activity - Last 7 days trend
        $userGrowthTrend = User::where('created_at', '>=', now()->subWeek())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $communityGrowthTrend = Community::where('created_at', '>=', now()->subWeek())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $contentGrowthTrend = Content::where('created_at', '>=', now()->subWeek())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top Communities by Members
        $topCommunities = Community::withCount('members')
            ->orderByDesc('members_count')
            ->limit(5)
            ->get(['id', 'name', 'members_count']);

        // Recent Reports
        $recentReports = Report::with(['reporter:id,name,email', 'reportable'])
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'admin' => [
                'name' => $admin?->full_name ?? $admin?->display_name ?? $admin?->user_name,
                'role' => $admin?->role,
            ],
            'analytics' => [
                'users' => [
                    'total' => $totalUsers,
                    'active' => $activeUsers,
                    'new_this_week' => $newUsersThisWeek,
                    'new_this_month' => $newUsersThisMonth,
                    'premium' => $premiumUsers,
                    'suspended' => $suspendedUsers,
                    'banned' => $bannedUsers,
                    'growth_trend' => $userGrowthTrend,
                ],
                'communities' => [
                    'total' => $totalCommunities,
                    'active' => $activeCommunities,
                    'new_this_week' => $newCommunitiesThisWeek,
                    'new_this_month' => $newCommunitiesThisMonth,
                    'public' => $publicCommunities,
                    'private' => $privateCommunities,
                    'growth_trend' => $communityGrowthTrend,
                    'top_communities' => $topCommunities,
                ],
                'content' => [
                    'total' => $totalContent,
                    'published' => $publishedContent,
                    'new_this_week' => $newContentThisWeek,
                    'new_this_month' => $newContentThisMonth,
                    'pending' => $pendingContent,
                    'flagged' => $flaggedContent,
                    'growth_trend' => $contentGrowthTrend,
                ],
                'admins' => $adminStats,
                'reports' => [
                    'total' => $totalReports,
                    'pending' => $pendingReports,
                    'resolved_this_week' => $resolvedReportsThisWeek,
                    'recent' => $recentReports,
                ],
            ],
        ]);
    }
}
