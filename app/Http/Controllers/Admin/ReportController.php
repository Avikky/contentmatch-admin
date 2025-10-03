<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\EngagementMetric;
use App\Models\Hashtag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
	public function index(Request $request): Response
	{
		$range = (int) $request->input('range', 30);
		$range = in_array($range, [7, 14, 30, 90]) ? $range : 30;

		$startDate = Carbon::now()->subDays($range - 1)->startOfDay();

		$userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as total')
			->where('created_at', '>=', $startDate)
			->groupBy('date')
			->orderBy('date')
			->get()
			->map(fn ($row) => [
				'date' => Carbon::parse($row->date)->toDateString(),
				'total' => (int) $row->total,
			]);

		$communityBreakdown = Community::withCount('members')
			->with(['owner:id,name,full_name'])
			->get()
			->map(function (Community $community) use ($startDate) {
				$engagement = $community->engagementMetrics()
					->where('recorded_for', '>=', $startDate)
					->selectRaw('SUM(posts) as posts, SUM(comments) as comments, SUM(likes) as likes, SUM(shares) as shares')
					->first();

				return [
					'id' => $community->id,
					'name' => $community->name,
					'members' => $community->members_count,
					'owner' => $community->owner?->display_name ?? 'Unassigned',
					'posts' => (int) ($engagement->posts ?? 0),
					'comments' => (int) ($engagement->comments ?? 0),
					'likes' => (int) ($engagement->likes ?? 0),
					'shares' => (int) ($engagement->shares ?? 0),
				];
			});

		$engagementByCommunity = EngagementMetric::select('community_id')
			->selectRaw('SUM(posts) as posts, SUM(comments) as comments, SUM(likes) as likes, SUM(shares) as shares, SUM(impressions) as impressions')
			->where('recorded_for', '>=', $startDate)
			->groupBy('community_id')
			->with('community:id,name')
			->orderByDesc(DB::raw('SUM(impressions)'))
			->get()
			->map(fn (EngagementMetric $metric) => [
				'community' => $metric->community?->name ?? 'All Communities',
				'posts' => (int) $metric->posts,
				'comments' => (int) $metric->comments,
				'likes' => (int) $metric->likes,
				'shares' => (int) $metric->shares,
				'impressions' => (int) $metric->impressions,
			]);

		$hashtagUsage = Hashtag::orderByDesc('usage_count')
			->take(15)
			->get(['id', 'tag', 'usage_count']);

		$summary = [
			'total_users' => User::count(),
			'total_admins' => User::admins()->count(),
			'total_communities' => Community::count(),
			'active_communities' => Community::where('status', 'active')->count(),
			'engagement_window_days' => $range,
			'engagement_totals' => EngagementMetric::where('recorded_for', '>=', $startDate)
				->selectRaw('SUM(posts) as posts, SUM(comments) as comments, SUM(likes) as likes, SUM(shares) as shares, SUM(impressions) as impressions')
				->first(),
		];

		return Inertia::render('Admin/Reports/Index', [
			'summary' => [
				'total_users' => $summary['total_users'],
				'total_admins' => $summary['total_admins'],
				'total_communities' => $summary['total_communities'],
				'active_communities' => $summary['active_communities'],
				'engagement_window_days' => $summary['engagement_window_days'],
				'engagement_totals' => [
					'posts' => (int) ($summary['engagement_totals']->posts ?? 0),
					'comments' => (int) ($summary['engagement_totals']->comments ?? 0),
					'likes' => (int) ($summary['engagement_totals']->likes ?? 0),
					'shares' => (int) ($summary['engagement_totals']->shares ?? 0),
					'impressions' => (int) ($summary['engagement_totals']->impressions ?? 0),
				],
			],
			'userGrowth' => $userGrowth,
			'communityBreakdown' => $communityBreakdown,
			'engagementByCommunity' => $engagementByCommunity,
			'hashtagUsage' => $hashtagUsage,
			'filters' => [
				'range' => $range,
			],
		]);
	}
}
