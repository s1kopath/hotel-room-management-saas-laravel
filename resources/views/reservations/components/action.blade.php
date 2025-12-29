<div class="dropdown d-inline-block">
    <button class="btn btn-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="material-symbols-rounded">tune</i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a href="{{ route('reservations.show', $row->id) }}" class="dropdown-item d-flex align-items-center gap-1">
                <i class="material-symbols-rounded">visibility</i> View
            </a>
        </li>
        @hasPermission('reservations.edit')
        @if(!$row->isAdminOverride() || auth()->user()->isSuperAdmin())
        <li>
            <a href="javascript:void(0)" onclick="loadModal('{{ route('reservations.edit', $row->id) }}')" 
                class="dropdown-item d-flex align-items-center gap-1">
                <i class="material-symbols-rounded">edit</i> Edit
            </a>
        </li>
        @endif
        @endhasPermission
        @hasPermission('reservations.check-in')
        @if($row->status == 'confirmed')
        <li>
            <form action="{{ route('reservations.check-in', $row->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="dropdown-item d-flex align-items-center gap-1 text-success" 
                    onclick="return confirm('Check in guest?')">
                    <i class="material-symbols-rounded">login</i> Check In
                </button>
            </form>
        </li>
        @endif
        @endhasPermission
        @hasPermission('reservations.check-out')
        @if($row->status == 'checked_in')
        <li>
            <form action="{{ route('reservations.check-out', $row->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="dropdown-item d-flex align-items-center gap-1 text-info" 
                    onclick="return confirm('Check out guest?')">
                    <i class="material-symbols-rounded">logout</i> Check Out
                </button>
            </form>
        </li>
        @endif
        @endhasPermission
        @hasPermission('reservations.cancel')
        @if(!$row->isAdminOverride() || auth()->user()->isSuperAdmin())
        @if(!in_array($row->status, ['checked_out', 'cancelled']))
        <li>
            <a href="javascript:void(0)" onclick="cancelReservation({{ $row->id }})" 
                class="dropdown-item d-flex align-items-center gap-1 text-danger">
                <i class="material-symbols-rounded">cancel</i> Cancel
            </a>
        </li>
        @endif
        @endif
        @endhasPermission
    </ul>
</div>

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

