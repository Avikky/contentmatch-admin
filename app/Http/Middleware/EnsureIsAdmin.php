<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth('admin')->user();

        if (! $admin || ! in_array($admin->role, ['admin', 'superadmin'])) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}
