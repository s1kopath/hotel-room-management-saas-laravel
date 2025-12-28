<div class="modal-header">
    <h5 class="modal-title text-brand">Add Hotel</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" action="{{ route('hotels.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="mb-3">
            <label for="name" class="form-label">Hotel Name
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control border rounded-3 @error('name') is-invalid @enderror" 
                id="name" name="name" value="{{ old('name') }}" 
                placeholder="Enter hotel name" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control border rounded-3 @error('city') is-invalid @enderror" 
                    id="city" name="city" value="{{ old('city') }}" 
                    placeholder="Enter city">
                @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="state" class="form-label">State/Province</label>
                <input type="text" class="form-control border rounded-3 @error('state') is-invalid @enderror" 
                    id="state" name="state" value="{{ old('state') }}" 
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
                    id="country" name="country" value="{{ old('country') }}" 
                    placeholder="Enter country">
                @error('country')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="postal_code" class="form-label">Postal Code</label>
                <input type="text" class="form-control border rounded-3 @error('postal_code') is-invalid @enderror" 
                    id="postal_code" name="postal_code" value="{{ old('postal_code') }}" 
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
                placeholder="Enter full address">{{ old('address') }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control border rounded-3 @error('phone') is-invalid @enderror" 
                    id="phone" name="phone" value="{{ old('phone') }}" 
                    placeholder="Enter phone number">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control border rounded-3 @error('email') is-invalid @enderror" 
                    id="email" name="email" value="{{ old('email') }}" 
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
                placeholder="Enter hotel description">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="images" class="form-label">Hotel Images</label>
            <input type="file" class="form-control border rounded-3 @error('images.*') is-invalid @enderror" 
                id="images" name="images[]" multiple accept="image/*">
            <small class="text-muted">You can select multiple images. First image will be set as main image.</small>
            @error('images.*')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Hotel</button>
    </div>
</form>

