<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        $admin = $request->user('admin');

        return Inertia::render('Admin/Profile', [
            'admin' => [
                'name' => $admin?->display_name ?? $admin?->full_name,
                'email' => $admin?->email,
                'role' => $admin?->role,
                'phone' => $admin?->phone,
                'username' => $admin?->user_name,
                'last_login_at' => optional($admin?->last_login_at)?->format('M d, Y h:i A'),
                'joined_at' => optional($admin?->created_at)?->format('M d, Y'),
                'profile_image_url' => $admin?->profile_image_url,
                'permissions' => $admin?->permissions ?? [],
            ],
        ]);
    }

    public function update(Request $request)
    {
        $admin = $request->user('admin');

        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'user_name' => ['required', 'string', 'max:255', 'unique:admins,user_name,'.$admin->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,'.$admin->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $admin->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $admin = $request->user('admin');

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Check if current password is correct
        if (! Hash::check($validated['current_password'], $admin->password)) {
            return back()->withErrors([
                'current_password' => 'The provided password does not match your current password.',
            ]);
        }

        $admin->update([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    public function uploadImage(Request $request)
    {
        $admin = $request->user('admin');

        $validated = $request->validate([
            'profile_image' => ['required', 'image', 'max:2048'], // 2MB max
        ]);

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($admin->profile_image_url) {
                try {
                    // Extract the path from the URL
                    $oldPath = parse_url($admin->profile_image_url, PHP_URL_PATH);
                    $oldPath = ltrim($oldPath, '/');
                    Storage::disk('digitalocean')->delete($oldPath);
                } catch (\Exception $e) {
                    // Ignore errors when deleting old image
                }
            }

            // Upload new image
            $profilePath = $request->file('profile_image')->store('profile-images', 'digitalocean');

            // Build the full URL
            $endpoint = config('filesystems.disks.digitalocean.endpoint');
            $bucket = config('filesystems.disks.digitalocean.bucket');
            $admin->profile_image_url = "{$endpoint}/{$bucket}/{$profilePath}";
            $admin->save();
        }

        return back()->with('success', 'Profile image updated successfully!');
    }
}
