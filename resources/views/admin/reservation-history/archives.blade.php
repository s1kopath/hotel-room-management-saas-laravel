@extends('layouts.app')

@section('title', 'Archived Admin Reservation History')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.reservation-history.index') }}" class="btn btn-secondary">
            <i class="material-symbols-rounded">arrow_back</i> Back to History
        </a>
    </div>
    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Archived Admin Reservation History</h6>
            </div>
        </div>
        <div class="card-body">
            @if(count($archiveData) > 0)
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Month</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Records</th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archiveData as $archive)
                        <tr>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">{{ \Carbon\Carbon::parse($archive['month'])->format('F Y') }}</h6>
                                        <p class="text-xs text-secondary mb-0">{{ $archive['month'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $archive['count'] }} records</span>
                            </td>
                            <td class="align-middle">
                                <a href="{{ route('admin.reservation-history.archive.view', $archive['month']) }}" 
                                    class="btn btn-sm btn-primary">
                                    <i class="material-symbols-rounded">visibility</i> View
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="clearArchive('{{ $archive['month'] }}')">
                                    <i class="material-symbols-rounded">delete</i> Clear
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info">
                <i class="material-symbols-rounded">info</i>
                No archived records found.
            </div>
            @endif
        </div>
    </div>

    <!-- Clear Archive Form -->
    <form id="clearArchiveForm" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="confirm" value="1">
    </form>
@endsection

@push('scripts')
<script>
function clearArchive(month) {
    if (confirm(`Are you sure you want to permanently delete all archived records for ${month}? This action cannot be undone!`)) {
        const form = document.getElementById('clearArchiveForm');
        form.action = `/admin/reservation-history/archive/${month}/clear`;
        form.submit();
    }
}
</script>
@endpush

