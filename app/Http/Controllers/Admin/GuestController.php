<?php

namespace App\Http\Controllers\Admin;

use App\Models\Guest;
use App\DataTables\GuestsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GuestsDataTable $dataTable)
    {
        return $dataTable->render('guests.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('guests.components.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check permission
        if (!Auth::user()->hasPermission('guests.create')) {
            abort(403, 'You do not have permission to create guests.');
        }

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'id_type' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'preferences' => 'nullable|array',
            'notes' => 'nullable|string',
            'vip_status' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $hotelOwnerId = null;

        // Determine hotel owner ID
        if ($user->isHotelOwner()) {
            $hotelOwnerId = $user->id;
        } elseif ($user->isStaff() && $user->parent_user_id) {
            $hotelOwnerId = $user->parent_user_id;
        }

        // Check if email already exists for this hotel owner
        if ($request->email && $hotelOwnerId) {
            $existingGuest = Guest::where('email', $request->email)
                ->where('hotel_owner_id', $hotelOwnerId)
                ->first();

            if ($existingGuest) {
                return back()->withInput()->withErrors(['email' => 'A guest with this email already exists for this hotel owner.']);
            }
        }

        $guest = Guest::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'phone_secondary' => $request->phone_secondary,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'date_of_birth' => $request->date_of_birth,
            'nationality' => $request->nationality,
            'preferences' => $request->preferences ?? [],
            'notes' => $request->notes,
            'vip_status' => $request->has('vip_status') ? true : false,
            'created_by' => Auth::id(),
            'hotel_owner_id' => $hotelOwnerId,
        ]);

        // Log activity
        app(ActivityLogService::class)->logGuestCreated($guest->id, $guest->full_name);

        return to_route('guests.index')->with('success', 'Guest created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $guest = Guest::with(['createdBy', 'hotelOwner', 'reservations.room', 'reservations.hotel'])->findOrFail($id);
        
        // Check access - guests belong to hotel owners
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            if ($user->isHotelOwner() && $guest->hotel_owner_id !== $user->id) {
                abort(403, 'You do not have access to this guest.');
            }
            if ($user->isStaff() && $guest->hotel_owner_id !== $user->parent_user_id) {
                abort(403, 'You do not have access to this guest.');
            }
        }

        return view('guests.show', compact('guest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $guest = Guest::findOrFail($id);
        
        // Check permission and access
        if (!Auth::user()->hasPermission('guests.edit')) {
            abort(403, 'You do not have permission to edit guests.');
        }

        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            if ($user->isHotelOwner() && $guest->hotel_owner_id !== $user->id) {
                abort(403, 'You do not have access to this guest.');
            }
            if ($user->isStaff() && $guest->hotel_owner_id !== $user->parent_user_id) {
                abort(403, 'You do not have access to this guest.');
            }
        }

        return view('guests.components.edit', compact('guest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $guest = Guest::findOrFail($id);

        // Check permission and access
        if (!Auth::user()->hasPermission('guests.edit')) {
            abort(403, 'You do not have permission to edit guests.');
        }

        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            if ($user->isHotelOwner() && $guest->hotel_owner_id !== $user->id) {
                abort(403, 'You do not have access to this guest.');
            }
            if ($user->isStaff() && $guest->hotel_owner_id !== $user->parent_user_id) {
                abort(403, 'You do not have access to this guest.');
            }
        }

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'id_type' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'preferences' => 'nullable|array',
            'notes' => 'nullable|string',
            'vip_status' => 'nullable|boolean',
        ]);

        // Check if email already exists for this hotel owner (excluding current guest)
        if ($request->email && $guest->hotel_owner_id) {
            $existingGuest = Guest::where('email', $request->email)
                ->where('hotel_owner_id', $guest->hotel_owner_id)
                ->where('id', '!=', $guest->id)
                ->first();

            if ($existingGuest) {
                return back()->withInput()->withErrors(['email' => 'A guest with this email already exists for this hotel owner.']);
            }
        }

        $guest->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'phone_secondary' => $request->phone_secondary,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'date_of_birth' => $request->date_of_birth,
            'nationality' => $request->nationality,
            'preferences' => $request->preferences ?? $guest->preferences ?? [],
            'notes' => $request->notes,
            'vip_status' => $request->has('vip_status') ? true : false,
        ]);

        // Log activity
        app(ActivityLogService::class)->logGuestUpdated($guest->id, $guest->full_name);

        return to_route('guests.index')->with('success', 'Guest updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $guest = Guest::findOrFail($id);

        // Check permission
        if (!Auth::user()->hasPermission('guests.delete')) {
            abort(403, 'You do not have permission to delete guests.');
        }

        // Check access
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            if ($user->isHotelOwner() && $guest->hotel_owner_id !== $user->id) {
                abort(403, 'You do not have access to this guest.');
            }
            if ($user->isStaff() && $guest->hotel_owner_id !== $user->parent_user_id) {
                abort(403, 'You do not have access to this guest.');
            }
        }

        // Don't allow deleting guests with active reservations
        $activeReservations = $guest->reservations()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->count();

        if ($activeReservations > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete guest with active reservations.',
                ], 400);
            }
            return back()->with('error', 'Cannot delete guest with active reservations.');
        }

        $guestName = $guest->full_name;
        $guestId = $guest->id;
        $guest->delete();

        // Log activity
        app(ActivityLogService::class)->logGuestDeleted($guestId, $guestName);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Guest deleted successfully!',
            ]);
        }

        return to_route('guests.index')->with('success', 'Guest deleted successfully!');
    }

    /**
     * Search guests (for AJAX requests)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $user = Auth::user();

        $guests = Guest::query();

        // Filter by hotel owner
        if ($user->isHotelOwner()) {
            $guests->where('hotel_owner_id', $user->id);
        } elseif ($user->isStaff() && $user->parent_user_id) {
            $guests->where('hotel_owner_id', $user->parent_user_id);
        }

        // Search by name, email, or phone
        if ($query) {
            $guests->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            });
        }

        $results = $guests->limit(20)->get()->map(function($guest) {
            return [
                'id' => $guest->id,
                'text' => $guest->full_name . ($guest->email ? ' (' . $guest->email . ')' : '') . ($guest->phone ? ' - ' . $guest->phone : ''),
                'guest' => [
                    'id' => $guest->id,
                    'full_name' => $guest->full_name,
                    'email' => $guest->email,
                    'phone' => $guest->phone,
                ]
            ];
        });

        return response()->json(['results' => $results]);
    }
}

