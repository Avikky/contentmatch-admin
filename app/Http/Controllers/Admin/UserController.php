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

class UserController extends Controller
{
	public function index(Request $request): Response
	{
		$search = $request->string('search')->toString();

		$users = User::nonAdmins()
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

		return Inertia::render('Admin/Users/Index', [
			'users' => $users,
			'filters' => [
				'search' => $search,
			],
		]);
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
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
			'user_name' => ['required', 'string', 'max:50', 'unique:users,user_name,' . $user->id],
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

		if (!empty($validated['password'])) {
			$user->password = Hash::make($validated['password']);
			$user->unencrypted_password = $validated['password'];
		}

		$user->save();

		return Redirect::back()->with('success', 'User details updated successfully.');
	}

	public function destroy(User $user): RedirectResponse
	{
		abort_if($user->is_admin, 403, 'Cannot delete an administrator from this section.');

		$user->communities()->detach();
		$user->delete();

		return Redirect::back()->with('success', 'User deleted successfully.');
	}
}
