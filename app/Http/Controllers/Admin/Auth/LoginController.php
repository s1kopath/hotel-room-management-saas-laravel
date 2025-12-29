<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'], // Can be username or email
            'password' => ['required', 'string'],
        ]);

        // Determine if login is email or username
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Find user by email or username
        $user = User::where($loginField, $request->login)->first();

        // Check if user exists
        if (!$user) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials do not match our records.'],
            ]);
        }

        // Check user status
        if ($user->status === 'suspended') {
            throw ValidationException::withMessages([
                'login' => ['Your account has been suspended. Please contact administrator.'],
            ]);
        }

        if ($user->status === 'deleted') {
            throw ValidationException::withMessages([
                'login' => ['Your account has been deleted. Please contact administrator.'],
            ]);
        }

        // Attempt authentication
        if (Auth::attempt([$loginField => $request->login, 'password' => $request->password])) {
            // Update last login timestamp
            $user->update(['last_login' => now()]);

            $request->session()->regenerate();

            // Log login activity
            app(ActivityLogService::class)->logLogin($request->login);

            return to_route('dashboard')->with('success', 'You are logged in!');
        }

        throw ValidationException::withMessages([
            'login' => ['The provided credentials do not match our records.'],
        ]);
    }

    public function logout(Request $request)
    {
        // Log logout activity before logging out
        if (Auth::check()) {
            app(ActivityLogService::class)->logLogout();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('login')->with('success', 'You are logged out!');
    }
}
