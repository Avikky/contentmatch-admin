<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminIsSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth('admin')->user();

        if (! $admin || ! $admin->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super admin access required.');
        }

        return $next($request);
    }
}
