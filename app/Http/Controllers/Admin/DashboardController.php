<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\EngagementMetric;
use App\Models\Hashtag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
	public function index(Request $request): Response
	{
		$today = Carbon::now();
		$sevenDaysAgo = $today->copy()->subDays(6)->startOfDay();
		$thirtyDaysAgo = $today->copy()->subDays(29)->startOfDay();

		$totals = [
			'total_users' => User::count(),
			'administrators' => User::admins()->count(),
			'new_users_week' => User::where('created_at', '>=', $sevenDaysAgo)->count(),
			'total_communities' => Community::count(),
			'active_communities' => Community::where('status', 'active')->count(),
		];

		$engagementAggregate = EngagementMetric::where('recorded_for', '>=', $sevenDaysAgo)
			->selectRaw('SUM(posts) as posts, SUM(comments) as comments, SUM(likes) as likes, SUM(shares) as shares, SUM(impressions) as impressions')
			->first();

		$topCommunities = Community::withCount('members')
			->with(['owner:id,name,full_name'])
			->orderByDesc('members_count')
			->take(5)
			->get()
			->map(fn (Community $community) => [
				'id' => $community->id,
				'name' => $community->name,
				'slug' => $community->slug,
				'members' => $community->members_count,
				'owner' => $community->owner?->display_name ?? 'Unassigned',
				'status' => $community->status,
				'visibility' => $community->visibility,
			]);

		$trendingHashtags = Hashtag::orderByDesc('usage_count')
			->take(8)
			->get(['id', 'tag', 'usage_count']);

		$engagementTrend = EngagementMetric::selectRaw('recorded_for as date, SUM(posts) as posts, SUM(likes) as likes, SUM(comments) as comments, SUM(shares) as shares')
			->where('recorded_for', '>=', $thirtyDaysAgo)
			->groupBy('recorded_for')
			->orderBy('recorded_for')
			->get()
			->map(fn ($metric) => [
				'date' => Carbon::parse($metric->date)->toDateString(),
				'posts' => (int) $metric->posts,
				'likes' => (int) $metric->likes,
				'comments' => (int) $metric->comments,
				'shares' => (int) $metric->shares,
			]);

		$recentAdmins = User::admins()
			->latest()
			->take(5)
			->get(['id', 'name', 'full_name', 'email', 'title', 'created_at'])
			->map(fn (User $admin) => [
				'id' => $admin->id,
				'name' => $admin->display_name,
				'email' => $admin->email,
				'title' => $admin->title,
				'joined_at' => optional($admin->created_at)->toDateString(),
			]);

		return Inertia::render('Admin/Dashboard', [
			'totals' => $totals,
			'engagement' => [
				'summary' => [
					'posts' => (int) ($engagementAggregate->posts ?? 0),
					'comments' => (int) ($engagementAggregate->comments ?? 0),
					'likes' => (int) ($engagementAggregate->likes ?? 0),
					'shares' => (int) ($engagementAggregate->shares ?? 0),
					'impressions' => (int) ($engagementAggregate->impressions ?? 0),
				],
				'trend' => $engagementTrend,
			],
			'topCommunities' => $topCommunities,
			'trendingHashtags' => $trendingHashtags,
			'recentAdmins' => $recentAdmins,
		]);
	}
}
