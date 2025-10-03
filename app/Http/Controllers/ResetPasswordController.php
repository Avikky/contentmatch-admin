<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Facades\LoginTrail;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Arr;

class ResetPasswordController extends Controller
{
    public function showResetForm(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
            'email' => session('email'),
        ]);
    }

    public function reset(ResetPasswordRequest $request)
    {
        try {
            $request->validateEmail();
            
            // Find the user by email
            $user = \App\Models\User::where('email', $request->input('email'))->first();
            
            // Generate new secure password (plain text for email)
            $newPassword = Str::random(12); // Generate a longer secure password
            
            // Update user password and related fields
            $user->password = Hash::make($newPassword);
            $user->unencrypted_password = $newPassword; // Store temporarily if needed
            $user->updated_at = now();
            $user->password_expire_date = now()->addDays(30);

            // unlock account
            $user->is_locked = 0;
            
            $user->save();
            
            // Send email with new password
            Mail::to($user->email)->send(new \App\Mail\PasswordResetMail($user->user_name, $newPassword));
            
            // Log successful password reset
            LoginTrail::logPasswordReset(
                $user, 
                true, 
                'Password successfully reset and sent via email',
                [
                    'email' => $user->email,
                    'password_length' => strlen($newPassword),
                    'expiration_date' => $user->password_expire_date,
                ]
            );

        } catch (\Exception $e) {
            
            \Log::error('Password reset error: ' . $e->getMessage());

            dd($e->getMessage());

            return inertia('Auth/ForgotPassword', [
                'error' => 'An error occurred while resetting your password. Please try again later.',
                'email' => $request->input('email'),
            ]);
        }

       

        return redirect()->route('login')->with('status', 'A new password has been sent to your email address. Please check your inbox and change it after logging in.');
    }
}
