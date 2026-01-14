<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Content;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    /**
     * Display all reports with filtering
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['status', 'type', 'reason', 'search', 'date_from', 'date_to']);
        $perPage = $request->integer('per_page', 15);

        $query = Report::query()
            ->with(['reporter:id,username,full_name,email', 'resolver:id,full_name,email', 'reportable'])
            ->orderByDesc('created_at');

        // Filter by status
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by reportable type
        if (! empty($filters['type'])) {
            $typemap = [
                'user' => User::class,
                'content' => Content::class,
                'community' => Community::class,
            ];
            if (isset($typemap[$filters['type']])) {
                $query->where('reportable_type', $typemap[$filters['type']]);
            }
        }

        // Filter by reason
        if (! empty($filters['reason'])) {
            $query->where('reason', $filters['reason']);
        }

        // Search in description
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('reporter', function ($rq) use ($search) {
                        $rq->where('username', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Date range filter
        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $reports = $query->paginate($perPage);

        // Get statistics
        $statistics = $this->getReportStatistics();

        return Inertia::render('Admin/Reports/Index', [
            'reports' => $reports,
            'statistics' => $statistics,
            'filters' => $filters,
        ]);
    }

    /**
     * Display a specific report with full details
     */
    public function show(Report $report): Response
    {
        $report->load([
            'reporter:id,username,full_name,email,profile_image_url',
            'resolver:id,full_name,email',
            'reportable',
        ]);

        // Load additional context based on reportable type
        $context = $this->getReportableContext($report);

        return Inertia::render('Admin/Reports/Show', [
            'report' => $report,
            'context' => $context,
        ]);
    }

    /**
     * Mark report as reviewing
     */
    public function review(Report $report, Request $request)
    {
        $admin = Auth::guard('admin')->user();

        DB::transaction(function () use ($report, $admin) {
            $report->markAsReviewing($admin);
        });

        return redirect()->back()->with('success', 'Report marked as under review.');
    }

    /**
     * Resolve a report
     */
    public function resolve(Report $report, Request $request)
    {
        $validated = $request->validate([
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
            'action_on_reportable' => ['nullable', 'in:none,ban,suspend,delete,flag'],
        ]);

        $admin = Auth::guard('admin')->user();

        DB::transaction(function () use ($report, $admin, $validated) {
            // Take action on the reported item if specified
            if (! empty($validated['action_on_reportable']) && $validated['action_on_reportable'] !== 'none') {
                $this->takeActionOnReportable($report, $validated['action_on_reportable'], $admin);
            }

            // Resolve the report
            $report->resolve($admin, $validated['resolution_notes']);
        });

        return redirect()->back()->with('success', 'Report resolved successfully.');
    }

    /**
     * Dismiss a report
     */
    public function dismiss(Report $report, Request $request)
    {
        $validated = $request->validate([
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $admin = Auth::guard('admin')->user();

        DB::transaction(function () use ($report, $admin, $validated) {
            $report->dismiss($admin, $validated['resolution_notes']);
        });

        return redirect()->back()->with('success', 'Report dismissed.');
    }

    /**
     * Bulk update report statuses
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'report_ids' => ['required', 'array'],
            'report_ids.*' => ['exists:reports,id'],
            'action' => ['required', 'in:resolve,dismiss,review'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $admin = Auth::guard('admin')->user();

        DB::transaction(function () use ($validated, $admin) {
            $reports = Report::whereIn('id', $validated['report_ids'])->get();

            foreach ($reports as $report) {
                match ($validated['action']) {
                    'resolve' => $report->resolve($admin, $validated['notes'] ?? null),
                    'dismiss' => $report->dismiss($admin, $validated['notes'] ?? null),
                    'review' => $report->markAsReviewing($admin),
                };
            }
        });

        return redirect()->back()->with('success', count($validated['report_ids']).' reports updated successfully.');
    }

    /**
     * Get report statistics
     */
    private function getReportStatistics(): array
    {
        return [
            'total' => Report::count(),
            'pending' => Report::pending()->count(),
            'reviewing' => Report::reviewing()->count(),
            'resolved' => Report::resolved()->count(),
            'dismissed' => Report::dismissed()->count(),
            'today' => Report::whereDate('created_at', today())->count(),
            'this_week' => Report::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => Report::where('created_at', '>=', now()->startOfMonth())->count(),
        ];
    }

    /**
     * Get additional context for reportable item
     */
    private function getReportableContext(Report $report): array
    {
        $reportable = $report->reportable;

        if (! $reportable) {
            return [];
        }

        $context = [];

        if ($reportable instanceof User) {
            $context['type'] = 'user';
            $context['user_status'] = $reportable->status;
            $context['is_verified'] = $reportable->is_verified;
            $context['joined_date'] = $reportable->created_at;
            $context['total_reports'] = $reportable->receivedReports()->count();
            $context['communities_count'] = $reportable->communities()->count();
            $context['content_count'] = $reportable->contents()->count();
        } elseif ($reportable instanceof Content) {
            $context['type'] = 'content';
            $context['content_status'] = $reportable->status;
            $context['author'] = $reportable->user;
            $context['community'] = $reportable->community;
            $context['created_at'] = $reportable->created_at;
            $context['total_reports'] = $reportable->reports()->count();
        } elseif ($reportable instanceof Community) {
            $context['type'] = 'community';
            $context['community_status'] = $reportable->status;
            $context['owner'] = $reportable->owner;
            $context['members_count'] = $reportable->members()->count();
            $context['created_at'] = $reportable->created_at;
            $context['total_reports'] = $reportable->reports()->count();
        }

        return $context;
    }

    /**
     * Take action on the reportable item
     */
    private function takeActionOnReportable(Report $report, string $action, $admin): void
    {
        $reportable = $report->reportable;

        if (! $reportable) {
            return;
        }

        if ($reportable instanceof User) {
            match ($action) {
                'ban' => $reportable->update(['status' => User::STATUS_BANNED]),
                'suspend' => $reportable->update(['status' => User::STATUS_SUSPENDED]),
                'delete' => $reportable->delete(),
                default => null
            };
        } elseif ($reportable instanceof Content) {
            match ($action) {
                'flag' => $reportable->update(['status' => Content::STATUS_FLAGGED]),
                'delete' => $reportable->delete(),
                default => null
            };
        } elseif ($reportable instanceof Community) {
            match ($action) {
                'suspend' => $reportable->update(['status' => Community::STATUS_SUSPENDED]),
                'delete' => $reportable->delete(),
                default => null
            };
        }
    }
}
