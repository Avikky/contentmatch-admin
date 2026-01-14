<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminOtpMail;
use App\Models\Admin;
use App\Models\AdminOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLogin()
    {
        if (auth('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('Auth/AdminLogin');
    }

    /**
     * Handle admin login (Step 1: Email & Password).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Rate limiting
        $key = 'admin-login:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => ["Too many login attempts. Please try again in {$seconds} seconds."],
            ]);
        }

        // Find admin
        $admin = Admin::where('email', $request->email)->first();

        // Validate credentials
        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if admin is active
        if (! $admin->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact the super admin.'],
            ]);
        }

        // Generate OTP
        $otp = AdminOtp::generateOtp();

        // Store OTP
        AdminOtp::createForAdmin($admin->id, $otp);

        // Send OTP email
        try {
            Mail::to($admin->email)->send(new AdminOtpMail($admin, $otp));
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email: '.$e->getMessage());

            throw ValidationException::withMessages([
                'email' => ['Failed to send OTP. Please try again.'],
            ]);
        }

        // Store admin ID in session for OTP verification
        $request->session()->put('admin_id_for_otp', $admin->id);
        $request->session()->put('otp_sent_at', now()->timestamp);

        // Clear rate limiter on successful credential validation
        RateLimiter::clear($key);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email. Please verify to continue.',
            'email' => $admin->email,
        ]);
    }

    /**
     * Show OTP verification page.
     */
    public function showOtpVerify()
    {
        if (! session('admin_id_for_otp')) {
            return redirect()->route('admin.login');
        }

        $admin = Admin::find(session('admin_id_for_otp'));

        if (! $admin) {
            session()->forget('admin_id_for_otp');

            return redirect()->route('admin.login');
        }

        return Inertia::render('Auth/OtpVerify', [
            'email' => $admin->email,
        ]);
    }

    /**
     * Verify OTP (Step 2).
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        // Rate limiting
        $key = 'otp-verify:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'otp' => ["Too many verification attempts. Please try again in {$seconds} seconds."],
            ]);
        }

        // Get admin ID from session
        $adminId = session('admin_id_for_otp');

        if (! $adminId) {
            throw ValidationException::withMessages([
                'otp' => ['Session expired. Please login again.'],
            ]);
        }

        // Find valid OTP
        $otpRecord = AdminOtp::validForAdmin($adminId)->first();

        if (! $otpRecord) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'otp' => ['OTP has expired. Please request a new one.'],
            ]);
        }

        // Verify OTP
        if (! $otpRecord->verify($request->otp)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP. Please try again.'],
            ]);
        }

        // Get admin
        $admin = Admin::find($adminId);

        // Login admin
        auth('admin')->login($admin);

        // Regenerate session
        $request->session()->regenerate();

        // Update last login
        $admin->updateLastLogin();

        // Clear session data
        session()->forget(['admin_id_for_otp', 'otp_sent_at']);

        // Clear rate limiter
        RateLimiter::clear($key);

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'redirect' => route('admin.dashboard'),
        ]);
    }

    /**
     * Resend OTP.
     */
    public function resendOtp(Request $request)
    {
        // Rate limiting
        $key = 'otp-resend:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'otp' => ["Too many resend attempts. Please try again in {$seconds} seconds."],
            ]);
        }

        $adminId = session('admin_id_for_otp');

        if (! $adminId) {
            throw ValidationException::withMessages([
                'otp' => ['Session expired. Please login again.'],
            ]);
        }

        $admin = Admin::find($adminId);

        if (! $admin) {
            throw ValidationException::withMessages([
                'otp' => ['Admin not found. Please login again.'],
            ]);
        }

        // Generate new OTP
        $otp = AdminOtp::generateOtp();

        // Store OTP
        AdminOtp::createForAdmin($admin->id, $otp);

        // Send OTP email
        try {
            Mail::to($admin->email)->send(new AdminOtpMail($admin, $otp));
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email: '.$e->getMessage());

            throw ValidationException::withMessages([
                'otp' => ['Failed to send OTP. Please try again.'],
            ]);
        }

        RateLimiter::hit($key, 60);

        return response()->json([
            'success' => true,
            'message' => 'New OTP sent to your email.',
        ]);
    }

    /**
     * Logout admin.
     */
    public function logout(Request $request)
    {
        auth('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
