<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function showProfile(Request $request): Response
    {
        return Inertia::render('Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function updatePersonalInfo(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20'
        ]);

        $user->update($validatedData);

        return Redirect::route('profile')->with('status', 'Profile updated successfully.');
    }


    public function updateCompanyInfo(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            // Add other company-related fields as necessary
        ]);

        $user->update($validatedData);

        return Redirect::route('profile')->with('status', 'Company information updated successfully.');
    }


    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return Redirect::back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($validatedData['new_password']);
        $user->save();

        return Redirect::route('profile')->with('status', 'Password changed successfully.');
    }

    
}
