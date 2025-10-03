<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
	public function index(Request $request): Response
	{
		$search = $request->string('search')->toString();

		$admins = User::admins()
			->when($search, function ($query) use ($search) {
				$query->where(function ($q) use ($search) {
					$q->where('name', 'like', "%{$search}%")
						->orWhere('full_name', 'like', "%{$search}%")
						->orWhere('email', 'like', "%{$search}%")
						->orWhere('user_name', 'like', "%{$search}%");
				});
			})
			->orderByDesc('created_at')
			->paginate(10)
			->withQueryString();

		$eligibleUsers = User::nonAdmins()
			->orderBy('name')
			->get(['id', 'name', 'full_name', 'email', 'user_name']);

		return Inertia::render('Admin/Admins/Index', [
			'admins' => $admins,
			'filters' => [
				'search' => $search,
			],
			'eligibleUsers' => $eligibleUsers->map(fn (User $user) => [
				'id' => $user->id,
				'name' => $user->display_name,
				'email' => $user->email,
				'user_name' => $user->user_name,
			]),
		]);
	}

	public function store(Request $request): RedirectResponse
	{
		if ($request->filled('user_id')) {
			$validated = $request->validate([
				'user_id' => ['required', 'exists:users,id'],
				'role' => ['nullable', 'string', 'max:50'],
				'title' => ['nullable', 'string', 'max:100'],
			]);

			$user = User::findOrFail($validated['user_id']);
			$user->is_admin = true;
			$user->role = $validated['role'] ?? 'admin';
			$user->title = $validated['title'] ?? $user->title;
			$user->save();

			return Redirect::back()->with('success', "{$user->display_name} is now an administrator.");
		}

		$validated = $request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
			'user_name' => ['required', 'string', 'max:50', 'unique:users,user_name'],
			'password' => ['required', 'string', 'min:8', 'confirmed'],
			'mobile_no' => ['nullable', 'string', 'max:20'],
			'role' => ['nullable', 'string', 'max:50'],
			'title' => ['nullable', 'string', 'max:100'],
		]);

		User::create([
			'user_id' => User::generateIdentifier('ADM'),
			'name' => $validated['name'],
			'full_name' => $validated['name'],
			'user_name' => $validated['user_name'],
			'email' => $validated['email'],
			'mobile_no' => $validated['mobile_no'] ?? null,
			'password' => Hash::make($validated['password']),
			'unencrypted_password' => $validated['password'],
			'role' => $validated['role'] ?? 'admin',
			'title' => $validated['title'] ?? null,
			'is_admin' => true,
		]);

		return Redirect::back()->with('success', 'Administrator account created successfully.');
	}

	public function destroy(Request $request, User $user): RedirectResponse
	{
		$requestingUser = $request->user();

		abort_if(!$user->is_admin, 404);
		abort_if($user->id === $requestingUser->id, 403, 'You cannot remove your own admin privileges.');
		abort_if(User::admins()->count() <= 1, 403, 'At least one administrator is required.');

		$user->is_admin = false;
		$user->role = 'member';
		$user->save();

		return Redirect::back()->with('success', "{$user->display_name} is no longer an administrator.");
	}
}
