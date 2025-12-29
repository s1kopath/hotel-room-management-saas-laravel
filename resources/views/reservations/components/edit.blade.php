<div class="modal-header">
    <h5 class="modal-title text-brand">Edit Reservation</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" action="{{ route('reservations.update', $reservation->id) }}" id="reservationEditForm">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Reservation Number</label>
            <input type="text" class="form-control" value="{{ $reservation->reservation_number }}" disabled>
        </div>

        <div class="mb-3">
            <label for="hotel_id" class="form-label">Select Hotel
                <span class="text-danger">*</span>
            </label>
            <select class="form-select border rounded-3 @error('hotel_id') is-invalid @enderror" 
                id="hotel_id" name="hotel_id" required>
                <option value="">Select a hotel</option>
                @foreach($hotels as $h)
                    <option value="{{ $h->id }}" {{ old('hotel_id', $reservation->hotel_id) == $h->id ? 'selected' : '' }}>
                        {{ $h->name }} ({{ $h->city ?? 'N/A' }})
                    </option>
                @endforeach
            </select>
            @error('hotel_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="check_in_date" class="form-label">Check In Date
                    <span class="text-danger">*</span>
                </label>
                <input type="date" class="form-control border rounded-3 @error('check_in_date') is-invalid @enderror" 
                    id="check_in_date" name="check_in_date" 
                    value="{{ old('check_in_date', $reservation->check_in_date->format('Y-m-d')) }}" required>
                @error('check_in_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="check_out_date" class="form-label">Check Out Date
                    <span class="text-danger">*</span>
                </label>
                <input type="date" class="form-control border rounded-3 @error('check_out_date') is-invalid @enderror" 
                    id="check_out_date" name="check_out_date" 
                    value="{{ old('check_out_date', $reservation->check_out_date->format('Y-m-d')) }}" required>
                @error('check_out_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="room_id" class="form-label">Select Room
                <span class="text-danger">*</span>
            </label>
            <select class="form-select border rounded-3 @error('room_id') is-invalid @enderror" 
                id="room_id" name="room_id" required>
                <option value="">Loading rooms...</option>
                @foreach($rooms as $r)
                    <option value="{{ $r->id }}" {{ old('room_id', $reservation->room_id) == $r->id ? 'selected' : '' }}>
                        {{ $r->room_number }}{{ $r->room_type ? ' - ' . $r->room_type : '' }}
                    </option>
                @endforeach
            </select>
            @error('room_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="guest_id" class="form-label">Select Guest
                <span class="text-danger">*</span>
            </label>
            <select class="form-select border rounded-3 @error('guest_id') is-invalid @enderror" 
                id="guest_id" name="guest_id" required>
                <option value="">Select a guest</option>
                @foreach($guests as $g)
                    <option value="{{ $g->id }}" {{ old('guest_id', $reservation->guest_id) == $g->id ? 'selected' : '' }}>
                        {{ $g->full_name }} {{ $g->email ? '(' . $g->email . ')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('guest_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="number_of_guests" class="form-label">Number of Guests
                    <span class="text-danger">*</span>
                </label>
                <input type="number" class="form-control border rounded-3 @error('number_of_guests') is-invalid @enderror" 
                    id="number_of_guests" name="number_of_guests" 
                    value="{{ old('number_of_guests', $reservation->number_of_guests) }}" 
                    min="1" required>
                @error('number_of_guests')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status
                    <span class="text-danger">*</span>
                </label>
                <select class="form-select border rounded-3 @error('status') is-invalid @enderror" 
                    id="status" name="status" required>
                    <option value="pending" {{ old('status', $reservation->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ old('status', $reservation->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="checked_in" {{ old('status', $reservation->status) == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="checked_out" {{ old('status', $reservation->status) == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                    <option value="cancelled" {{ old('status', $reservation->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="no_show" {{ old('status', $reservation->status) == 'no_show' ? 'selected' : '' }}>No Show</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="payment_status" class="form-label">Payment Status
                    <span class="text-danger">*</span>
                </label>
                <select class="form-select border rounded-3 @error('payment_status') is-invalid @enderror" 
                    id="payment_status" name="payment_status" required>
                    <option value="pending" {{ old('payment_status', $reservation->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ old('payment_status', $reservation->payment_status) == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ old('payment_status', $reservation->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="refunded" {{ old('payment_status', $reservation->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
                @error('payment_status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="number" step="0.01" class="form-control border rounded-3 @error('total_amount') is-invalid @enderror" 
                    id="total_amount" name="total_amount" 
                    value="{{ old('total_amount', $reservation->total_amount) }}" 
                    min="0">
                @error('total_amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="paid_amount" class="form-label">Paid Amount</label>
            <input type="number" step="0.01" class="form-control border rounded-3 @error('paid_amount') is-invalid @enderror" 
                id="paid_amount" name="paid_amount" 
                value="{{ old('paid_amount', $reservation->paid_amount) }}" 
                min="0">
            @error('paid_amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if($reservation->total_amount > 0)
            <small class="text-muted">
                Remaining: {{ number_format($reservation->total_amount - $reservation->paid_amount, 2) }}
            </small>
            @endif
        </div>

        <div class="mb-3">
            <label for="special_requests" class="form-label">Special Requests</label>
            <textarea class="form-control border rounded-3 @error('special_requests') is-invalid @enderror" 
                id="special_requests" name="special_requests" rows="2" 
                placeholder="Guest special requests">{{ old('special_requests', $reservation->special_requests) }}</textarea>
            @error('special_requests')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Internal Notes</label>
            <textarea class="form-control border rounded-3 @error('notes') is-invalid @enderror" 
                id="notes" name="notes" rows="2" 
                placeholder="Internal notes">{{ old('notes', $reservation->notes) }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Reservation</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hotelSelect = document.getElementById('hotel_id');
    const roomSelect = document.getElementById('room_id');

    hotelSelect.addEventListener('change', function() {
        const hotelId = hotelSelect.value;
        if (hotelId) {
            // Reload page with new hotel to get updated rooms
            // Or fetch rooms via AJAX
            fetch(`/rooms?hotel_id=${hotelId}`)
                .then(response => response.text())
                .then(html => {
                    // This is a simple approach - in production, use AJAX to fetch rooms
                    location.reload();
                });
        }
    });
});
</script>

