<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
	public function show(Request $request): Response
	{
		return Inertia::render('Admin/Profile/Index', [
			'user' => $request->user()->only([
				'id',
				'name',
				'full_name',
				'email',
				'user_name',
				'mobile_no',
				'title',
				'bio',
				'avatar_path',
				'role',
				'created_at',
			]),
		]);
	}

	public function update(Request $request): RedirectResponse
	{
		$user = $request->user();

		$validated = $request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
			'user_name' => ['required', 'string', 'max:50', Rule::unique('users', 'user_name')->ignore($user->id)],
			'mobile_no' => ['nullable', 'string', 'max:20'],
			'title' => ['nullable', 'string', 'max:100'],
			'bio' => ['nullable', 'string'],
			'avatar_path' => ['nullable', 'string', 'max:255'],
		]);

		$user->fill([
			'name' => $validated['name'],
			'full_name' => $validated['name'],
			'email' => $validated['email'],
			'user_name' => $validated['user_name'],
			'mobile_no' => $validated['mobile_no'] ?? null,
			'title' => $validated['title'] ?? null,
			'bio' => $validated['bio'] ?? null,
		]);

		if (!empty($validated['avatar_path'])) {
			$user->avatar_path = $validated['avatar_path'];
		}

		$user->save();

		return Redirect::back()->with('success', 'Profile updated successfully.');
	}

	public function updatePassword(Request $request): RedirectResponse
	{
		$user = $request->user();

		$validated = $request->validate([
			'current_password' => ['required'],
			'new_password' => ['required', 'string', 'min:8', 'confirmed'],
		]);

		if (!Hash::check($validated['current_password'], $user->password)) {
			return Redirect::back()->withErrors([
				'current_password' => 'Current password is incorrect.',
			]);
		}

		$user->password = Hash::make($validated['new_password']);
		$user->unencrypted_password = $validated['new_password'];
		$user->save();

		return Redirect::back()->with('success', 'Password updated successfully.');
	}
}
