@extends('layouts.app')

@section('title', 'Admin Reservation History')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('admin.reservation-history.archives') }}" class="btn btn-info">
            <i class="material-symbols-rounded text-sm">archive</i>&nbsp;&nbsp;View Archives
        </a>
        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#archiveModal">
            <i class="material-symbols-rounded text-sm">archive</i>&nbsp;&nbsp;Archive Month
        </button>
    </div>
    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Admin Reservation History (Last 30 Days)</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="material-symbols-rounded">info</i>
                This view shows only the last 30 days of admin override reservation actions. Older records can be archived.
            </div>
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table align-items-center mb-0', 'style' => 'width: 100%; height: 100%;']) !!}
            </div>
        </div>
    </div>

    <!-- Archive Modal -->
    <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.reservation-history.archive') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="archiveModalLabel">Archive Admin Reservation History</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> Archiving will move records older than 30 days to the archive. This action cannot be undone easily.
                        </div>
                        <div class="mb-3">
                            <label for="archive_month" class="form-label">Select Month to Archive
                                <span class="text-danger">*</span>
                            </label>
                            <input type="month" class="form-control @error('archive_month') is-invalid @enderror" 
                                id="archive_month" name="archive_month" 
                                value="{{ old('archive_month') }}" 
                                max="{{ now()->subMonth()->format('Y-m') }}" required>
                            <small class="text-muted">Select a month to archive (must be at least 1 month old)</small>
                            @error('archive_month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Archive Month</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush

