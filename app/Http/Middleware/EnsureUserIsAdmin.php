<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = $request->user('admin');

        if (! $admin) {
            if ($request->expectsJson()) {
                abort(403, 'This action is authorized for ContentMatch administrators only.');
            }

            return redirect()->route('admin.login')->with('error', 'You need administrator privileges to access this area.');
        }

        return $next($request);
    }
}
