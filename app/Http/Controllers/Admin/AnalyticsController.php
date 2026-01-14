<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Content;
use App\Models\Engagement;
use App\Models\Hashtag;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    /**
     * Display main analytics dashboard
     */
    public function index(Request $request): Response
    {
        $range = (int) $request->input('range', 30);
        $range = in_array($range, [7, 14, 30, 90]) ? $range : 30;

        $startDate = Carbon::now()->subDays($range - 1)->startOfDay();

        // User growth over time
        $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => Carbon::parse($row->date)->toDateString(),
                'total' => (int) $row->total,
            ]);

        // Community breakdown
        $communityStats = Community::withCount('members')
            ->with(['owner:id,username,full_name'])
            ->limit(10)
            ->orderByDesc('members_count')
            ->get()
            ->map(function (Community $community) {
                return [
                    'id' => $community->id,
                    'name' => $community->name,
                    'members' => $community->members_count,
                    'owner' => $community->owner?->full_name ?? $community->owner?->username ?? 'Unknown',
                    'status' => $community->status,
                ];
            });

        // Top hashtags
        $topHashtags = Hashtag::orderByDesc('usage_count')
            ->take(15)
            ->get(['id', 'tag', 'usage_count']);

        // Reports statistics
        $reportStats = [
            'pending' => Report::pending()->count(),
            'reviewing' => Report::reviewing()->count(),
            'resolved_this_week' => Report::resolved()->where('resolved_at', '>=', now()->startOfWeek())->count(),
        ];

        // Summary statistics
        $summary = [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'premium_users' => User::premium()->count(),
            'total_communities' => Community::count(),
            'active_communities' => Community::where('status', 'active')->count(),
            'total_content' => Content::count(),
            'total_reports' => Report::count(),
            'new_users_this_week' => User::where('created_at', '>=', now()->startOfWeek())->count(),
            'new_content_this_week' => Content::where('created_at', '>=', now()->startOfWeek())->count(),
        ];

        return Inertia::render('Admin/Analytics/Index', [
            'summary' => $summary,
            'userGrowth' => $userGrowth,
            'communityStats' => $communityStats,
            'topHashtags' => $topHashtags,
            'reportStats' => $reportStats,
            'filters' => [
                'range' => $range,
            ],
        ]);
    }

    /**
     * User-specific analytics
     */
    public function users(Request $request): Response
    {
        $range = (int) $request->input('range', 30);
        $range = in_array($range, [7, 14, 30, 90]) ? $range : 30;
        $startDate = Carbon::now()->subDays($range - 1)->startOfDay();

        // User registrations over time
        $registrations = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // User status breakdown
        $statusBreakdown = [
            'active' => User::active()->count(),
            'suspended' => User::suspended()->count(),
            'banned' => User::banned()->count(),
        ];

        // Verification status
        $verificationStats = [
            'verified' => User::verified()->count(),
            'unverified' => User::where('is_verified', false)->count(),
        ];

        // Premium vs Free
        $subscriptionStats = [
            'premium' => User::premium()->count(),
            'free' => User::where('is_premium', false)->count(),
        ];

        // Onboarding completion
        $onboardingStats = [
            'completed' => User::onboardingCompleted()->count(),
            'incomplete' => User::whereNull('onboarding_completed_at')->count(),
        ];

        // Most active users (by content created)
        $mostActiveUsers = User::withCount(['contents'])
            ->orderByDesc('contents_count')
            ->limit(10)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->full_name,
                'content_count' => $user->contents_count,
                'status' => $user->status,
            ]);

        return Inertia::render('Admin/Analytics/Users', [
            'registrations' => $registrations,
            'statusBreakdown' => $statusBreakdown,
            'verificationStats' => $verificationStats,
            'subscriptionStats' => $subscriptionStats,
            'onboardingStats' => $onboardingStats,
            'mostActiveUsers' => $mostActiveUsers,
            'filters' => ['range' => $range],
        ]);
    }

    /**
     * Community-specific analytics
     */
    public function communities(Request $request): Response
    {
        $range = (int) $request->input('range', 30);
        $range = in_array($range, [7, 14, 30, 90]) ? $range : 30;
        $startDate = Carbon::now()->subDays($range - 1)->startOfDay();

        // Community growth over time
        $communityGrowth = Community::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Communities by status
        $statusBreakdown = [
            'active' => Community::where('status', 'active')->count(),
            'suspended' => Community::where('status', 'suspended')->count(),
            'archived' => Community::where('status', 'archived')->count(),
        ];

        // Top communities by members
        $topByMembers = Community::withCount('members')
            ->orderByDesc('members_count')
            ->limit(10)
            ->get()
            ->map(fn ($community) => [
                'id' => $community->id,
                'name' => $community->name,
                'members_count' => $community->members_count,
                'status' => $community->status,
                'owner' => $community->owner?->username,
            ]);

        // Top communities by content
        $topByContent = Community::withCount('content')
            ->orderByDesc('content_count')
            ->limit(10)
            ->get()
            ->map(fn ($community) => [
                'id' => $community->id,
                'name' => $community->name,
                'content_count' => $community->content_count,
                'status' => $community->status,
            ]);

        return Inertia::render('Admin/Analytics/Communities', [
            'communityGrowth' => $communityGrowth,
            'statusBreakdown' => $statusBreakdown,
            'topByMembers' => $topByMembers,
            'topByContent' => $topByContent,
            'filters' => ['range' => $range],
        ]);
    }

    /**
     * Content-specific analytics
     */
    public function content(Request $request): Response
    {
        $range = (int) $request->input('range', 30);
        $range = in_array($range, [7, 14, 30, 90]) ? $range : 30;
        $startDate = Carbon::now()->subDays($range - 1)->startOfDay();

        // Content creation over time
        $contentGrowth = Content::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Content by status
        $statusBreakdown = [
            'approved' => Content::approved()->count(),
            'pending' => Content::pending()->count(),
            'flagged' => Content::flagged()->count(),
        ];

        // Content by platform
        $contentByPlatform = Content::select('platform_id')
            ->selectRaw('COUNT(*) as count')
            ->with('platform:id,name')
            ->groupBy('platform_id')
            ->get()
            ->map(fn ($item) => [
                'platform' => $item->platform->name ?? 'Unknown',
                'count' => $item->count,
            ]);

        // Most engaged content
        $mostEngaged = Content::withCount(['engagements', 'feedback'])
            ->orderByDesc('engagements_count')
            ->limit(10)
            ->get()
            ->map(fn ($content) => [
                'id' => $content->id,
                'title' => $content->title,
                'engagements_count' => $content->engagements_count,
                'feedback_count' => $content->feedback_count,
                'author' => $content->user->username,
            ]);

        return Inertia::render('Admin/Analytics/Content', [
            'contentGrowth' => $contentGrowth,
            'statusBreakdown' => $statusBreakdown,
            'contentByPlatform' => $contentByPlatform,
            'mostEngaged' => $mostEngaged,
            'filters' => ['range' => $range],
        ]);
    }

    /**
     * Engagement analytics
     */
    public function engagement(Request $request): Response
    {
        $range = (int) $request->input('range', 30);
        $range = in_array($range, [7, 14, 30, 90]) ? $range : 30;
        $startDate = Carbon::now()->subDays($range - 1)->startOfDay();

        // Engagement over time
        $engagementOverTime = Engagement::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Total engagement stats
        $totalEngagements = Engagement::where('created_at', '>=', $startDate)->count();

        // Most active users by engagement
        $activeEngagers = User::withCount(['engagements' => function ($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }])
            ->having('engagements_count', '>', 0)
            ->orderByDesc('engagements_count')
            ->limit(10)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'username' => $user->username,
                'engagements_count' => $user->engagements_count,
            ]);

        return Inertia::render('Admin/Analytics/Engagement', [
            'engagementOverTime' => $engagementOverTime,
            'totalEngagements' => $totalEngagements,
            'activeEngagers' => $activeEngagers,
            'filters' => ['range' => $range],
        ]);
    }
}
