@extends('layouts.app')

@section('title', 'User Details')

@section('content')
    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">User Details</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-dark font-weight-bolder">Basic Information</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Username:</strong></td>
                            <td>{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Full Name:</strong></td>
                            <td>{{ $user->full_name ?? '--' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $user->phone ?? '--' }}</td>
                        </tr>
                        <tr>
                            <td><strong>User Type:</strong></td>
                            <td>
                                @if($user->user_type == 'super_admin')
                                    <span class="badge bg-danger">Super Admin</span>
                                @elseif($user->user_type == 'hotel_owner')
                                    <span class="badge bg-primary">Hotel Owner</span>
                                @else
                                    <span class="badge bg-info">Staff</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @if($user->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($user->status == 'suspended')
                                    <span class="badge bg-warning">Suspended</span>
                                @else
                                    <span class="badge bg-secondary">Deleted</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-uppercase text-dark font-weight-bolder">Additional Information</h6>
                    <table class="table table-borderless">
                        @if($user->parentUser)
                        <tr>
                            <td><strong>Parent User (Hotel Owner):</strong></td>
                            <td>{{ $user->parentUser->full_name ?? $user->parentUser->username }}</td>
                        </tr>
                        @endif
                        @if($user->createdBy)
                        <tr>
                            <td><strong>Created By:</strong></td>
                            <td>{{ $user->createdBy->full_name ?? $user->createdBy->username }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Created At:</strong></td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Last Login:</strong></td>
                            <td>{{ $user->last_login ? $user->last_login->format('d/m/Y H:i') : 'Never' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($user->roles->count() > 0)
            <div class="mt-4">
                <h6 class="text-uppercase text-dark font-weight-bolder">Assigned Roles</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($user->roles as $role)
                        <span class="badge bg-gradient-primary">{{ $role->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to List</a>
                <a href="javascript:void(0)" onclick="loadModal('{{ route('users.edit', $user->id) }}')" 
                    class="btn btn-primary">Edit User</a>
            </div>
        </div>
    </div>
@endsection

