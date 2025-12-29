<div class="modal-header">
    <h5 class="modal-title text-brand">Create New Role</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" action="{{ route('roles.store') }}" id="roleForm">
    @csrf
    <div class="modal-body">
        <div class="mb-3">
            <label for="name" class="form-label">Role Name
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control border rounded-3 @error('name') is-invalid @enderror" 
                id="name" name="name" 
                value="{{ old('name') }}" 
                placeholder="e.g. Night Manager, Housekeeping Supervisor" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control border rounded-3 @error('description') is-invalid @enderror" 
                id="description" name="description" rows="2" 
                placeholder="Brief description of this role">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if($canCreateSystemRole)
        <div class="mb-3">
            <label for="scope" class="form-label">Scope</label>
            <select class="form-select border rounded-3" id="scope" name="scope">
                <option value="hotel_owner" {{ old('scope') == 'hotel_owner' ? 'selected' : '' }}>Custom (Hotel Owner)</option>
                <option value="system" {{ old('scope') == 'system' ? 'selected' : '' }}>System</option>
            </select>
            <small class="text-muted">System roles are available to all hotel owners.</small>
        </div>
        @endif

        <div class="mb-3">
            <label class="form-label">Assign Permissions</label>
            <div class="alert alert-info">
                <i class="material-symbols-rounded">info</i>
                Select the permissions you want to grant to this role.
            </div>
            @foreach($permissions as $category => $categoryPermissions)
            <div class="card mb-3">
                <div class="card-header">
                    <strong>{{ ucfirst($category) }}</strong>
                    <button type="button" class="btn btn-sm btn-link float-end" onclick="toggleCategory('{{ $category }}')">
                        Select All
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categoryPermissions as $permission)
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input category-{{ $category }}" type="checkbox" 
                                    name="permissions[]" value="{{ $permission->id }}" 
                                    id="perm-{{ $permission->id }}"
                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm-{{ $permission->id }}">
                                    {{ $permission->name }}
                                    @if($permission->description)
                                    <br><small class="text-muted">{{ $permission->description }}</small>
                                    @endif
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Role</button>
    </div>
</form>

<script>
function toggleCategory(category) {
    const checkboxes = document.querySelectorAll('.category-' + category);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
</script>

