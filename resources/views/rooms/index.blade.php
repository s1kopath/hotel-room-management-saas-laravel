@extends('layouts.app')

@section('title', isset($hotel) ? $hotel->name . ' - Rooms' : 'Rooms')

@section('content')
    @if(isset($hotel))
    <div class="mb-3">
        <a href="{{ route('hotels.show', $hotel->id) }}" class="btn btn-secondary">
            <i class="material-symbols-rounded">arrow_back</i> Back to Hotel
        </a>
    </div>
    @endif

    <div class="text-end">
        @hasPermission('rooms.create')
        <button type="button" class="btn bg-gradient-dark bg-brand-secondary mb-0"
            onclick="loadModal('{{ route('rooms.create', isset($hotel) ? ['hotel_id' => $hotel->id] : []) }}')">
            <i class="material-symbols-rounded text-sm">add</i>&nbsp;&nbsp;Add New Room
        </button>
        @endhasPermission
    </div>
    <div class="card mt-5">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">
                    {{ isset($hotel) ? $hotel->name . ' - Rooms' : 'All Rooms' }}
                </h6>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table align-items-center mb-0', 'style' => 'width: 100%; height: 100%;']) !!}
            </div>
        </div>
    </div>

    <!-- Status Legend -->
    <div class="card mt-4">
        <div class="card-body">
            <h6 class="mb-3">Status Legend</h6>
            <div class="row">
                <div class="col-md-3">
                    <span class="badge bg-success">🟢 Vacant</span> - Available for booking
                </div>
                <div class="col-md-3">
                    <span class="badge bg-warning">🟡 Reserved</span> - Reserved by guest
                </div>
                <div class="col-md-3">
                    <span class="badge bg-danger">🔴 Occupied</span> - Guest checked in
                </div>
                <div class="col-md-3">
                    <span class="badge bg-primary">🔵 Admin Reserved</span> - Reserved by admin
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush

