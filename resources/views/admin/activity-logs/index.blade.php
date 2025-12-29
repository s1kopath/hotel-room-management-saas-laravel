@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">System Activity Logs</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="material-symbols-rounded">info</i>
                This view shows all user activities across the system for security monitoring and compliance.
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

