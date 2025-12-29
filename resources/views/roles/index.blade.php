@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
    <div class="text-end">
        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('roles.create'))
        <button type="button" class="btn bg-gradient-dark bg-brand-secondary mb-0"
            onclick="loadModal('{{ route('roles.create') }}')">
            <i class="material-symbols-rounded text-sm">add</i>&nbsp;&nbsp;Create Role
        </button>
        @endif
    </div>
    <div class="card mt-5">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Roles & Permissions</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="material-symbols-rounded">info</i>
                <strong>System Roles</strong> are created by administrators and cannot be modified.
                <strong>Custom Roles</strong> can be created by hotel owners for their staff.
            </div>
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table align-items-center mb-0', 'style' => 'width: 100%; height: 100%;']) !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush

