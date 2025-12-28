<div class="modal-header">
    <h5 class="modal-title text-brand">Add Guest</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" action="{{ route('guests.store') }}">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">First Name
                    <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control border rounded-3 @error('first_name') is-invalid @enderror" 
                    id="first_name" name="first_name" value="{{ old('first_name') }}" 
                    placeholder="Enter first name" required>
                @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Last Name
                    <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control border rounded-3 @error('last_name') is-invalid @enderror" 
                    id="last_name" name="last_name" value="{{ old('last_name') }}" 
                    placeholder="Enter last name" required>
                @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control border rounded-3 @error('email') is-invalid @enderror" 
                    id="email" name="email" value="{{ old('email') }}" 
                    placeholder="Enter email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control border rounded-3 @error('phone') is-invalid @enderror" 
                    id="phone" name="phone" value="{{ old('phone') }}" 
                    placeholder="Enter phone number">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="phone_secondary" class="form-label">Secondary Phone</label>
            <input type="text" class="form-control border rounded-3 @error('phone_secondary') is-invalid @enderror" 
                id="phone_secondary" name="phone_secondary" value="{{ old('phone_secondary') }}" 
                placeholder="Enter secondary phone">
            @error('phone_secondary')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control border rounded-3 @error('address') is-invalid @enderror" 
                id="address" name="address" rows="2" 
                placeholder="Enter address">{{ old('address') }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control border rounded-3 @error('city') is-invalid @enderror" 
                    id="city" name="city" value="{{ old('city') }}" 
                    placeholder="Enter city">
                @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="state" class="form-label">State/Province</label>
                <input type="text" class="form-control border rounded-3 @error('state') is-invalid @enderror" 
                    id="state" name="state" value="{{ old('state') }}" 
                    placeholder="Enter state">
                @error('state')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="country" class="form-label">Country</label>
                <input type="text" class="form-control border rounded-3 @error('country') is-invalid @enderror" 
                    id="country" name="country" value="{{ old('country') }}" 
                    placeholder="Enter country">
                @error('country')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="postal_code" class="form-label">Postal Code</label>
                <input type="text" class="form-control border rounded-3 @error('postal_code') is-invalid @enderror" 
                    id="postal_code" name="postal_code" value="{{ old('postal_code') }}" 
                    placeholder="Enter postal code">
                @error('postal_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="date_of_birth" class="form-label">Date of Birth</label>
                <input type="date" class="form-control border rounded-3 @error('date_of_birth') is-invalid @enderror" 
                    id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                @error('date_of_birth')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="id_type" class="form-label">ID Type</label>
                <select class="form-select border rounded-3 @error('id_type') is-invalid @enderror" 
                    id="id_type" name="id_type">
                    <option value="">Select ID Type</option>
                    <option value="Passport" {{ old('id_type') == 'Passport' ? 'selected' : '' }}>Passport</option>
                    <option value="Driver's License" {{ old('id_type') == "Driver's License" ? 'selected' : '' }}>Driver's License</option>
                    <option value="National ID" {{ old('id_type') == 'National ID' ? 'selected' : '' }}>National ID</option>
                    <option value="Other" {{ old('id_type') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('id_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="id_number" class="form-label">ID Number</label>
                <input type="text" class="form-control border rounded-3 @error('id_number') is-invalid @enderror" 
                    id="id_number" name="id_number" value="{{ old('id_number') }}" 
                    placeholder="Enter ID number">
                @error('id_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="nationality" class="form-label">Nationality</label>
            <input type="text" class="form-control border rounded-3 @error('nationality') is-invalid @enderror" 
                id="nationality" name="nationality" value="{{ old('nationality') }}" 
                placeholder="Enter nationality">
            @error('nationality')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Guest Preferences</label>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="preferences[smoking]" value="1" 
                            id="pref_smoking" {{ old('preferences.smoking') ? 'checked' : '' }}>
                        <label class="form-check-label" for="pref_smoking">Smoking Room</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="preferences[high_floor]" value="1" 
                            id="pref_high_floor" {{ old('preferences.high_floor') ? 'checked' : '' }}>
                        <label class="form-check-label" for="pref_high_floor">High Floor</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="preferences[quiet_room]" value="1" 
                            id="pref_quiet" {{ old('preferences.quiet_room') ? 'checked' : '' }}>
                        <label class="form-check-label" for="pref_quiet">Quiet Room</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="preferences[extra_bed]" value="1" 
                            id="pref_extra_bed" {{ old('preferences.extra_bed') ? 'checked' : '' }}>
                        <label class="form-check-label" for="pref_extra_bed">Extra Bed</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="preferences[late_checkout]" value="1" 
                            id="pref_late_checkout" {{ old('preferences.late_checkout') ? 'checked' : '' }}>
                        <label class="form-check-label" for="pref_late_checkout">Late Checkout</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="preferences[airport_shuttle]" value="1" 
                            id="pref_shuttle" {{ old('preferences.airport_shuttle') ? 'checked' : '' }}>
                        <label class="form-check-label" for="pref_shuttle">Airport Shuttle</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control border rounded-3 @error('notes') is-invalid @enderror" 
                id="notes" name="notes" rows="3" 
                placeholder="Special notes about guest">{{ old('notes') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="vip_status" value="1" 
                    id="vip_status" {{ old('vip_status') ? 'checked' : '' }}>
                <label class="form-check-label" for="vip_status">
                    VIP Guest
                </label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Guest</button>
    </div>
</form>

