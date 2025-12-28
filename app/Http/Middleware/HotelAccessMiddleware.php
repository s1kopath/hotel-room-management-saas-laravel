<?php

namespace App\Http\Middleware;

use App\Models\Hotel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HotelAccessMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Checks if user has access to the hotel.
     * Expects hotel_id in route parameter or request.
     * Usage: ->middleware('hotel.access')
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Super admin has access to all hotels
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Get hotel_id from route parameter or request
        $hotelId = $request->route('hotel') ?? $request->route('hotel_id') ?? $request->input('hotel_id');

        if (!$hotelId) {
            abort(400, 'Hotel ID is required.');
        }

        // Hotel owners have access to their own hotels
        if ($user->isHotelOwner()) {
            $hotel = Hotel::find($hotelId);
            if ($hotel && $hotel->user_id === $user->id) {
                return $next($request);
            }
        }

        // Staff must have explicit access via user_hotel_access
        if ($user->isStaff()) {
            $hasAccess = $user->accessibleHotels()->where('hotels.id', $hotelId)->exists();
            if ($hasAccess) {
                return $next($request);
            }
        }

        abort(403, 'You do not have access to this hotel.');
    }
}

