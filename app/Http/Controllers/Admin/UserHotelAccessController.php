<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Hotel;
use App\Models\UserHotelAccess;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserHotelAccessController extends Controller
{
    /**
     * Display hotel access for a staff member
     */
    public function index(string $userId)
    {
        $user = Auth::user();
        $staff = User::findOrFail($userId);

        // Only hotel owners can manage their staff's hotel access
        if (!$user->isHotelOwner() || $staff->parent_user_id !== $user->id) {
            if (!$user->isSuperAdmin()) {
                abort(403, 'You do not have permission to manage this user\'s hotel access.');
            }
        }

        // Get owner's hotels
        $ownerHotels = $user->isSuperAdmin()
            ? Hotel::where('status', 'active')->orderBy('name')->get()
            : $user->hotels()->where('status', 'active')->orderBy('name')->get();

        // Get staff's current hotel access
        $accessibleHotels = $staff->accessibleHotels()->pluck('hotels.id')->toArray();

        return view('users.hotel-access', compact('staff', 'ownerHotels', 'accessibleHotels'));
    }

    /**
     * Update hotel access for a staff member
     */
    public function update(Request $request, string $userId)
    {
        $user = Auth::user();
        $staff = User::findOrFail($userId);

        // Only hotel owners can manage their staff's hotel access
        if (!$user->isHotelOwner() || $staff->parent_user_id !== $user->id) {
            if (!$user->isSuperAdmin()) {
                abort(403, 'You do not have permission to manage this user\'s hotel access.');
            }
        }

        // Ensure user is staff
        if (!$staff->isStaff()) {
            return back()->with('error', 'Can only manage hotel access for staff members.');
        }

        $request->validate([
            'hotels' => 'nullable|array',
            'hotels.*' => 'exists:hotels,id',
        ]);

        DB::beginTransaction();
        try {
            // Remove all existing access
            UserHotelAccess::where('user_id', $staff->id)->delete();

            // Add new access
            if ($request->has('hotels') && is_array($request->hotels)) {
                foreach ($request->hotels as $hotelId) {
                    // Verify hotel belongs to the owner (unless super admin)
                    if (!$user->isSuperAdmin()) {
                        $hotel = $user->hotels()->find($hotelId);
                        if (!$hotel) {
                            continue; // Skip hotels that don't belong to the owner
                        }
                    }

                    UserHotelAccess::create([
                        'user_id' => $staff->id,
                        'hotel_id' => $hotelId,
                        'assigned_by' => Auth::id(),
                        'is_active' => true,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Hotel access updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating hotel access: ' . $e->getMessage());
        }
    }

    /**
     * Toggle hotel access for a staff member (AJAX)
     */
    public function toggle(Request $request, string $userId, string $hotelId)
    {
        $user = Auth::user();
        $staff = User::findOrFail($userId);
        $hotel = Hotel::findOrFail($hotelId);

        // Only hotel owners can manage their staff's hotel access
        if (!$user->isHotelOwner() || $staff->parent_user_id !== $user->id) {
            if (!$user->isSuperAdmin()) {
                abort(403, 'You do not have permission to manage this user\'s hotel access.');
            }
        }

        // Verify hotel belongs to the owner (unless super admin)
        if (!$user->isSuperAdmin()) {
            if ($hotel->user_id !== $user->id) {
                abort(403, 'You do not have access to this hotel.');
            }
        }

        $access = UserHotelAccess::where('user_id', $staff->id)
            ->where('hotel_id', $hotelId)
            ->first();

        if ($access) {
            // Remove access
            $access->delete();
            return response()->json(['success' => true, 'action' => 'removed']);
        } else {
            // Grant access
            UserHotelAccess::create([
                'user_id' => $staff->id,
                'hotel_id' => $hotelId,
                'assigned_by' => Auth::id(),
                'is_active' => true,
            ]);
            return response()->json(['success' => true, 'action' => 'granted']);
        }
    }
}
