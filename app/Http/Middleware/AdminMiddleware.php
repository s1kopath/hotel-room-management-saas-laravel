<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Checks if user is authenticated and has active status.
     * Allows super_admin, hotel_owner, and staff to access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Check user status
        if ($user->status === 'suspended') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended. Please contact administrator.');
        }

        if ($user->status === 'deleted') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been deleted. Please contact administrator.');
        }

        // Allow all authenticated users with active status
        return $next($request);
    }
}
