@extends('layouts.app')

@section('title', 'Room Details')

@section('content')
    <div class="mb-3">
        <a href="{{ route('rooms.index', ['hotel_id' => $room->hotel_id]) }}" class="btn btn-secondary">
            <i class="material-symbols-rounded">arrow_back</i> Back to Rooms
        </a>
    </div>

    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Room Details - {{ $room->room_number }}</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="mb-3">Room {{ $room->room_number }}</h4>
                    
                    @if($room->images->count() > 0)
                    <div class="mb-4">
                        <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($room->images as $index => $image)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $image->image_url) }}" 
                                            class="d-block w-100" alt="Room Image" 
                                            style="height: 400px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                            @if($room->images->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                            @endif
                        </div>
                    </div>
                    @endif

                    <h6 class="text-uppercase text-dark font-weight-bolder">Room Information</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Room Number:</strong></td>
                            <td>{{ $room->room_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Hotel:</strong></td>
                            <td>
                                <a href="{{ route('hotels.show', $room->hotel_id) }}">{{ $room->hotel->name }}</a>
                            </td>
                        </tr>
                        @if($room->room_type)
                        <tr>
                            <td><strong>Room Type:</strong></td>
                            <td>{{ $room->room_type }}</td>
                        </tr>
                        @endif
                        @if($room->floor_number)
                        <tr>
                            <td><strong>Floor:</strong></td>
                            <td>{{ $room->floor_number }}</td>
                        </tr>
                        @endif
                        @if($room->capacity)
                        <tr>
                            <td><strong>Capacity:</strong></td>
                            <td>{{ $room->capacity }} guest(s)</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @if($room->status == 'vacant')
                                    <span class="badge bg-success">🟢 Vacant</span>
                                @elseif($room->status == 'reserved')
                                    <span class="badge bg-warning">🟡 Reserved</span>
                                @elseif($room->status == 'occupied')
                                    <span class="badge bg-danger">🔴 Occupied</span>
                                @else
                                    <span class="badge bg-primary">🔵 Admin Reserved</span>
                                @endif
                            </td>
                        </tr>
                        @if($room->description)
                        <tr>
                            <td><strong>Description:</strong></td>
                            <td>{{ $room->description }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Last Status Change:</strong></td>
                            <td>{{ $room->last_status_change ? $room->last_status_change->format('d/m/Y H:i') : '--' }}</td>
                        </tr>
                        @if($room->statusUpdatedBy)
                        <tr>
                            <td><strong>Status Updated By:</strong></td>
                            <td>{{ $room->statusUpdatedBy->full_name ?? $room->statusUpdatedBy->username }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-4">
                    <h6 class="text-uppercase text-dark font-weight-bolder">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        @hasPermission('rooms.edit')
                        <a href="javascript:void(0)" onclick="loadModal('{{ route('rooms.edit', $room->id) }}')" 
                            class="btn btn-primary">Edit Room</a>
                        @endhasPermission
                        @hasPermission('rooms.change-status')
                        <a href="javascript:void(0)" onclick="changeRoomStatus({{ $room->id }}, '{{ $room->status }}')" 
                            class="btn btn-info">Change Status</a>
                        @endhasPermission
                    </div>

                    <h6 class="text-uppercase text-dark font-weight-bolder mt-4">Status History</h6>
                    @if($room->statusHistory->count() > 0)
                    <div class="list-group">
                        @foreach($room->statusHistory->take(10) as $history)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        @if($history->previous_status)
                                            {{ ucfirst($history->previous_status) }} → {{ ucfirst($history->new_status) }}
                                        @else
                                            Created as {{ ucfirst($history->new_status) }}
                                        @endif
                                    </h6>
                                    <small>{{ $history->changed_at->format('d/m/Y H:i') }}</small>
                                </div>
                                @if($history->changedBy)
                                <p class="mb-1"><small>By: {{ $history->changedBy->full_name ?? $history->changedBy->username }}</small></p>
                                @endif
                                @if($history->notes)
                                <p class="mb-0"><small class="text-muted">{{ $history->notes }}</small></p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted">No status history available.</p>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('rooms.index', ['hotel_id' => $room->hotel_id]) }}" class="btn btn-secondary">Back to Rooms</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function changeRoomStatus(roomId, currentStatus) {
    const statusOptions = {
        'vacant': '🟢 Vacant',
        'reserved': '🟡 Reserved',
        'occupied': '🔴 Occupied',
        'admin_reserved': '🔵 Admin Reserved'
    };

    let statusHtml = '<select id="new_status" class="form-select mb-3" required>';
    for (const [value, label] of Object.entries(statusOptions)) {
        if (value !== currentStatus) {
            statusHtml += `<option value="${value}">${label}</option>`;
        }
    }
    statusHtml += '</select>';

    const notesHtml = '<textarea id="status_notes" class="form-control" rows="3" placeholder="Optional notes for status change"></textarea>';

    const modalContent = `
        <div class="modal-header">
            <h5 class="modal-title">Change Room Status</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="statusChangeForm">
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Current Status</label>
                    <input type="text" class="form-control" value="${statusOptions[currentStatus]}" disabled>
                </div>
                <div class="mb-3">
                    <label for="new_status" class="form-label">New Status <span class="text-danger">*</span></label>
                    ${statusHtml}
                </div>
                <div class="mb-3">
                    <label for="status_notes" class="form-label">Notes</label>
                    ${notesHtml}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Change Status</button>
            </div>
        </form>
    `;

    // Create and show modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                ${modalContent}
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    // Handle form submission
    document.getElementById('statusChangeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const newStatus = document.getElementById('new_status').value;
        const notes = document.getElementById('status_notes').value;

        fetch(`/rooms/${roomId}/change-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: newStatus,
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bsModal.hide();
                location.reload();
            } else {
                alert(data.message || 'Error changing status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error changing status');
        });
    });

    // Remove modal from DOM when hidden
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}
</script>
@endpush

