<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\AdminReservationHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isHotelOwner()) {
            return $this->ownerDashboard();
        } else {
            return $this->staffDashboard();
        }
    }

    /**
     * Super Admin Dashboard - System-wide analytics
     */
    private function adminDashboard()
    {
        // Total counts
        $stats = [
            'total_hotels' => Hotel::count(),
            'total_rooms' => Room::count(),
            'total_users' => User::count(),
            'total_guests' => Guest::count(),
            'total_reservations' => Reservation::count(),
            'active_reservations' => Reservation::active()->count(),
            'admin_reserved_rooms' => Room::where('status', 'admin_reserved')->count(),
        ];

        // Breakdown by status
        $roomsByStatus = Room::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $reservationsByStatus = Reservation::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent admin actions
        $recentAdminActions = AdminReservationHistory::with(['reservation', 'admin'])
            ->orderBy('action_at', 'desc')
            ->limit(10)
            ->get();

        // Hotels with most admin reserved rooms
        $hotelsWithAdminRooms = Hotel::withCount(['rooms as admin_reserved_count' => function($query) {
            $query->where('status', 'admin_reserved');
        }])
        ->having('admin_reserved_count', '>', 0)
        ->orderBy('admin_reserved_count', 'desc')
        ->limit(10)
        ->get();

        // All hotels
        $allHotels = Hotel::with(['user', 'rooms'])
            ->withCount('rooms')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // User breakdown
        $usersByType = User::select('user_type', DB::raw('count(*) as count'))
            ->groupBy('user_type')
            ->pluck('count', 'user_type')
            ->toArray();

        return view('dashboard-admin', compact(
            'stats',
            'roomsByStatus',
            'reservationsByStatus',
            'recentAdminActions',
            'hotelsWithAdminRooms',
            'allHotels',
            'usersByType'
        ));
    }

    /**
     * Hotel Owner Dashboard
     */
    private function ownerDashboard()
    {
        $user = Auth::user();

        // Owner's statistics
        $stats = [
            'total_hotels' => $user->hotels()->count(),
            'total_rooms' => Room::whereIn('hotel_id', $user->hotels()->pluck('id'))->count(),
            'total_staff' => User::where('parent_user_id', $user->id)->count(),
            'total_guests' => Guest::where('hotel_owner_id', $user->id)->count(),
            'total_reservations' => Reservation::whereIn('hotel_id', $user->hotels()->pluck('id'))->count(),
            'active_reservations' => Reservation::whereIn('hotel_id', $user->hotels()->pluck('id'))->active()->count(),
        ];

        // Rooms by status
        $roomsByStatus = Room::whereIn('hotel_id', $user->hotels()->pluck('id'))
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent reservations
        $recentReservations = Reservation::whereIn('hotel_id', $user->hotels()->pluck('id'))
            ->with(['room', 'hotel', 'guest'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact('stats', 'roomsByStatus', 'recentReservations'));
    }

    /**
     * Staff Dashboard
     */
    private function staffDashboard()
    {
        $user = Auth::user();
        $accessibleHotels = $user->accessibleHotels()->pluck('hotels.id');

        // Staff's statistics
        $stats = [
            'accessible_hotels' => $accessibleHotels->count(),
            'total_rooms' => Room::whereIn('hotel_id', $accessibleHotels)->count(),
            'active_reservations' => Reservation::whereIn('hotel_id', $accessibleHotels)->active()->count(),
        ];

        // Rooms by status
        $roomsByStatus = Room::whereIn('hotel_id', $accessibleHotels)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent reservations
        $recentReservations = Reservation::whereIn('hotel_id', $accessibleHotels)
            ->with(['room', 'hotel', 'guest'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact('stats', 'roomsByStatus', 'recentReservations'));
    }
}
