<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    protected UserManagementService $userManagementService;

    public function __construct(UserManagementService $userManagementService)
    {
        $this->userManagementService = $userManagementService;
    }

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'status', 'is_premium', 'is_verified', 'created_from', 'created_to']);
        $perPage = $request->integer('per_page', 15);

        $users = $this->userManagementService->getUsers($filters, $perPage);
        $statistics = $this->userManagementService->getUserStatistics();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'statistics' => $statistics,
            'filters' => $filters,
        ]);
    }

    public function show(User $user): Response
    {
        $data = $this->userManagementService->getUserDetails($user->id);

        return Inertia::render('Admin/Users/Show', $data);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'user_name' => ['required', 'string', 'max:50', 'unique:users,user_name'],
            'mobile_no' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'user_id' => User::generateIdentifier('USR'),
            'name' => $validated['name'],
            'full_name' => $validated['name'],
            'user_name' => $validated['user_name'],
            'email' => $validated['email'],
            'mobile_no' => $validated['mobile_no'] ?? null,
            'password' => Hash::make($validated['password']),
            'unencrypted_password' => $validated['password'],
            'role' => 'member',
            'is_admin' => false,
        ]);

        return Redirect::back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is_admin, 403, 'Admin accounts are managed elsewhere.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'user_name' => ['required', 'string', 'max:50', 'unique:users,user_name,'.$user->id],
            'mobile_no' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'full_name' => $validated['name'],
            'user_name' => $validated['user_name'],
            'email' => $validated['email'],
            'mobile_no' => $validated['mobile_no'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->unencrypted_password = $validated['password'];
        }

        $user->save();

        return Redirect::back()->with('success', 'User details updated successfully.');
    }

    public function destroy(User $user, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $admin = Auth::guard('admin')->user();
            $this->userManagementService->removeUser(
                $user->id,
                $admin->id,
                $validated['reason'] ?? null
            );

            return Redirect::route('admin.users.index')->with('success', 'User removed successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function ban(User $user, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        try {
            $admin = Auth::guard('admin')->user();
            $this->userManagementService->banUser($user->id, $admin->id, $validated['reason']);

            return Redirect::back()->with('success', 'User banned successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function suspend(User $user, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        try {
            $admin = Auth::guard('admin')->user();
            $this->userManagementService->suspendUser($user->id, $admin->id, $validated['reason']);

            return Redirect::back()->with('success', 'User suspended successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function reactivate(User $user, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $admin = Auth::guard('admin')->user();
            $this->userManagementService->reactivateUser($user->id, $admin->id, $validated['reason'] ?? null);

            return Redirect::back()->with('success', 'User reactivated successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function removeFromCommunity(User $user, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'community_id' => ['required'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $admin = Auth::guard('admin')->user();
            $this->userManagementService->removeUserFromCommunity(
                $user->id,
                $validated['community_id'],
                $admin->id,
                $validated['reason'] ?? null
            );

            return Redirect::back()->with('success', 'User removed from community successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function reports(User $user, Request $request): Response
    {
        $perPage = $request->integer('per_page', 15);
        $reports = $this->userManagementService->getUserReports($user->id, $perPage);

        return Inertia::render('Admin/Users/Reports', [
            'user' => $user->only(['id', 'username', 'full_name', 'email']),
            'reports' => $reports,
        ]);
    }

    public function communities(User $user): Response
    {
        $communities = $this->userManagementService->getUserCommunities($user->id);

        return Inertia::render('Admin/Users/Communities', [
            'user' => $user->only(['id', 'username', 'full_name', 'email']),
            'communities' => $communities,
        ]);
    }

    public function subscriptions(User $user): Response
    {
        $subscriptions = $this->userManagementService->getUserSubscriptions($user->id);

        return Inertia::render('Admin/Users/Subscriptions', [
            'user' => $user->only(['id', 'username', 'full_name', 'email', 'is_premium', 'trial_ends_at']),
            'subscriptions' => $subscriptions,
        ]);
    }

    public function moderationHistory(User $user, Request $request): Response
    {
        $perPage = $request->integer('per_page', 15);
        $history = $this->userManagementService->getModerationHistory($user->id, $perPage);

        return Inertia::render('Admin/Users/ModerationHistory', [
            'user' => $user->only(['id', 'username', 'full_name', 'email']),
            'history' => $history,
        ]);
    }
}
