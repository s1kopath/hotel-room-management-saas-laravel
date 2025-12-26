<div class="modal-header">
    <h5 class="modal-title text-brand">Add User</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="mb-1">
            <label for="name" class="form-label">Name
                <span class="text-danger">*</span>
            </label>
            <input type="name" class="form-control border rounded-3" id="name" name="name"
                placeholder="Enter name" required>
        </div>
        <div class="mb-1">
            <label for="email" class="form-label">Email
                <span class="text-danger">*</span>
            </label>
            <input type="email" class="form-control border rounded-3" id="email" name="email"
                placeholder="Enter email" required>
        </div>
        <div class="mb-1">
            <label for="phone" class="form-label">Phone
                <span class="text-muted">(Optional)</span>
            </label>
            <input type="phone" class="form-control border rounded-3" id="phone" name="phone"
                placeholder="Enter phone">
        </div>
        <div class="mb-1">
            <label for="password" class="form-label">Password
                <span class="text-danger">*</span>
            </label>
            <input type="password" class="form-control border rounded-3" id="password" name="password"
                placeholder="********" required>
        </div>
        <div class="mb-1">
            <label for="is_active" class="form-label">Status</label>
            <select class="form-select border rounded-3 select2" id="is_active" name="is_active" required>
                <option value="1">Active</option>
                <option value="o">Inactive</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create User</button>
    </div>
</form>
