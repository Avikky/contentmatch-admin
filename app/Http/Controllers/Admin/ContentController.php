<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Content;
use App\Models\Engagement;
use App\Models\Feedback;
use App\Models\Hashtag;
use App\Models\Platform;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ContentController extends Controller
{
    /**
     * Display a listing of all content with filters
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $platform = $request->string('platform')->toString();
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();

        $contents = Content::query()
            ->with(['user:id,full_name,username,email', 'platform:id,name', 'community:id,name', 'metrics'])
            ->withCount(['reports' => function ($q) {
                $q->where('status', 'pending');
            }, 'feedback', 'engagements', 'likes'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('full_name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($platform, fn ($q) => $q->whereHas('platform', fn ($pq) => $pq->where('name', $platform)))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $platforms = Platform::orderBy('name')->get(['id', 'name']);

        $statistics = [
            'total' => Content::count(),
            'published' => Content::where('status', 'published')->count(),
            'reported_count' => Content::has('reports')->count(),
            'removed' => Content::where('status', 'removed')->count(),
            'new_today' => Content::whereDate('created_at', today())->count(),
            'avg_rating' => Feedback::avg('rating') ? number_format(Feedback::avg('rating'), 1) : '0.0',
            'feedback_count' => Feedback::count(),
        ];

        return Inertia::render('Admin/Content/Index', [
            'contents' => $contents,
            'platforms' => $platforms,
            'statistics' => $statistics,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'platform' => $platform,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    /**
     * Display detailed view of a single content
     */
    public function show(Content $content): Response
    {
        $content->load([
            'user:id,full_name,username,email,profile_image_url,status',
            'platform:id,name',
            'community:id,name,slug',
            'metrics',
            'categories:id,name',
            'hashtags:id,name,usage_count',
            'reports' => function ($query) {
                $query->with('reporter:id,full_name,username,email')
                    ->latest();
            },
            'feedback' => function ($query) {
                $query->with('user:id,full_name,username,email')
                    ->latest();
            },
            'engagements' => function ($query) {
                $query->with('user:id,full_name,username,email')
                    ->latest();
            },
            'likes' => function ($query) {
                $query->with('user:id,full_name,username')
                    ->latest()
                    ->limit(50);
            },
        ]);

        // Get engagement statistics
        $engagementStats = [
            'total_engagements' => $content->engagements_count ?? $content->engagements()->count(),
            'subscribed' => $content->engagements()->where('did_subscribe', true)->count(),
            'liked' => $content->engagements()->where('did_like', true)->count(),
            'commented' => $content->engagements()->where('did_comment', true)->count(),
        ];

        // Get feedback statistics
        $feedbackStats = [
            'total_feedback' => $content->feedback_count ?? $content->feedback()->count(),
            'average_rating' => $content->feedback()->avg('rating'),
            'rating_distribution' => $content->feedback()
                ->select('rating', DB::raw('count(*) as count'))
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray(),
        ];

        // Get report statistics
        $reportStats = [
            'total_reports' => $content->reports()->count(),
            'pending' => $content->reports()->where('status', 'pending')->count(),
            'reviewing' => $content->reports()->where('status', 'reviewing')->count(),
            'resolved' => $content->reports()->where('status', 'resolved')->count(),
            'dismissed' => $content->reports()->where('status', 'dismissed')->count(),
        ];

        return Inertia::render('Admin/Content/Show', [
            'content' => $content,
            'stats' => [
                'reports_count' => $reportStats['total_reports'],
                'engagements_count' => $engagementStats['total_engagements'],
                'feedback_count' => $feedbackStats['total_feedback'],
                'average_rating' => $feedbackStats['average_rating'],
                'engagement' => $engagementStats,
                'feedback' => $feedbackStats,
                'reports' => $reportStats,
            ],
        ]);
    }

    /**
     * Display reported content management interface
     */
    public function reported(Request $request): Response
    {
        $status = $request->string('status')->toString() ?: 'pending';
        $search = $request->string('search')->toString();

        $reports = Report::query()
            ->where('reportable_type', Content::class)
            ->with([
                'reporter:id,full_name,username,email',
                'reportable' => function ($query) {
                    $query->with(['user:id,full_name,username,email', 'platform:id,name']);
                },
                'resolver:id,full_name',
            ])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reason', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('reporter', function ($userQuery) use ($search) {
                            $userQuery->where('full_name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $statistics = [
            'total' => Report::where('reportable_type', Content::class)->count(),
            'pending' => Report::where('reportable_type', Content::class)->where('status', 'pending')->count(),
            'reviewing' => Report::where('reportable_type', Content::class)->where('status', 'reviewing')->count(),
            'resolved' => Report::where('reportable_type', Content::class)->where('status', 'resolved')->count(),
            'dismissed' => Report::where('reportable_type', Content::class)->where('status', 'dismissed')->count(),
        ];

        return Inertia::render('Admin/Content/Reported', [
            'reports' => $reports,
            'statistics' => $statistics,
            'filters' => [
                'status' => $status,
                'search' => $search,
            ],
        ]);
    }

    /**
     * Analytics dashboard for content metrics
     */
    public function analytics(Request $request): Response
    {
        $days = $request->input('days', 30); // Default to 30 days
        $dateFrom = now()->subDays((int) $days);

        // Trending hashtags
        $trendingHashtags = Hashtag::query()
            ->withCount(['contents as recent_usage' => function ($q) use ($dateFrom) {
                $q->where('created_at', '>=', $dateFrom);
            }])
            ->having('recent_usage', '>', 0)
            ->orderByDesc('recent_usage')
            ->limit(20)
            ->get(['id', 'name', 'usage_count']);

        // Top categories
        $topCategories = Category::query()
            ->withCount(['contents as recent_contents' => function ($q) use ($dateFrom) {
                $q->where('created_at', '>=', $dateFrom);
            }])
            ->having('recent_contents', '>', 0)
            ->orderByDesc('recent_contents')
            ->limit(10)
            ->get(['id', 'name']);

        // Most engaged content (last period)
        $mostEngagedContent = Content::query()
            ->with(['user:id,full_name,username', 'platform:id,name'])
            ->withCount(['engagements', 'feedback', 'likes'])
            ->where('created_at', '>=', $dateFrom)
            ->orderByDesc('engagements_count')
            ->limit(10)
            ->get();

        // Most active users (content creators)
        $mostActiveCreators = User::query()
            ->withCount(['contents as recent_contents' => function ($q) use ($dateFrom) {
                $q->where('created_at', '>=', $dateFrom);
            }])
            ->having('recent_contents', '>', 0)
            ->orderByDesc('recent_contents')
            ->limit(10)
            ->get(['id', 'full_name', 'username', 'email', 'profile_image_url']);

        // Most active engagers
        $mostActiveEngagers = User::query()
            ->withCount(['engagements as recent_engagements' => function ($q) use ($dateFrom) {
                $q->where('created_at', '>=', $dateFrom);
            }])
            ->having('recent_engagements', '>', 0)
            ->orderByDesc('recent_engagements')
            ->limit(10)
            ->get(['id', 'full_name', 'username', 'email']);

        // Content per day (for chart)
        $contentPerDay = Content::query()
            ->where('created_at', '>=', $dateFrom)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Engagement per day (for chart)
        $engagementPerDay = Engagement::query()
            ->where('created_at', '>=', $dateFrom)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Platform distribution
        $platformDistribution = Content::query()
            ->select('platform_id', DB::raw('count(*) as count'))
            ->where('created_at', '>=', $dateFrom)
            ->with('platform:id,name')
            ->groupBy('platform_id')
            ->get();

        // Overall stats
        $stats = [
            'total_content' => Content::count(),
            'recent_content' => Content::where('created_at', '>=', $dateFrom)->count(),
            'total_engagements' => Engagement::count(),
            'recent_engagements' => Engagement::where('created_at', '>=', $dateFrom)->count(),
            'total_feedback' => Feedback::count(),
            'recent_feedback' => Feedback::where('created_at', '>=', $dateFrom)->count(),
            'average_rating' => Feedback::where('created_at', '>=', $dateFrom)->avg('rating'),
            'total_users' => User::count(),
            'active_users' => User::where('last_active_at', '>=', $dateFrom)->count(),
        ];

        // Transform platform distribution for frontend
        $platformStats = $platformDistribution->map(function ($item) {
            $total = $platformDistribution->sum('count');

            return [
                'name' => $item->platform->name ?? 'Unknown',
                'count' => $item->count,
                'percentage' => $total > 0 ? round(($item->count / $total) * 100, 1) : 0,
            ];
        });

        return Inertia::render('Admin/Content/Analytics', [
            'trendingHashtags' => $trendingHashtags,
            'topCategories' => $topCategories,
            'mostEngagedContent' => $mostEngagedContent,
            'topCreators' => $mostActiveCreators,
            'topEngagers' => $mostActiveEngagers,
            'contentPerDay' => $contentPerDay,
            'engagementPerDay' => $engagementPerDay,
            'platformStats' => $platformStats,
            'stats' => $stats,
            'days' => (int) $days,
        ]);
    }

    /**
     * Display feedback for content
     */
    public function feedback(Request $request): Response
    {
        $contentId = $request->input('content_id');
        $rating = $request->string('rating')->toString();

        $feedbacks = Feedback::query()
            ->with(['user:id,full_name,username,email,profile_image_url', 'content:id,title,slug'])
            ->when($contentId, fn ($q) => $q->where('content_id', $contentId))
            ->when($rating, fn ($q) => $q->where('rating', $rating))
            ->withCount('likes')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Feedback::count(),
            'average_rating' => Feedback::avg('rating'),
            'positive' => Feedback::where('rating', '>=', 4)->count(),
            'neutral' => Feedback::whereBetween('rating', [2, 3])->count(),
            'negative' => Feedback::where('rating', '<=', 1)->count(),
        ];

        return Inertia::render('Admin/Content/Feedback', [
            'feedbacks' => $feedbacks,
            'stats' => $stats,
            'filters' => [
                'content_id' => $contentId,
                'rating' => $rating,
            ],
        ]);
    }

    /**
     * Display engagement data
     */
    public function engagements(Request $request): Response
    {
        $contentId = $request->input('content_id');

        $engagements = Engagement::query()
            ->with(['user:id,full_name,username,email', 'content:id,title,slug'])
            ->when($contentId, fn ($q) => $q->where('content_id', $contentId))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Engagement::count(),
            'did_engage' => Engagement::where('did_engage', true)->count(),
            'did_subscribe' => Engagement::where('did_subscribe', true)->count(),
            'did_like' => Engagement::where('did_like', true)->count(),
            'did_comment' => Engagement::where('did_comment', true)->count(),
        ];

        return Inertia::render('Admin/Content/Engagements', [
            'engagements' => $engagements,
            'stats' => $stats,
            'filters' => [
                'content_id' => $contentId,
            ],
        ]);
    }

    /**
     * Update content status
     */
    public function updateStatus(Content $content, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,published,draft,archived,reported,removed'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $content->update(['status' => $validated['status']]);

        // Log activity
        activity()
            ->performedOn($content)
            ->withProperties([
                'status' => $validated['status'],
                'reason' => $validated['reason'] ?? null,
            ])
            ->log('content_status_updated');

        return Redirect::back()->with('success', 'Content status updated successfully.');
    }

    /**
     * Ban content (set status to removed and potentially ban user)
     */
    public function ban(Content $content, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'ban_user' => ['boolean'],
            'ban_duration' => ['nullable', 'integer', 'min:1'], // days
        ]);

        DB::beginTransaction();
        try {
            // Update content status
            $content->update(['status' => 'removed']);

            // Update all pending reports as resolved
            $content->reports()
                ->where('status', 'pending')
                ->update([
                    'status' => 'resolved',
                    'resolved_by' => auth()->id(),
                    'resolution_notes' => $validated['reason'],
                    'resolved_at' => now(),
                ]);

            // Optionally ban the user
            if ($validated['ban_user'] ?? false) {
                $content->user->update(['status' => 'banned']);
            }

            // Log activity
            activity()
                ->performedOn($content)
                ->withProperties($validated)
                ->log('content_banned');

            DB::commit();

            return Redirect::back()->with('success', 'Content has been banned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return Redirect::back()->with('error', 'Failed to ban content: '.$e->getMessage());
        }
    }

    /**
     * Remove content permanently
     */
    public function destroy(Content $content, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        // Log before deletion
        activity()
            ->performedOn($content)
            ->withProperties([
                'content_id' => $content->id,
                'title' => $content->title,
                'user_id' => $content->user_id,
                'reason' => $validated['reason'],
            ])
            ->log('content_deleted');

        $content->delete();

        return Redirect::back()->with('success', 'Content has been removed successfully.');
    }

    /**
     * Resolve a report
     */
    public function resolveReport(Report $report, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:dismiss,ban_content,remove_content,warn_user'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();
        try {
            $content = $report->reportable;

            // Perform action based on admin decision
            switch ($validated['action']) {
                case 'ban_content':
                    $content->update(['status' => 'removed']);
                    break;
                case 'remove_content':
                    $content->delete();
                    break;
                case 'warn_user':
                    // Could implement warning system here
                    break;
                case 'dismiss':
                    // Just mark as dismissed
                    break;
            }

            // Update report status
            $report->update([
                'status' => $validated['action'] === 'dismiss' ? 'dismissed' : 'resolved',
                'resolved_by' => auth()->id(),
                'resolution_notes' => $validated['notes'],
                'resolved_at' => now(),
            ]);

            // Log activity
            activity()
                ->performedOn($report)
                ->withProperties($validated)
                ->log('report_resolved');

            DB::commit();

            return Redirect::back()->with('success', 'Report has been resolved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return Redirect::back()->with('error', 'Failed to resolve report: '.$e->getMessage());
        }
    }

    /**
     * Bulk update content status
     */
    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content_ids' => ['required', 'array'],
            'content_ids.*' => ['exists:contents,id'],
            'status' => ['required', 'in:active,published,draft,archived,reported,removed'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        Content::whereIn('id', $validated['content_ids'])
            ->update(['status' => $validated['status']]);

        activity()
            ->withProperties($validated)
            ->log('content_bulk_status_update');

        return Redirect::back()->with('success', count($validated['content_ids']).' content items updated successfully.');
    }

    /**
     * Export content data
     */
    public function export(Request $request)
    {
        $contents = Content::query()
            ->with(['user:id,full_name,username', 'platform:id,name'])
            ->withCount(['reports', 'feedback', 'engagements', 'likes'])
            ->get();

        $csv = "ID,Title,Creator,Platform,Status,Reports,Feedback,Engagements,Likes,Created At\n";

        foreach ($contents as $content) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $content->id,
                str_replace(',', ';', $content->title),
                $content->user->full_name ?? $content->user->username,
                $content->platform->name,
                $content->status,
                $content->reports_count,
                $content->feedback_count,
                $content->engagements_count,
                $content->likes_count,
                $content->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="content-export-'.now()->format('Y-m-d').'.csv"',
        ]);
    }
}
