<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Checks if user has the required permission.
     * Usage: ->middleware('permission:hotels.create')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Super admin has all permissions
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has the required permission
        if (!$user->hasPermission($permission)) {
            abort(403, "You don't have permission to perform this action. Required: {$permission}");
        }

        return $next($request);
    }
}

