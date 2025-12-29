@extends('layouts.app')

@section('title', 'Reservation Details')

@section('content')
    <div class="mb-3">
        <a href="{{ route('reservations.index') }}" class="btn btn-secondary">
            <i class="material-symbols-rounded">arrow_back</i> Back to Reservations
        </a>
    </div>

    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">
                    Reservation Details - {{ $reservation->reservation_number }}
                    @if($reservation->isAdminOverride())
                        <span class="badge bg-primary">🔵 Admin Override</span>
                    @endif
                </h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="mb-3">Reservation #{{ $reservation->reservation_number }}</h4>

                    <h6 class="text-uppercase text-dark font-weight-bolder">Reservation Information</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @if($reservation->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($reservation->status == 'confirmed')
                                    <span class="badge bg-info">Confirmed</span>
                                @elseif($reservation->status == 'checked_in')
                                    <span class="badge bg-success">Checked In</span>
                                @elseif($reservation->status == 'checked_out')
                                    <span class="badge bg-secondary">Checked Out</span>
                                @elseif($reservation->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-dark">No Show</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Hotel:</strong></td>
                            <td>
                                <a href="{{ route('hotels.show', $reservation->hotel_id) }}">
                                    {{ $reservation->hotel->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Room:</strong></td>
                            <td>
                                <a href="{{ route('rooms.show', $reservation->room_id) }}">
                                    {{ $reservation->room->room_number }}
                                </a>
                                @if($reservation->room->room_type)
                                    ({{ $reservation->room->room_type }})
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Guest:</strong></td>
                            <td>
                                <a href="{{ route('guests.show', $reservation->guest_id) }}">
                                    {{ $reservation->guest->full_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Check In:</strong></td>
                            <td>{{ $reservation->check_in_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Check Out:</strong></td>
                            <td>{{ $reservation->check_out_date->format('d/m/Y') }}</td>
                        </tr>
                        @if($reservation->actual_check_in)
                        <tr>
                            <td><strong>Actual Check In:</strong></td>
                            <td>{{ $reservation->actual_check_in->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                        @if($reservation->actual_check_out)
                        <tr>
                            <td><strong>Actual Check Out:</strong></td>
                            <td>{{ $reservation->actual_check_out->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Number of Guests:</strong></td>
                            <td>{{ $reservation->number_of_guests }}</td>
                        </tr>
                        <tr>
                            <td><strong>Reservation Type:</strong></td>
                            <td>
                                @if($reservation->reservation_type == 'admin_override')
                                    <span class="badge bg-primary">Admin Override</span>
                                @else
                                    <span class="badge bg-info">Regular</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <h6 class="text-uppercase text-dark font-weight-bolder mt-4">Payment Information</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Payment Status:</strong></td>
                            <td>
                                @if($reservation->payment_status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($reservation->payment_status == 'partial')
                                    <span class="badge bg-info">Partial</span>
                                @elseif($reservation->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-danger">Refunded</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Total Amount:</strong></td>
                            <td>${{ number_format($reservation->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Paid Amount:</strong></td>
                            <td>${{ number_format($reservation->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Remaining Amount:</strong></td>
                            <td>${{ number_format($reservation->remaining_amount, 2) }}</td>
                        </tr>
                    </table>

                    @if($reservation->special_requests)
                    <h6 class="text-uppercase text-dark font-weight-bolder mt-4">Special Requests</h6>
                    <p>{{ $reservation->special_requests }}</p>
                    @endif

                    @if($reservation->notes)
                    <h6 class="text-uppercase text-dark font-weight-bolder mt-4">Internal Notes</h6>
                    <p>{{ $reservation->notes }}</p>
                    @endif

                    @if($reservation->cancelled_at)
                    <h6 class="text-uppercase text-dark font-weight-bolder mt-4">Cancellation Information</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Cancelled At:</strong></td>
                            <td>{{ $reservation->cancelled_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($reservation->cancelledBy)
                        <tr>
                            <td><strong>Cancelled By:</strong></td>
                            <td>{{ $reservation->cancelledBy->full_name ?? $reservation->cancelledBy->username }}</td>
                        </tr>
                        @endif
                        @if($reservation->cancellation_reason)
                        <tr>
                            <td><strong>Reason:</strong></td>
                            <td>{{ $reservation->cancellation_reason }}</td>
                        </tr>
                        @endif
                    </table>
                    @endif
                </div>
                <div class="col-md-4">
                    <h6 class="text-uppercase text-dark font-weight-bolder">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        @hasPermission('reservations.edit')
                        @if(!$reservation->isAdminOverride() || auth()->user()->isSuperAdmin())
                        <a href="javascript:void(0)" onclick="loadModal('{{ route('reservations.edit', $reservation->id) }}')" 
                            class="btn btn-primary">Edit Reservation</a>
                        @endif
                        @endhasPermission
                        @hasPermission('reservations.check-in')
                        @if($reservation->status == 'confirmed')
                        <form action="{{ route('reservations.check-in', $reservation->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" 
                                onclick="return confirm('Check in guest?')">
                                Check In Guest
                            </button>
                        </form>
                        @endif
                        @endhasPermission
                        @hasPermission('reservations.check-out')
                        @if($reservation->status == 'checked_in')
                        <form action="{{ route('reservations.check-out', $reservation->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-info w-100" 
                                onclick="return confirm('Check out guest?')">
                                Check Out Guest
                            </button>
                        </form>
                        @endif
                        @endhasPermission
                        @hasPermission('reservations.cancel')
                        @if(!$reservation->isAdminOverride() || auth()->user()->isSuperAdmin())
                        @if(!in_array($reservation->status, ['checked_out', 'cancelled']))
                        <a href="javascript:void(0)" onclick="cancelReservation({{ $reservation->id }})" 
                            class="btn btn-danger">Cancel Reservation</a>
                        @endif
                        @endif
                        @endhasPermission
                    </div>

                    <h6 class="text-uppercase text-dark font-weight-bolder mt-4">Reservation Details</h6>
                    <table class="table table-borderless">
                        @if($reservation->createdBy)
                        <tr>
                            <td><strong>Created By:</strong></td>
                            <td>{{ $reservation->createdBy->full_name ?? $reservation->createdBy->username }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Created At:</strong></td>
                            <td>{{ $reservation->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Last Updated:</strong></td>
                            <td>{{ $reservation->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function cancelReservation(reservationId) {
    const reason = prompt('Please enter cancellation reason (optional):');
    if (reason === null) return; // User cancelled

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/reservations/${reservationId}/cancel`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);

    if (reason) {
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'cancellation_reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);
    }

    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush

