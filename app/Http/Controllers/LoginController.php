<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Loggin;
use App\Facades\LoginTrail;

class LoginController extends Controller
{
    public function showLoginForm(): Response
    {
        return Inertia::render('Auth/Login', [
            'status' => session('status'),
        ]);
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
            
            // Log successful login
            LoginTrail::logLogin(Auth::user(), [
                'login_method' => 'web',
                'remember_me' => $request->boolean('remember'),
            ]);

            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::HOME);
            
        } catch (\Exception $e) {
            // Log failed login attempt
            LoginTrail::logFailedLogin(
                $request->input('username', $request->input('email', 'unknown')),
                'Authentication failed: ' . $e->getMessage()
            );
            
            throw $e;
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Log logout
        if ($user) {
            LoginTrail::logLogout($user, [
                'logout_method' => 'web',
                'session_duration' => session('login_time') ? now()->diffInMinutes(session('login_time')) : null,
            ]);
        }

        return redirect('/');
    }
}
