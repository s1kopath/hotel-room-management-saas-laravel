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
     * Checks if user has the required permission(s).
     * Usage: ->middleware('permission:hotels.create')
     * Usage: ->middleware('permission:hotels.view-own,hotels.view-all') - Multiple permissions (OR logic)
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Super admin has all permissions
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // If single permission string contains comma, split it
        if (count($permissions) === 1 && str_contains($permissions[0], ',')) {
            $permissions = array_map('trim', explode(',', $permissions[0]));
        }

        // Check if user has any of the required permissions (OR logic)
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if ($user->hasPermission(trim($permission))) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            $permissionsList = implode(', ', $permissions);
            abort(403, "You don't have permission to perform this action. Required: {$permissionsList}");
        }

        return $next($request);
    }
}

