<div class="modal-header">
    <h5 class="modal-title text-brand">Edit Room</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" action="{{ route('rooms.update', $room->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Hotel</label>
            <input type="text" class="form-control" value="{{ $room->hotel->name }}" disabled>
        </div>

        <div class="mb-3">
            <label for="room_number" class="form-label">Room Number
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control border rounded-3 @error('room_number') is-invalid @enderror" 
                id="room_number" name="room_number" value="{{ old('room_number', $room->room_number) }}" 
                placeholder="e.g., 101, 205, Suite-1" required>
            @error('room_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="room_type" class="form-label">Room Type</label>
                <input type="text" class="form-control border rounded-3 @error('room_type') is-invalid @enderror" 
                    id="room_type" name="room_type" value="{{ old('room_type', $room->room_type) }}" 
                    placeholder="e.g., Single, Double, Suite">
                @error('room_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="floor_number" class="form-label">Floor Number</label>
                <input type="number" class="form-control border rounded-3 @error('floor_number') is-invalid @enderror" 
                    id="floor_number" name="floor_number" value="{{ old('floor_number', $room->floor_number) }}" 
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
                    id="capacity" name="capacity" value="{{ old('capacity', $room->capacity) }}" 
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
                    <option value="vacant" {{ old('status', $room->status) == 'vacant' ? 'selected' : '' }}>🟢 Vacant</option>
                    <option value="reserved" {{ old('status', $room->status) == 'reserved' ? 'selected' : '' }}>🟡 Reserved</option>
                    <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>🔴 Occupied</option>
                    @if(auth()->user()->isSuperAdmin() || $room->status == 'admin_reserved')
                    <option value="admin_reserved" {{ old('status', $room->status) == 'admin_reserved' ? 'selected' : '' }}>🔵 Admin Reserved</option>
                    @endif
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="status_change_notes" class="form-label">Status Change Notes
                <span class="text-muted">(if status changed)</span>
            </label>
            <textarea class="form-control border rounded-3 @error('status_change_notes') is-invalid @enderror" 
                id="status_change_notes" name="status_change_notes" rows="2" 
                placeholder="Optional notes for status change">{{ old('status_change_notes') }}</textarea>
            @error('status_change_notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control border rounded-3 @error('description') is-invalid @enderror" 
                id="description" name="description" rows="3" 
                placeholder="Room description, amenities, etc.">{{ old('description', $room->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if($room->images->count() > 0)
        <div class="mb-3">
            <label class="form-label">Current Images</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach($room->images as $image)
                    <div class="position-relative">
                        <img src="{{ asset('storage/' . $image->image_url) }}" alt="Room Image" 
                            class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="mb-3">
            <label for="images" class="form-label">Add More Images</label>
            <input type="file" class="form-control border rounded-3 @error('images.*') is-invalid @enderror" 
                id="images" name="images[]" multiple accept="image/*">
            <small class="text-muted">You can select multiple images to add.</small>
            @error('images.*')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Room</button>
    </div>
</form>

