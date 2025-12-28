<?php

namespace App\Http\Controllers\Admin;

use App\Models\Hotel;
use App\Models\User;
use App\DataTables\HotelsDataTable;
use App\Http\Controllers\Controller;
use App\Service\FileHandlerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(HotelsDataTable $dataTable)
    {
        return $dataTable->render('hotels.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hotels.components.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check permission
        if (!Auth::user()->hasPermission('hotels.create')) {
            abort(403, 'You do not have permission to create hotels.');
        }

        $request->validate([
            'name' => 'required|string|max:200',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Generate unique slug
        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Hotel::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $user = Auth::user();
        $hotel = Hotel::create([
            'user_id' => $user->isSuperAdmin() ? ($request->user_id ?? $user->id) : $user->id,
            'name' => $request->name,
            'slug' => $slug,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'email' => $request->email,
            'description' => $request->description,
            'status' => 'active',
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $fileHandler = new FileHandlerService();
            foreach ($request->file('images') as $index => $image) {
                $imagePath = $fileHandler->uploadImageAndGetPath($image, '/public/hotels');
                
                $hotel->images()->create([
                    'image_url' => $imagePath,
                    'image_type' => $index === 0 ? 'main' : 'gallery',
                    'display_order' => $index,
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        return to_route('hotels.index')->with('success', 'Hotel created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hotel = Hotel::with(['owner', 'images', 'rooms', 'reservations'])->findOrFail($id);
        
        // Check access
        if (!Auth::user()->hasAccessToHotel($hotel->id)) {
            abort(403, 'You do not have access to this hotel.');
        }

        return view('hotels.show', compact('hotel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $hotel = Hotel::with('images')->findOrFail($id);
        
        // Check permission and access
        if (!Auth::user()->hasPermission('hotels.edit-own') && !Auth::user()->hasPermission('hotels.edit-all')) {
            abort(403, 'You do not have permission to edit hotels.');
        }

        if (!Auth::user()->isSuperAdmin() && !Auth::user()->hasAccessToHotel($hotel->id)) {
            abort(403, 'You do not have access to this hotel.');
        }

        return view('hotels.components.edit', compact('hotel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $hotel = Hotel::findOrFail($id);

        // Check permission and access
        if (!Auth::user()->hasPermission('hotels.edit-own') && !Auth::user()->hasPermission('hotels.edit-all')) {
            abort(403, 'You do not have permission to edit hotels.');
        }

        if (!Auth::user()->isSuperAdmin() && !Auth::user()->hasAccessToHotel($hotel->id)) {
            abort(403, 'You do not have access to this hotel.');
        }

        $request->validate([
            'name' => 'required|string|max:200',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,archived',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update slug if name changed
        if ($hotel->name !== $request->name) {
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $counter = 1;
            while (Hotel::where('slug', $slug)->where('id', '!=', $hotel->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $hotel->slug = $slug;
        }

        $hotel->update([
            'name' => $request->name,
            'slug' => $hotel->slug,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'email' => $request->email,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $fileHandler = new FileHandlerService();
            $existingImagesCount = $hotel->images()->count();
            foreach ($request->file('images') as $index => $image) {
                $imagePath = $fileHandler->uploadImageAndGetPath($image, '/public/hotels');
                
                $hotel->images()->create([
                    'image_url' => $imagePath,
                    'image_type' => ($existingImagesCount + $index === 0) ? 'main' : 'gallery',
                    'display_order' => $existingImagesCount + $index,
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        return to_route('hotels.index')->with('success', 'Hotel updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $hotel = Hotel::findOrFail($id);

        // Check permission
        if (!Auth::user()->hasPermission('hotels.delete-own') && !Auth::user()->hasPermission('hotels.delete-all')) {
            abort(403, 'You do not have permission to delete hotels.');
        }

        if (!Auth::user()->isSuperAdmin() && !Auth::user()->hasAccessToHotel($hotel->id)) {
            abort(403, 'You do not have access to this hotel.');
        }

        // Archive instead of delete
        $hotel->update(['status' => 'archived']);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Hotel archived successfully!']);
        }

        return to_route('hotels.index')->with('success', 'Hotel archived successfully!');
    }
}

