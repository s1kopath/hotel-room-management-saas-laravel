<div class="modal-header">
    <h5 class="modal-title text-brand">Edit Hotel</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" action="{{ route('hotels.update', $hotel->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="mb-3">
            <label for="name" class="form-label">Hotel Name
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control border rounded-3 @error('name') is-invalid @enderror" 
                id="name" name="name" value="{{ old('name', $hotel->name) }}" 
                placeholder="Enter hotel name" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control border rounded-3 @error('city') is-invalid @enderror" 
                    id="city" name="city" value="{{ old('city', $hotel->city) }}" 
                    placeholder="Enter city">
                @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="state" class="form-label">State/Province</label>
                <input type="text" class="form-control border rounded-3 @error('state') is-invalid @enderror" 
                    id="state" name="state" value="{{ old('state', $hotel->state) }}" 
                    placeholder="Enter state">
                @error('state')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="country" class="form-label">Country</label>
                <input type="text" class="form-control border rounded-3 @error('country') is-invalid @enderror" 
                    id="country" name="country" value="{{ old('country', $hotel->country) }}" 
                    placeholder="Enter country">
                @error('country')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="postal_code" class="form-label">Postal Code</label>
                <input type="text" class="form-control border rounded-3 @error('postal_code') is-invalid @enderror" 
                    id="postal_code" name="postal_code" value="{{ old('postal_code', $hotel->postal_code) }}" 
                    placeholder="Enter postal code">
                @error('postal_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control border rounded-3 @error('address') is-invalid @enderror" 
                id="address" name="address" rows="2" 
                placeholder="Enter full address">{{ old('address', $hotel->address) }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control border rounded-3 @error('phone') is-invalid @enderror" 
                    id="phone" name="phone" value="{{ old('phone', $hotel->phone) }}" 
                    placeholder="Enter phone number">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control border rounded-3 @error('email') is-invalid @enderror" 
                    id="email" name="email" value="{{ old('email', $hotel->email) }}" 
                    placeholder="Enter email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control border rounded-3 @error('description') is-invalid @enderror" 
                id="description" name="description" rows="3" 
                placeholder="Enter hotel description">{{ old('description', $hotel->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status
                <span class="text-danger">*</span>
            </label>
            <select class="form-select border rounded-3 @error('status') is-invalid @enderror" 
                id="status" name="status" required>
                <option value="active" {{ old('status', $hotel->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $hotel->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="archived" {{ old('status', $hotel->status) == 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if($hotel->images->count() > 0)
        <div class="mb-3">
            <label class="form-label">Current Images</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach($hotel->images as $image)
                    <div class="position-relative">
                        <img src="{{ asset('storage/' . $image->image_url) }}" alt="Hotel Image" 
                            class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        @if($image->image_type == 'main')
                            <span class="badge bg-primary position-absolute top-0 start-0">Main</span>
                        @endif
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
        <button type="submit" class="btn btn-primary">Update Hotel</button>
    </div>
</form>

