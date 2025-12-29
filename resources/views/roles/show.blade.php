@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
    <div class="mb-3">
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
            <i class="material-symbols-rounded">arrow_back</i> Back to Roles
        </a>
    </div>

    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">
                    {{ $role->name }}
                    @if($role->scope === 'system')
                        <span class="badge bg-primary">System Role</span>
                    @else
                        <span class="badge bg-info">Custom Role</span>
                    @endif
                </h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="text-uppercase text-dark font-weight-bolder">Role Information</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $role->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Description:</strong></td>
                            <td>{{ $role->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Scope:</strong></td>
                            <td>
                                @if($role->scope === 'system')
                                    <span class="badge bg-primary">System</span>
                                @else
                                    <span class="badge bg-info">Custom (Hotel Owner)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Created By:</strong></td>
                            <td>{{ $role->createdBy ? ($role->createdBy->full_name ?? $role->createdBy->username) : 'System' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Users with this role:</strong></td>
                            <td><span class="badge bg-success">{{ $role->users->count() }} users</span></td>
                        </tr>
                    </table>

                    <h6 class="text-uppercase text-dark font-weight-bolder mt-4">Assigned Permissions</h6>
                    @if($role->permissions->count() > 0)
                    <div class="row">
                        @php
                            $permissionsByCategory = $role->permissions->groupBy('category');
                        @endphp
                        @foreach($permissionsByCategory as $category => $permissions)
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <strong>{{ ucfirst($category) }}</strong>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        @foreach($permissions as $permission)
                                        <li>
                                            <i class="material-symbols-rounded text-success">check_circle</i>
                                            {{ $permission->name }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="material-symbols-rounded">warning</i>
                        No permissions assigned to this role yet.
                    </div>
                    @endif
                </div>
                <div class="col-md-4">
                    <h6 class="text-uppercase text-dark font-weight-bolder">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        @if(auth()->user()->isSuperAdmin() || ($role->scope !== 'system' && $role->created_by === auth()->id()))
                        <a href="javascript:void(0)" onclick="loadModal('{{ route('roles.edit', $role->id) }}')" 
                            class="btn btn-primary">Edit Role</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
@endsection

