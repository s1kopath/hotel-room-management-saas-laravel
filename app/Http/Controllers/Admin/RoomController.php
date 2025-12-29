<?php

namespace App\Http\Controllers\Admin;

use App\Models\Room;
use App\Models\Hotel;
use App\Models\RoomStatusHistory;
use App\DataTables\RoomsDataTable;
use App\Http\Controllers\Controller;
use App\Service\FileHandlerService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, RoomsDataTable $dataTable)
    {
        // Get hotel_id from request if provided
        $hotelId = $request->get('hotel_id');
        
        if ($hotelId) {
            $hotel = Hotel::findOrFail($hotelId);
            // Check access
            if (!Auth::user()->hasAccessToHotel($hotelId)) {
                abort(403, 'You do not have access to this hotel.');
            }
            return $dataTable->forHotel($hotelId)->render('rooms.index', compact('hotel'));
        }

        return $dataTable->render('rooms.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $hotelId = $request->get('hotel_id');
        $user = Auth::user();
        
        // Get accessible hotels for the user
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

        // No hotels available
        if ($hotels->isEmpty()) {
            return back()->with('error', 'No hotels available. Please create a hotel first.');
        }

        // If hotel_id provided, validate access
        if ($hotelId) {
            $selectedHotel = Hotel::find($hotelId);
            if (!$selectedHotel || !$user->hasAccessToHotel($hotelId)) {
                abort(403, 'You do not have access to this hotel.');
            }
        }

        // Always pass hotels collection and optionally selected hotel_id
        return view('rooms.components.create', [
            'hotels' => $hotels,
            'selected_hotel_id' => $hotelId
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check permission
        if (!Auth::user()->hasPermission('rooms.create')) {
            abort(403, 'You do not have permission to create rooms.');
        }

        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_number' => 'required|string|max:50',
            'room_type' => 'nullable|string|max:100',
            'floor_number' => 'nullable|integer',
            'capacity' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'required|in:vacant,reserved,occupied,admin_reserved',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check hotel access
        if (!Auth::user()->hasAccessToHotel($request->hotel_id)) {
            abort(403, 'You do not have access to this hotel.');
        }

        // Check if room number already exists for this hotel
        $existingRoom = Room::where('hotel_id', $request->hotel_id)
            ->where('room_number', $request->room_number)
            ->first();

        if ($existingRoom) {
            return back()->withInput()->withErrors(['room_number' => 'Room number already exists for this hotel.']);
        }

        DB::beginTransaction();
        try {
            $room = Room::create([
                'hotel_id' => $request->hotel_id,
                'room_number' => $request->room_number,
                'room_type' => $request->room_type,
                'floor_number' => $request->floor_number,
                'capacity' => $request->capacity,
                'description' => $request->description,
                'status' => $request->status,
                'last_status_change' => now(),
                'status_updated_by' => Auth::id(),
            ]);

            // Record initial status in history
            RoomStatusHistory::create([
                'room_id' => $room->id,
                'previous_status' => null,
                'new_status' => $request->status,
                'changed_by' => Auth::id(),
                'changed_at' => now(),
                'notes' => 'Room created',
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $fileHandler = new FileHandlerService();
                foreach ($request->file('images') as $index => $image) {
                    $imagePath = $fileHandler->uploadImageAndGetPath($image, '/public/rooms');
                    
                    $room->images()->create([
                        'image_url' => $imagePath,
                        'display_order' => $index,
                        'uploaded_by' => Auth::id(),
                        'uploaded_at' => now(),
                    ]);
                }
            }

            // Update hotel total_rooms count
            $hotel = Hotel::find($request->hotel_id);
            $hotel->increment('total_rooms');

            // Log activity
            app(ActivityLogService::class)->logRoomCreated($room->id, $room->room_number);

            DB::commit();

            return to_route('rooms.index', ['hotel_id' => $request->hotel_id])
                ->with('success', 'Room created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating room: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $room = Room::with(['hotel', 'images', 'statusHistory.changedBy', 'statusUpdatedBy'])->findOrFail($id);
        
        // Check access
        if (!Auth::user()->hasAccessToHotel($room->hotel_id)) {
            abort(403, 'You do not have access to this room.');
        }

        return view('rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $room = Room::with(['hotel', 'images'])->findOrFail($id);
        
        // Check permission and access
        if (!Auth::user()->hasPermission('rooms.edit')) {
            abort(403, 'You do not have permission to edit rooms.');
        }

        if (!Auth::user()->hasAccessToHotel($room->hotel_id)) {
            abort(403, 'You do not have access to this room.');
        }

        return view('rooms.components.edit', compact('room'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $room = Room::findOrFail($id);

        // Check permission and access
        if (!Auth::user()->hasPermission('rooms.edit')) {
            abort(403, 'You do not have permission to edit rooms.');
        }

        if (!Auth::user()->hasAccessToHotel($room->hotel_id)) {
            abort(403, 'You do not have access to this room.');
        }

        $request->validate([
            'room_number' => 'required|string|max:50',
            'room_type' => 'nullable|string|max:100',
            'floor_number' => 'nullable|integer',
            'capacity' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'required|in:vacant,reserved,occupied,admin_reserved',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if room number already exists for this hotel (excluding current room)
        $existingRoom = Room::where('hotel_id', $room->hotel_id)
            ->where('room_number', $request->room_number)
            ->where('id', '!=', $room->id)
            ->first();

        if ($existingRoom) {
            return back()->withInput()->withErrors(['room_number' => 'Room number already exists for this hotel.']);
        }

        DB::beginTransaction();
        try {
            $previousStatus = $room->status;
            $statusChanged = $previousStatus !== $request->status;

            $room->update([
                'room_number' => $request->room_number,
                'room_type' => $request->room_type,
                'floor_number' => $request->floor_number,
                'capacity' => $request->capacity,
                'description' => $request->description,
                'status' => $request->status,
                'last_status_change' => $statusChanged ? now() : $room->last_status_change,
                'status_updated_by' => $statusChanged ? Auth::id() : $room->status_updated_by,
            ]);

            // Record status change in history if status changed
            if ($statusChanged) {
                RoomStatusHistory::create([
                    'room_id' => $room->id,
                    'previous_status' => $previousStatus,
                    'new_status' => $request->status,
                    'changed_by' => Auth::id(),
                    'changed_at' => now(),
                    'notes' => $request->status_change_notes ?? 'Status changed',
                ]);
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $fileHandler = new FileHandlerService();
                $existingImagesCount = $room->images()->count();
                foreach ($request->file('images') as $index => $image) {
                    $imagePath = $fileHandler->uploadImageAndGetPath($image, '/public/rooms');
                    
                    $room->images()->create([
                        'image_url' => $imagePath,
                        'display_order' => $existingImagesCount + $index,
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return to_route('rooms.index', ['hotel_id' => $room->hotel_id])
                ->with('success', 'Room updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating room: ' . $e->getMessage());
        }
    }

    /**
     * Change room status (quick action)
     */
    public function changeStatus(Request $request, string $id)
    {
        $room = Room::findOrFail($id);

        // Check permission
        if (!Auth::user()->hasPermission('rooms.change-status')) {
            abort(403, 'You do not have permission to change room status.');
        }

        // Check access
        if (!Auth::user()->hasAccessToHotel($room->hotel_id)) {
            abort(403, 'You do not have access to this room.');
        }

        // Prevent changing admin reserved rooms (unless super admin)
        if ($room->isAdminReserved() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Cannot change status of admin reserved room.');
        }

        $request->validate([
            'status' => 'required|in:vacant,reserved,occupied,admin_reserved',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $previousStatus = $room->status;

            $room->update([
                'status' => $request->status,
                'last_status_change' => now(),
                'status_updated_by' => Auth::id(),
            ]);

            // Record status change in history
            RoomStatusHistory::create([
                'room_id' => $room->id,
                'previous_status' => $previousStatus,
                'new_status' => $request->status,
                'changed_by' => Auth::id(),
                'changed_at' => now(),
                'notes' => $request->notes ?? 'Status changed',
            ]);

            // Log activity
            app(ActivityLogService::class)->logRoomStatusChanged(
                $room->id, 
                $room->room_number, 
                $previousStatus, 
                $request->status
            );

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Room status updated successfully!',
                    'room' => $room->fresh(),
                ]);
            }

            return back()->with('success', 'Room status updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating room status: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Error updating room status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $room = Room::findOrFail($id);

        // Check permission
        if (!Auth::user()->hasPermission('rooms.delete')) {
            abort(403, 'You do not have permission to delete rooms.');
        }

        // Check access
        if (!Auth::user()->hasAccessToHotel($room->hotel_id)) {
            abort(403, 'You do not have access to this room.');
        }

        // Don't allow deleting rooms with active reservations
        $activeReservations = $room->reservations()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->count();

        if ($activeReservations > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete room with active reservations.',
                ], 400);
            }
            return back()->with('error', 'Cannot delete room with active reservations.');
        }

        DB::beginTransaction();
        try {
            $hotelId = $room->hotel_id;
            $roomNumber = $room->room_number;
            $roomId = $room->id;
            
            // Delete room images
            foreach ($room->images as $image) {
                $fileHandler = new FileHandlerService();
                $fileHandler->deleteImage($image->image_url);
            }
            $room->images()->delete();

            // Delete status history
            $room->statusHistory()->delete();

            // Delete the room
            $room->delete();

            // Update hotel total_rooms count
            $hotel = Hotel::find($hotelId);
            if ($hotel && $hotel->total_rooms > 0) {
                $hotel->decrement('total_rooms');
            }

            // Log activity
            app(ActivityLogService::class)->logRoomDeleted($roomId, $roomNumber);

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Room deleted successfully!',
                ]);
            }

            return to_route('rooms.index', ['hotel_id' => $hotelId])
                ->with('success', 'Room deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting room: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Error deleting room: ' . $e->getMessage());
        }
    }
}

