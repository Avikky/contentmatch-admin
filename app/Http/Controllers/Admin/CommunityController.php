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
            ->with(['owner:id,full_name,username', 'members:id', 'category:id,name'])
            ->withCount('members')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $owners = User::where('status', 'active')
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'username', 'email']);

        $availableMembers = User::where('status', 'active')
            ->orderBy('full_name')
            ->take(50)
            ->get(['id', 'full_name', 'username', 'email']);

        return Inertia::render('Admin/Communities/Index', [
            'communities' => $communities,
            'filters' => [
                'search' => $search,
            ],
            'owners' => $owners->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->full_name ?? $user->username,
                'username' => $user->username,
                'email' => $user->email,
            ]),
            'members' => $availableMembers->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->full_name ?? $user->username,
                'username' => $user->username,
                'email' => $user->email,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        $community = Community::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name'].'-'.Str::random(4)),
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'] ?? 'public',
            'status' => $validated['status'],
            'owner_id' => $validated['owner_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'banner_url' => $validated['banner_url'] ?? null,
        ]);

        if (! empty($validated['member_ids'])) {
            $community->members()->sync($validated['member_ids']);
        }

        if (! empty($validated['hashtags'])) {
            $this->syncHashtags($community, $validated['hashtags']);
        }

        return Redirect::back()->with('success', 'Community created successfully.');
    }

    public function update(Request $request, Community $community): RedirectResponse
    {
        $validated = $this->validatePayload($request, $community);

        $community->fill([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'] ?? $community->type,
            'status' => $validated['status'],
            'owner_id' => $validated['owner_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
        ]);

        if (! empty($validated['slug'])) {
            $community->slug = $validated['slug'];
        }

        if (! empty($validated['banner_url'])) {
            $community->banner_url = $validated['banner_url'];
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

    public function show(Community $community): Response
    {
        $community->load([
            'owner:id,full_name,username,email,profile_image_url',
            'members' => function ($query) {
                $query->select('users.id', 'users.full_name', 'users.username', 'users.email', 'users.profile_image_url', 'users.status')
                    ->withPivot('role', 'status', 'notification_enabled', 'last_activity_at', 'created_at');
            },
            'admins:id,full_name,username,email,profile_image_url',
            'moderators:id,full_name,username,email,profile_image_url',
            'communityDiscord',
            'category:id,name',
        ]);

        return Inertia::render('Admin/Communities/Show', [
            'community' => [
                'id' => $community->id,
                'name' => $community->name,
                'slug' => $community->slug,
                'banner_url' => $community->banner_url,
                'category' => $community->category ? [
                    'id' => $community->category->id,
                    'name' => $community->category->name,
                ] : null,
                'description' => $community->description,
                'type' => $community->type,
                'status' => $community->status,
                'created_at' => $community->created_at,
                'updated_at' => $community->updated_at,
                'owner' => $community->owner ? [
                    'id' => $community->owner->id,
                    'name' => $community->owner->full_name ?? $community->owner->username,
                    'username' => $community->owner->username,
                    'email' => $community->owner->email,
                    'profile_image_url' => $community->owner->profile_image_url,
                ] : null,
                'members_count' => $community->members->count(),
                'members' => $community->members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->full_name ?? $member->username,
                        'username' => $member->username,
                        'email' => $member->email,
                        'profile_image_url' => $member->profile_image_url,
                        'status' => $member->status,
                        'role' => $member->pivot->role,
                        'member_status' => $member->pivot->status,
                        'joined_at' => $member->pivot->created_at,
                        'last_activity_at' => $member->pivot->last_activity_at,
                    ];
                }),
                'admins' => $community->admins->map(function ($admin) {
                    return [
                        'id' => $admin->id,
                        'name' => $admin->full_name ?? $admin->username,
                        'username' => $admin->username,
                        'email' => $admin->email,
                        'profile_image_url' => $admin->profile_image_url,
                    ];
                }),
                'moderators' => $community->moderators->map(function ($moderator) {
                    return [
                        'id' => $moderator->id,
                        'name' => $moderator->full_name ?? $moderator->username,
                        'username' => $moderator->username,
                        'email' => $moderator->email,
                        'profile_image_url' => $moderator->profile_image_url,
                    ];
                }),
                'discord_server' => $community->communityDiscord ? [
                    'id' => $community->communityDiscord->id,
                    'server_id' => $community->communityDiscord->server_id,
                    'server_name' => $community->communityDiscord->server_name,
                    'invite_code' => $community->communityDiscord->invite_code,
                    'is_active' => $community->communityDiscord->is_active,
                    'created_at' => $community->communityDiscord->created_at,
                ] : null,
            ],
        ]);
    }

    public function destroy(Community $community): RedirectResponse
    {
        // Log the deletion for audit trail
        activity()
            ->performedOn($community)
            ->withProperties([
                'community_id' => $community->id,
                'name' => $community->name,
                'members_count' => $community->members()->count(),
                'content_count' => $community->content()->count(),
            ])
            ->log('community_deleted');

        // Detach all members from the community
        $community->members()->detach();

        // Soft delete all content posted in this community
        $community->content()->delete();

        // Delete community messages if they exist
        if (method_exists($community, 'messages')) {
            $community->messages()->delete();
        }

        // Delete engagement scores
        $community->engagementScores()->delete();

        // Delete community rules
        $community->rules()->delete();

        // Delete community settings
        if ($community->settings) {
            $community->settings()->delete();
        }

        // Delete Discord integration
        if ($community->communityDiscord) {
            $community->communityDiscord()->delete();
        }

        // Detach all morph relationships
        $community->hashtags()->detach();
        $community->purposes()->detach();
        $community->platforms()->detach();

        // Finally, soft delete the community itself
        $community->delete();

        return Redirect::route('admin.communities.index')->with('success', 'Community and all related data have been removed successfully.');
    }

    public function banMember(Community $community, User $user): RedirectResponse
    {
        $member = $community->members()->where('user_id', $user->id)->first();

        if (! $member) {
            return Redirect::back()->with('error', 'User is not a member of this community.');
        }

        $community->members()->updateExistingPivot($user->id, [
            'status' => 'banned',
        ]);

        return Redirect::back()->with('success', 'Member has been banned from the community.');
    }

    public function removeMember(Community $community, User $user): RedirectResponse
    {
        $member = $community->members()->where('user_id', $user->id)->first();

        if (! $member) {
            return Redirect::back()->with('error', 'User is not a member of this community.');
        }

        $community->members()->detach($user->id);

        return Redirect::back()->with('success', 'Member has been removed from the community.');
    }

    public function updateMemberRole(Community $community, User $user, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,moderator,member'],
        ]);

        $member = $community->members()->where('user_id', $user->id)->first();

        if (! $member) {
            return Redirect::back()->with('error', 'User is not a member of this community.');
        }

        $community->members()->updateExistingPivot($user->id, [
            'role' => $validated['role'],
        ]);

        return Redirect::back()->with('success', 'Member role updated successfully.');
    }

    public function toggleMemberStatus(Community $community, User $user, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,banned'],
        ]);

        $member = $community->members()->where('user_id', $user->id)->first();

        if (! $member) {
            return Redirect::back()->with('error', 'User is not a member of this community.');
        }

        $community->members()->updateExistingPivot($user->id, [
            'status' => $validated['status'],
        ]);

        $message = $validated['status'] === 'banned'
            ? 'Member has been banned from the community.'
            : 'Member has been reinstated in the community.';

        return Redirect::back()->with('success', $message);
    }

    protected function validatePayload(Request $request, ?Community $community = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:communities,slug,'.optional($community)->id],
            'banner_url' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:public,private'],
            'status' => ['required', 'in:active,archived,suspended'],
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
                return Hashtag::firstOrCreate(['name' => $tag])->id;
            })
            ->all();

        $community->hashtags()->sync($hashtagIds);
    }
}
