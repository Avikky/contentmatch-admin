<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Hashtag;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CommunityController extends Controller
{
	public function index(Request $request): Response
	{
		$search = $request->string('search')->toString();

		$communities = Community::query()
			->with(['owner:id,name,full_name', 'members:id'])
			->withCount('members')
			->when($search, function ($query) use ($search) {
				$query->where(function ($q) use ($search) {
					$q->where('name', 'like', "%{$search}%")
						->orWhere('category', 'like', "%{$search}%")
						->orWhere('description', 'like', "%{$search}%");
				});
			})
			->orderByDesc('created_at')
			->paginate(10)
			->withQueryString();

		$owners = User::admins()
			->orderBy('name')
			->get(['id', 'name', 'full_name', 'title']);

		$availableMembers = User::orderBy('name')
			->take(50)
			->get(['id', 'name', 'full_name', 'email']);

		return Inertia::render('Admin/Communities/Index', [
			'communities' => $communities,
			'filters' => [
				'search' => $search,
			],
			'owners' => $owners->map(fn (User $user) => [
				'id' => $user->id,
				'name' => $user->display_name,
				'title' => $user->title,
			]),
			'members' => $availableMembers->map(fn (User $user) => [
				'id' => $user->id,
				'name' => $user->display_name,
				'email' => $user->email,
			]),
		]);
	}

	public function store(Request $request): RedirectResponse
	{
		$validated = $this->validatePayload($request);

		$community = Community::create([
			'name' => $validated['name'],
			'slug' => $validated['slug'] ?? Str::slug($validated['name'] . '-' . Str::random(4)),
			'avatar_path' => $validated['avatar_path'] ?? null,
			'category' => $validated['category'] ?? null,
			'description' => $validated['description'] ?? null,
			'visibility' => $validated['visibility'],
			'status' => $validated['status'],
			'owner_id' => $validated['owner_id'] ?? null,
		]);

		if (!empty($validated['member_ids'])) {
			$community->members()->sync($validated['member_ids']);
		}

		if (!empty($validated['hashtags'])) {
			$this->syncHashtags($community, $validated['hashtags']);
		}

		return Redirect::back()->with('success', 'Community created successfully.');
	}

	public function update(Request $request, Community $community): RedirectResponse
	{
		$validated = $this->validatePayload($request, $community);

		$community->fill([
			'name' => $validated['name'],
			'category' => $validated['category'] ?? null,
			'description' => $validated['description'] ?? null,
			'visibility' => $validated['visibility'],
			'status' => $validated['status'],
			'owner_id' => $validated['owner_id'] ?? null,
		]);

		if (!empty($validated['slug'])) {
			$community->slug = $validated['slug'];
		}

		if (!empty($validated['avatar_path'])) {
			$community->avatar_path = $validated['avatar_path'];
		}

		$community->save();

		if (array_key_exists('member_ids', $validated)) {
			$community->members()->sync($validated['member_ids'] ?? []);
		}

		if (array_key_exists('hashtags', $validated)) {
			$this->syncHashtags($community, $validated['hashtags'] ?? []);
		}

		return Redirect::back()->with('success', 'Community updated successfully.');
	}

	public function destroy(Community $community): RedirectResponse
	{
		$community->members()->detach();
		$community->hashtags()->detach();
		$community->engagementMetrics()->delete();
		$community->delete();

		return Redirect::back()->with('success', 'Community removed successfully.');
	}

	protected function validatePayload(Request $request, ?Community $community = null): array
	{
		return $request->validate([
			'name' => ['required', 'string', 'max:255'],
			'slug' => ['nullable', 'string', 'max:255', 'unique:communities,slug,' . optional($community)->id],
			'avatar_path' => ['nullable', 'string', 'max:255'],
			'category' => ['nullable', 'string', 'max:120'],
			'description' => ['nullable', 'string'],
			'visibility' => ['required', 'in:public,private'],
			'status' => ['required', 'in:active,archived'],
			'owner_id' => ['nullable', 'exists:users,id'],
			'member_ids' => ['nullable', 'array'],
			'member_ids.*' => ['exists:users,id'],
			'hashtags' => ['nullable', 'array'],
			'hashtags.*' => ['string', 'max:100'],
		]);
	}

	protected function syncHashtags(Community $community, array $hashtags): void
	{
		$hashtagIds = collect($hashtags)
			->filter()
			->map(fn ($tag) => ltrim((string) $tag, '#'))
			->map(fn ($tag) => Str::lower(trim($tag)))
			->unique()
			->map(function ($tag) {
				return Hashtag::firstOrCreate(['tag' => $tag])->id;
			})
			->all();

		$community->hashtags()->sync($hashtagIds);
	}
}
