@extends('layouts.app')

@section('title', 'Reservations')

@section('content')
    <div class="text-end">
        @hasPermission('reservations.create')
        <button type="button" class="btn bg-gradient-dark bg-brand-secondary mb-0"
            onclick="loadModal('{{ route('reservations.create') }}')">
            <i class="material-symbols-rounded text-sm">add</i>&nbsp;&nbsp;New Reservation
        </button>
        @endhasPermission
        @if(auth()->user()->isSuperAdmin())
        <button type="button" class="btn bg-primary mb-0"
            onclick="loadModal('{{ route('reservations.admin-override.create') }}')">
            <i class="material-symbols-rounded text-sm">admin_panel_settings</i>&nbsp;&nbsp;Admin Override
        </button>
        @endif
    </div>
    <div class="card mt-5">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Reservations</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table align-items-center mb-0', 'style' => 'width: 100%; height: 100%;']) !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
