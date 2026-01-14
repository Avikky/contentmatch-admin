<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminIsAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = auth('admin')->user();

        // Check if admin is active
        if (! $admin->is_active) {
            auth('admin')->logout();

            return redirect()->route('admin.login')
                ->with('error', 'Your account has been deactivated. Please contact the super admin.');
        }

        return $next($request);
    }
}
