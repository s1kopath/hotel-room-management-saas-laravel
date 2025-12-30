<div class="modal-header">
    <h5 class="modal-title text-brand">Assign Roles to {{ $user->full_name ?? $user->username }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('users.roles.update', $user->id) }}" method="POST" id="editRolesForm">
    @csrf
    @method('PUT')
    <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">User Information</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-1"><strong>Username:</strong> {{ $user->username }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                                <p class="mb-0"><strong>User Type:</strong> 
                                    @if($user->user_type == 'super_admin')
                                        <span class="badge bg-danger">Super Admin</span>
                                    @elseif($user->user_type == 'hotel_owner')
                                        <span class="badge bg-primary">Hotel Owner</span>
                                    @else
                                        <span class="badge bg-info">Staff</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Available Roles</label>
                        <p class="text-muted small">Select one or more roles to assign to this user. Permissions are cumulative (user gets all permissions from all assigned roles).</p>
                        
                        @if($availableRoles->isEmpty())
                            <div class="alert alert-warning">
                                No roles available. Please create roles first.
                            </div>
                        @else
                            <div class="row">
                                @php
                                    $systemRoles = $availableRoles->where('scope', 'system');
                                    $customRoles = $availableRoles->where('scope', 'hotel_owner');
                                    $currentRoleIds = $user->roles->pluck('id')->toArray();
                                @endphp

                                @if($systemRoles->isNotEmpty())
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary">System Roles</h6>
                                    @foreach($systemRoles as $role)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="roles[]" 
                                                value="{{ $role->id }}" id="role_{{ $role->id }}"
                                                {{ in_array($role->id, $currentRoleIds) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                <strong>{{ $role->name }}</strong>
                                                @if($role->description)
                                                    <br><small class="text-muted">{{ $role->description }}</small>
                                                @endif
                                                <br><small class="badge bg-secondary">{{ $role->permissions()->count() }} permissions</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @endif

                                @if($customRoles->isNotEmpty())
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-info">Custom Roles</h6>
                                    @foreach($customRoles as $role)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="roles[]" 
                                                value="{{ $role->id }}" id="role_{{ $role->id }}"
                                                {{ in_array($role->id, $currentRoleIds) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                <strong>{{ $role->name }}</strong>
                                                @if($role->description)
                                                    <br><small class="text-muted">{{ $role->description }}</small>
                                                @endif
                                                <br><small class="badge bg-info">{{ $role->permissions()->count() }} permissions</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if($user->roles->count() > 0)
                    <div class="mb-3">
                        <label class="form-label">Currently Assigned Roles</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($user->roles as $role)
                                <span class="badge bg-gradient-primary">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Roles</button>
    </div>
</form>

