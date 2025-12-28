<div class="modal-header">
    <h5 class="modal-title text-brand">Edit User</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="mb-3">
            <label for="username" class="form-label">Username
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control border rounded-3 @error('username') is-invalid @enderror" 
                id="username" name="username" value="{{ old('username', $user->username) }}" 
                placeholder="Enter username" required>
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email
                <span class="text-danger">*</span>
            </label>
            <input type="email" class="form-control border rounded-3 @error('email') is-invalid @enderror" 
                id="email" name="email" value="{{ old('email', $user->email) }}" 
                placeholder="Enter email" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name
                <span class="text-muted">(Optional)</span>
            </label>
            <input type="text" class="form-control border rounded-3 @error('full_name') is-invalid @enderror" 
                id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" 
                placeholder="Enter full name">
            @error('full_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone
                <span class="text-muted">(Optional)</span>
            </label>
            <input type="text" class="form-control border rounded-3 @error('phone') is-invalid @enderror" 
                id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                placeholder="Enter phone">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="user_type" class="form-label">User Type
                <span class="text-danger">*</span>
            </label>
            <select class="form-select border rounded-3 @error('user_type') is-invalid @enderror" 
                id="user_type" name="user_type" required>
                <option value="">Select user type</option>
                <option value="super_admin" {{ old('user_type', $user->user_type) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="hotel_owner" {{ old('user_type', $user->user_type) == 'hotel_owner' ? 'selected' : '' }}>Hotel Owner</option>
                <option value="staff" {{ old('user_type', $user->user_type) == 'staff' ? 'selected' : '' }}>Staff</option>
            </select>
            @error('user_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3" id="parent_user_container" style="display: {{ old('user_type', $user->user_type) == 'staff' ? 'block' : 'none' }};">
            <label for="parent_user_id" class="form-label">Hotel Owner (Parent)
                <span class="text-muted">(Required for staff)</span>
            </label>
            <select class="form-select border rounded-3 @error('parent_user_id') is-invalid @enderror" 
                id="parent_user_id" name="parent_user_id">
                <option value="">Select hotel owner</option>
                @foreach($hotelOwners as $owner)
                    <option value="{{ $owner->id }}" {{ old('parent_user_id', $user->parent_user_id) == $owner->id ? 'selected' : '' }}>
                        {{ $owner->full_name ?? $owner->username }} ({{ $owner->email }})
                    </option>
                @endforeach
            </select>
            @error('parent_user_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password
                <span class="text-muted">(Leave blank to keep current password)</span>
            </label>
            <input type="password" class="form-control border rounded-3 @error('password') is-invalid @enderror" 
                id="password" name="password" placeholder="Enter new password (min 8 characters)">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status
                <span class="text-danger">*</span>
            </label>
            <select class="form-select border rounded-3 @error('status') is-invalid @enderror" 
                id="status" name="status" required>
                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                <option value="deleted" {{ old('status', $user->status) == 'deleted' ? 'selected' : '' }}>Deleted</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update User</button>
    </div>
</form>

<script>
    // Show/hide parent user field based on user type
    document.getElementById('user_type').addEventListener('change', function() {
        const parentContainer = document.getElementById('parent_user_container');
        if (this.value === 'staff') {
            parentContainer.style.display = 'block';
            document.getElementById('parent_user_id').setAttribute('required', 'required');
        } else {
            parentContainer.style.display = 'none';
            document.getElementById('parent_user_id').removeAttribute('required');
        }
    });
</script>

