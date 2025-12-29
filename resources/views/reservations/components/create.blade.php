<div class="modal-header">
    <h5 class="modal-title text-brand">New Reservation</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" action="{{ route('reservations.store') }}" id="reservationForm">
    @csrf
    <div class="modal-body">
        <div class="mb-3">
            <label for="hotel_id" class="form-label">Select Hotel
                <span class="text-danger">*</span>
            </label>
            <select class="form-select border rounded-3 @error('hotel_id') is-invalid @enderror" 
                id="hotel_id" name="hotel_id" required>
                <option value="">Select a hotel</option>
                @foreach($hotels as $h)
                    <option value="{{ $h->id }}" {{ old('hotel_id', $hotelId) == $h->id ? 'selected' : '' }}>
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
                    value="{{ old('check_in_date') }}" 
                    min="{{ date('Y-m-d') }}" required>
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
                    value="{{ old('check_out_date') }}" 
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
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
                <option value="">First select hotel and dates</option>
            </select>
            <small class="text-muted">Available rooms will load after selecting hotel and dates.</small>
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
                    <option value="{{ $g->id }}" {{ old('guest_id', $guestId) == $g->id ? 'selected' : '' }}>
                        {{ $g->full_name }} {{ $g->email ? '(' . $g->email . ')' : '' }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">
                <a href="javascript:void(0)" onclick="loadModal('{{ route('guests.create') }}')">Add new guest</a>
            </small>
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
                    value="{{ old('number_of_guests', 1) }}" 
                    min="1" required>
                @error('number_of_guests')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="number" step="0.01" class="form-control border rounded-3 @error('total_amount') is-invalid @enderror" 
                    id="total_amount" name="total_amount" 
                    value="{{ old('total_amount', 0) }}" 
                    min="0">
                @error('total_amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="special_requests" class="form-label">Special Requests</label>
            <textarea class="form-control border rounded-3 @error('special_requests') is-invalid @enderror" 
                id="special_requests" name="special_requests" rows="2" 
                placeholder="Guest special requests">{{ old('special_requests') }}</textarea>
            @error('special_requests')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Internal Notes</label>
            <textarea class="form-control border rounded-3 @error('notes') is-invalid @enderror" 
                id="notes" name="notes" rows="2" 
                placeholder="Internal notes">{{ old('notes') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Reservation</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hotelSelect = document.getElementById('hotel_id');
    const checkInDate = document.getElementById('check_in_date');
    const checkOutDate = document.getElementById('check_out_date');
    const roomSelect = document.getElementById('room_id');

    function loadAvailableRooms() {
        const hotelId = hotelSelect.value;
        const checkIn = checkInDate.value;
        const checkOut = checkOutDate.value;

        if (!hotelId || !checkIn || !checkOut) {
            roomSelect.innerHTML = '<option value="">First select hotel and dates</option>';
            return;
        }

        if (new Date(checkOut) <= new Date(checkIn)) {
            roomSelect.innerHTML = '<option value="">Check-out date must be after check-in date</option>';
            return;
        }

        roomSelect.innerHTML = '<option value="">Loading available rooms...</option>';
        roomSelect.disabled = true;

        fetch(`{{ route('reservations.available-rooms') }}?hotel_id=${hotelId}&check_in_date=${checkIn}&check_out_date=${checkOut}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            roomSelect.innerHTML = '<option value="">Select a room</option>';
            if (data.rooms && data.rooms.length > 0) {
                data.rooms.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.id;
                    option.textContent = `${room.room_number}${room.room_type ? ' - ' + room.room_type : ''}${room.capacity ? ' (Capacity: ' + room.capacity + ')' : ''}`;
                    if (room.id == {{ $roomId ?? 'null' }}) {
                        option.selected = true;
                    }
                    roomSelect.appendChild(option);
                });
            } else {
                roomSelect.innerHTML = '<option value="">No available rooms for selected dates</option>';
            }
            roomSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            roomSelect.innerHTML = '<option value="">Error loading rooms</option>';
            roomSelect.disabled = false;
        });
    }

    hotelSelect.addEventListener('change', loadAvailableRooms);
    checkInDate.addEventListener('change', function() {
        if (checkOutDate.value) {
            checkOutDate.min = new Date(new Date(checkInDate.value).getTime() + 86400000).toISOString().split('T')[0];
            loadAvailableRooms();
        }
    });
    checkOutDate.addEventListener('change', loadAvailableRooms);

    // Load rooms if hotel and dates are pre-selected
    @if($hotelId && $roomId)
        loadAvailableRooms();
    @endif
});
</script>

