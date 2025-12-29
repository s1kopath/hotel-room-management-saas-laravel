<?php

namespace App\Http\Controllers\Admin;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Hotel;
use App\Models\Guest;
use App\DataTables\ReservationsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ReservationsDataTable $dataTable)
    {
        return $dataTable->render('reservations.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $hotelId = $request->get('hotel_id');
        $roomId = $request->get('room_id');
        $guestId = $request->get('guest_id');
        
        $user = Auth::user();
        
        // Get accessible hotels
        if ($user->isSuperAdmin()) {
            $hotels = Hotel::where('status', 'active')->orderBy('name')->get();
        } elseif ($user->isHotelOwner()) {
            $hotels = $user->hotels()->where('status', 'active')->orderBy('name')->get();
        } elseif ($user->isStaff() && $user->parent_user_id) {
            $hotels = Hotel::where('user_id', $user->parent_user_id)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        } else {
            $hotels = collect();
        }

        // Get rooms for selected hotel
        $rooms = collect();
        if ($hotelId) {
            $hotel = Hotel::find($hotelId);
            if ($hotel && $user->hasAccessToHotel($hotelId)) {
                $rooms = $hotel->rooms()->orderBy('room_number')->get();
            }
        }

        // Get guests
        if ($user->isSuperAdmin()) {
            $guests = Guest::orderBy('first_name')->orderBy('last_name')->get();
        } elseif ($user->isHotelOwner()) {
            $guests = Guest::where('hotel_owner_id', $user->id)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();
        } elseif ($user->isStaff() && $user->parent_user_id) {
            $guests = Guest::where('hotel_owner_id', $user->parent_user_id)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();
        } else {
            $guests = collect();
        }

        return view('reservations.components.create', compact('hotels', 'rooms', 'guests', 'hotelId', 'roomId', 'guestId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check permission
        if (!Auth::user()->hasPermission('reservations.create')) {
            abort(403, 'You do not have permission to create reservations.');
        }

        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'total_amount' => 'nullable|numeric|min:0',
            'special_requests' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Check hotel access
        if (!$user->hasAccessToHotel($request->hotel_id)) {
            abort(403, 'You do not have access to this hotel.');
        }

        // Verify room belongs to hotel
        $room = Room::findOrFail($request->room_id);
        if ($room->hotel_id != $request->hotel_id) {
            return back()->withInput()->withErrors(['room_id' => 'Selected room does not belong to the selected hotel.']);
        }

        // Check if room is available for the dates
        $conflictingReservation = Reservation::where('room_id', $request->room_id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($request) {
                $query->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                      ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date])
                      ->orWhere(function($q) use ($request) {
                          $q->where('check_in_date', '<=', $request->check_in_date)
                            ->where('check_out_date', '>=', $request->check_out_date);
                      });
            })
            ->first();

        if ($conflictingReservation) {
            return back()->withInput()->withErrors(['check_in_date' => 'Room is not available for the selected dates.']);
        }

        DB::beginTransaction();
        try {
            // Generate reservation number
            $reservationNumber = $this->generateReservationNumber();

            $reservation = Reservation::create([
                'reservation_number' => $reservationNumber,
                'room_id' => $request->room_id,
                'hotel_id' => $request->hotel_id,
                'guest_id' => $request->guest_id,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'number_of_guests' => $request->number_of_guests,
                'reservation_type' => 'regular',
                'status' => 'pending',
                'payment_status' => 'pending',
                'total_amount' => $request->total_amount ?? 0.00,
                'paid_amount' => 0.00,
                'special_requests' => $request->special_requests,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            // Update room status to reserved
            $room->update([
                'status' => 'reserved',
                'last_status_change' => now(),
                'status_updated_by' => Auth::id(),
            ]);

            // Record status change in history
            \App\Models\RoomStatusHistory::create([
                'room_id' => $room->id,
                'previous_status' => 'vacant',
                'new_status' => 'reserved',
                'changed_by' => Auth::id(),
                'changed_at' => now(),
                'notes' => 'Room reserved for reservation #' . $reservationNumber,
            ]);

            DB::commit();

            return to_route('reservations.show', $reservation->id)
                ->with('success', 'Reservation created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating reservation: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reservation = Reservation::with(['room', 'hotel', 'guest', 'createdBy', 'cancelledBy'])
            ->findOrFail($id);
        
        // Check access
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            if (!$user->hasAccessToHotel($reservation->hotel_id)) {
                abort(403, 'You do not have access to this reservation.');
            }
        }

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        // Check permission and access
        if (!Auth::user()->hasPermission('reservations.edit')) {
            abort(403, 'You do not have permission to edit reservations.');
        }

        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            if (!$user->hasAccessToHotel($reservation->hotel_id)) {
                abort(403, 'You do not have access to this reservation.');
            }
        }

        // Prevent editing admin override reservations
        if ($reservation->isAdminOverride() && !$user->isSuperAdmin()) {
            abort(403, 'Cannot edit admin override reservations.');
        }

        $user = Auth::user();
        
        // Get accessible hotels
        if ($user->isSuperAdmin()) {
            $hotels = Hotel::where('status', 'active')->orderBy('name')->get();
        } elseif ($user->isHotelOwner()) {
            $hotels = $user->hotels()->where('status', 'active')->orderBy('name')->get();
        } elseif ($user->isStaff() && $user->parent_user_id) {
            $hotels = Hotel::where('user_id', $user->parent_user_id)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        } else {
            $hotels = collect();
        }

        $rooms = collect();
        if ($reservation->hotel_id) {
            $hotel = Hotel::find($reservation->hotel_id);
            if ($hotel) {
                $rooms = $hotel->rooms()->orderBy('room_number')->get();
            }
        }

        // Get guests
        if ($user->isSuperAdmin()) {
            $guests = Guest::orderBy('first_name')->orderBy('last_name')->get();
        } elseif ($user->isHotelOwner()) {
            $guests = Guest::where('hotel_owner_id', $user->id)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();
        } elseif ($user->isStaff() && $user->parent_user_id) {
            $guests = Guest::where('hotel_owner_id', $user->parent_user_id)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();
        } else {
            $guests = collect();
        }

        return view('reservations.components.edit', compact('reservation', 'hotels', 'rooms', 'guests'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);

        // Check permission and access
        if (!Auth::user()->hasPermission('reservations.edit')) {
            abort(403, 'You do not have permission to edit reservations.');
        }

        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            if (!$user->hasAccessToHotel($reservation->hotel_id)) {
                abort(403, 'You do not have access to this reservation.');
            }
        }

        // Prevent editing admin override reservations
        if ($reservation->isAdminOverride() && !$user->isSuperAdmin()) {
            abort(403, 'Cannot edit admin override reservations.');
        }

        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
            'payment_status' => 'required|in:pending,partial,paid,refunded',
            'total_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'special_requests' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Check hotel access
        if (!$user->hasAccessToHotel($request->hotel_id)) {
            abort(403, 'You do not have access to this hotel.');
        }

        // Verify room belongs to hotel
        $room = Room::findOrFail($request->room_id);
        if ($room->hotel_id != $request->hotel_id) {
            return back()->withInput()->withErrors(['room_id' => 'Selected room does not belong to the selected hotel.']);
        }

        // Check if room is available for the dates (excluding current reservation)
        $conflictingReservation = Reservation::where('room_id', $request->room_id)
            ->where('id', '!=', $reservation->id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($request) {
                $query->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                      ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date])
                      ->orWhere(function($q) use ($request) {
                          $q->where('check_in_date', '<=', $request->check_in_date)
                            ->where('check_out_date', '>=', $request->check_out_date);
                      });
            })
            ->first();

        if ($conflictingReservation) {
            return back()->withInput()->withErrors(['check_in_date' => 'Room is not available for the selected dates.']);
        }

        DB::beginTransaction();
        try {
            $oldRoomId = $reservation->room_id;
            $oldStatus = $reservation->status;
            $newStatus = $request->status;

            $reservation->update([
                'room_id' => $request->room_id,
                'hotel_id' => $request->hotel_id,
                'guest_id' => $request->guest_id,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'number_of_guests' => $request->number_of_guests,
                'status' => $newStatus,
                'payment_status' => $request->payment_status,
                'total_amount' => $request->total_amount ?? 0.00,
                'paid_amount' => $request->paid_amount ?? 0.00,
                'special_requests' => $request->special_requests,
                'notes' => $request->notes,
            ]);

            // Handle room status changes
            if ($oldRoomId != $request->room_id) {
                // Release old room
                $oldRoom = Room::find($oldRoomId);
                if ($oldRoom) {
                    $oldRoom->update([
                        'status' => 'vacant',
                        'last_status_change' => now(),
                        'status_updated_by' => Auth::id(),
                    ]);
                }

                // Reserve new room
                $room->update([
                    'status' => 'reserved',
                    'last_status_change' => now(),
                    'status_updated_by' => Auth::id(),
                ]);
            } else {
                // Update room status based on reservation status
                $roomStatus = $this->getRoomStatusFromReservationStatus($newStatus);
                if ($room->status != $roomStatus) {
                    $room->update([
                        'status' => $roomStatus,
                        'last_status_change' => now(),
                        'status_updated_by' => Auth::id(),
                    ]);
                }
            }

            // Handle check-in/check-out timestamps
            if ($newStatus == 'checked_in' && !$reservation->actual_check_in) {
                $reservation->update(['actual_check_in' => now()]);
            }
            if ($newStatus == 'checked_out' && !$reservation->actual_check_out) {
                $reservation->update(['actual_check_out' => now()]);
                // Release room
                $room->update([
                    'status' => 'vacant',
                    'last_status_change' => now(),
                    'status_updated_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return to_route('reservations.show', $reservation->id)
                ->with('success', 'Reservation updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating reservation: ' . $e->getMessage());
        }
    }

    /**
     * Check-in a reservation
     */
    public function checkIn(string $id)
    {
        $reservation = Reservation::findOrFail($id);

        // Check permission
        if (!Auth::user()->hasPermission('reservations.check-in')) {
            abort(403, 'You do not have permission to check-in reservations.');
        }

        // Check access
        $user = Auth::user();
        if (!$user->isSuperAdmin() && !$user->hasAccessToHotel($reservation->hotel_id)) {
            abort(403, 'You do not have access to this reservation.');
        }

        if ($reservation->status != 'confirmed') {
            return back()->with('error', 'Only confirmed reservations can be checked in.');
        }

        DB::beginTransaction();
        try {
            $reservation->update([
                'status' => 'checked_in',
                'actual_check_in' => now(),
            ]);

            $reservation->room->update([
                'status' => 'occupied',
                'last_status_change' => now(),
                'status_updated_by' => Auth::id(),
            ]);

            \App\Models\RoomStatusHistory::create([
                'room_id' => $reservation->room_id,
                'previous_status' => 'reserved',
                'new_status' => 'occupied',
                'changed_by' => Auth::id(),
                'changed_at' => now(),
                'notes' => 'Guest checked in - Reservation #' . $reservation->reservation_number,
            ]);

            DB::commit();

            return back()->with('success', 'Guest checked in successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error checking in: ' . $e->getMessage());
        }
    }

    /**
     * Check-out a reservation
     */
    public function checkOut(string $id)
    {
        $reservation = Reservation::findOrFail($id);

        // Check permission
        if (!Auth::user()->hasPermission('reservations.check-out')) {
            abort(403, 'You do not have permission to check-out reservations.');
        }

        // Check access
        $user = Auth::user();
        if (!$user->isSuperAdmin() && !$user->hasAccessToHotel($reservation->hotel_id)) {
            abort(403, 'You do not have access to this reservation.');
        }

        if ($reservation->status != 'checked_in') {
            return back()->with('error', 'Only checked-in reservations can be checked out.');
        }

        DB::beginTransaction();
        try {
            $reservation->update([
                'status' => 'checked_out',
                'actual_check_out' => now(),
            ]);

            $reservation->room->update([
                'status' => 'vacant',
                'last_status_change' => now(),
                'status_updated_by' => Auth::id(),
            ]);

            \App\Models\RoomStatusHistory::create([
                'room_id' => $reservation->room_id,
                'previous_status' => 'occupied',
                'new_status' => 'vacant',
                'changed_by' => Auth::id(),
                'changed_at' => now(),
                'notes' => 'Guest checked out - Reservation #' . $reservation->reservation_number,
            ]);

            DB::commit();

            return back()->with('success', 'Guest checked out successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error checking out: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a reservation
     */
    public function cancel(Request $request, string $id)
    {
        $reservation = Reservation::findOrFail($id);

        // Check permission
        if (!Auth::user()->hasPermission('reservations.cancel')) {
            abort(403, 'You do not have permission to cancel reservations.');
        }

        // Check access
        $user = Auth::user();
        if (!$user->isSuperAdmin() && !$user->hasAccessToHotel($reservation->hotel_id)) {
            abort(403, 'You do not have access to this reservation.');
        }

        // Prevent cancelling admin override reservations
        if ($reservation->isAdminOverride() && !$user->isSuperAdmin()) {
            abort(403, 'Cannot cancel admin override reservations.');
        }

        if (in_array($reservation->status, ['checked_out', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel a reservation that is already checked out or cancelled.');
        }

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $reservation->update([
                'status' => 'cancelled',
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            // Release room if it was reserved
            if (in_array($reservation->room->status, ['reserved', 'occupied'])) {
                $reservation->room->update([
                    'status' => 'vacant',
                    'last_status_change' => now(),
                    'status_updated_by' => Auth::id(),
                ]);

                \App\Models\RoomStatusHistory::create([
                    'room_id' => $reservation->room_id,
                    'previous_status' => $reservation->room->status,
                    'new_status' => 'vacant',
                    'changed_by' => Auth::id(),
                    'changed_at' => now(),
                    'notes' => 'Reservation cancelled - Reservation #' . $reservation->reservation_number,
                ]);
            }

            DB::commit();

            return back()->with('success', 'Reservation cancelled successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error cancelling reservation: ' . $e->getMessage());
        }
    }

    /**
     * Get available rooms for a hotel and date range
     */
    public function getAvailableRooms(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        $user = Auth::user();
        if (!$user->hasAccessToHotel($request->hotel_id)) {
            abort(403, 'You do not have access to this hotel.');
        }

        // Get all rooms for the hotel
        $rooms = Room::where('hotel_id', $request->hotel_id)
            ->where('status', '!=', 'admin_reserved') // Exclude admin reserved
            ->get();

        // Filter out rooms with conflicting reservations
        $availableRooms = $rooms->filter(function($room) use ($request) {
            $conflicting = Reservation::where('room_id', $room->id)
                ->where('status', '!=', 'cancelled')
                ->where(function($query) use ($request) {
                    $query->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                          ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date])
                          ->orWhere(function($q) use ($request) {
                              $q->where('check_in_date', '<=', $request->check_in_date)
                                ->where('check_out_date', '>=', $request->check_out_date);
                          });
                })
                ->exists();

            return !$conflicting;
        });

        return response()->json([
            'rooms' => $availableRooms->map(function($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'room_type' => $room->room_type,
                    'capacity' => $room->capacity,
                    'status' => $room->status,
                ];
            })
        ]);
    }

    /**
     * Generate unique reservation number
     */
    private function generateReservationNumber(): string
    {
        $year = date('Y');
        $prefix = 'RES-' . $year . '-';
        
        $lastReservation = Reservation::where('reservation_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReservation) {
            $lastNumber = (int) str_replace($prefix, '', $lastReservation->reservation_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get room status from reservation status
     */
    private function getRoomStatusFromReservationStatus(string $reservationStatus): string
    {
        return match($reservationStatus) {
            'pending', 'confirmed' => 'reserved',
            'checked_in' => 'occupied',
            'checked_out', 'cancelled', 'no_show' => 'vacant',
            default => 'vacant',
        };
    }
}
