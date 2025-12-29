<div class="modal-header">
    <h5 class="modal-title text-brand">Add Room</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" action="{{ route('rooms.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        @if(isset($hotels) && $hotels->count() > 1)
            {{-- Show hotel selector if multiple hotels --}}
            <div class="mb-3">
                <label for="hotel_id" class="form-label">Select Hotel
                    <span class="text-danger">*</span>
                </label>
                <select class="form-select border rounded-3 @error('hotel_id') is-invalid @enderror" 
                    id="hotel_id" name="hotel_id" required>
                    <option value="">Select a hotel</option>
                    @foreach($hotels as $h)
                        <option value="{{ $h->id }}" 
                            {{ old('hotel_id', $selected_hotel_id ?? '') == $h->id ? 'selected' : '' }}>
                            {{ $h->name }} ({{ $h->city ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                @error('hotel_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @elseif(isset($hotels) && $hotels->count() == 1)
            {{-- Show hotel name if single hotel (read-only) --}}
            @php
                $hotel = $hotels->first();
            @endphp
            <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
            <div class="mb-3">
                <label class="form-label">Hotel</label>
                <input type="text" class="form-control" value="{{ $hotel->name }}" disabled>
            </div>
        @else
            {{-- Fallback: should not happen, but handle gracefully --}}
            <div class="alert alert-danger">
                No hotels available. Please create a hotel first.
            </div>
        @endif

        <div class="mb-3">
            <label for="room_number" class="form-label">Room Number
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control border rounded-3 @error('room_number') is-invalid @enderror" 
                id="room_number" name="room_number" value="{{ old('room_number') }}" 
                placeholder="e.g., 101, 205, Suite-1" required>
            @error('room_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="room_type" class="form-label">Room Type</label>
                <input type="text" class="form-control border rounded-3 @error('room_type') is-invalid @enderror" 
                    id="room_type" name="room_type" value="{{ old('room_type') }}" 
                    placeholder="e.g., Single, Double, Suite">
                @error('room_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="floor_number" class="form-label">Floor Number</label>
                <input type="number" class="form-control border rounded-3 @error('floor_number') is-invalid @enderror" 
                    id="floor_number" name="floor_number" value="{{ old('floor_number') }}" 
                    placeholder="e.g., 1, 2, 3">
                @error('floor_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="capacity" class="form-label">Capacity (Guests)</label>
                <input type="number" class="form-control border rounded-3 @error('capacity') is-invalid @enderror" 
                    id="capacity" name="capacity" value="{{ old('capacity') }}" 
                    placeholder="e.g., 1, 2, 4" min="1">
                @error('capacity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status
                    <span class="text-danger">*</span>
                </label>
                <select class="form-select border rounded-3 @error('status') is-invalid @enderror" 
                    id="status" name="status" required>
                    <option value="vacant" {{ old('status') == 'vacant' ? 'selected' : '' }}>🟢 Vacant</option>
                    <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>🟡 Reserved</option>
                    <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>🔴 Occupied</option>
                    @if(auth()->user()->isSuperAdmin())
                    <option value="admin_reserved" {{ old('status') == 'admin_reserved' ? 'selected' : '' }}>🔵 Admin Reserved</option>
                    @endif
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control border rounded-3 @error('description') is-invalid @enderror" 
                id="description" name="description" rows="3" 
                placeholder="Room description, amenities, etc.">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="images" class="form-label">Room Images</label>
            <input type="file" class="form-control border rounded-3 @error('images.*') is-invalid @enderror" 
                id="images" name="images[]" multiple accept="image/*">
            <small class="text-muted">You can select multiple images.</small>
            @error('images.*')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Room</button>
    </div>
</form>

