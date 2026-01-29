<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || $user->is_admin || !$user->department_id) {
            abort(403, 'Unauthorized.');
        }
        return $next($request);
    }
} 