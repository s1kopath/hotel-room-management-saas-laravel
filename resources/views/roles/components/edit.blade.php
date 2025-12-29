<div class="modal-header">
    <h5 class="modal-title text-brand">Edit Role - {{ $role->name }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" action="{{ route('roles.update', $role->id) }}" id="roleEditForm">
    @csrf
    @method('PUT')
    <div class="modal-body">
        @if($role->scope === 'system')
        <div class="alert alert-warning">
            <i class="material-symbols-rounded">warning</i>
            This is a system role. Changes will affect all users with this role.
        </div>
        @endif

        <div class="mb-3">
            <label for="name" class="form-label">Role Name
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control border rounded-3 @error('name') is-invalid @enderror" 
                id="name" name="name" 
                value="{{ old('name', $role->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control border rounded-3 @error('description') is-invalid @enderror" 
                id="description" name="description" rows="2">{{ old('description', $role->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Assigned Permissions</label>
            @foreach($permissions as $category => $categoryPermissions)
            <div class="card mb-3">
                <div class="card-header">
                    <strong>{{ ucfirst($category) }}</strong>
                    <button type="button" class="btn btn-sm btn-link float-end" onclick="toggleCategory('{{ $category }}')">
                        Toggle All
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
                                    {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
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
        <button type="submit" class="btn btn-primary">Update Role</button>
    </div>
</form>

<script>
function toggleCategory(category) {
    const checkboxes = document.querySelectorAll('.category-' + category);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
</script>

