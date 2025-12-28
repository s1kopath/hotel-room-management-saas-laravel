<div class="dropdown d-inline-block">
    <button class="btn btn-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="material-symbols-rounded">tune</i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a href="{{ route('guests.show', $row->id) }}" class="dropdown-item d-flex align-items-center gap-1">
                <i class="material-symbols-rounded">visibility</i> View
            </a>
        </li>
        @hasPermission('guests.edit')
        <li>
            <a href="javascript:void(0)" onclick="loadModal('{{ route('guests.edit', $row->id) }}')" 
                class="dropdown-item d-flex align-items-center gap-1">
                <i class="material-symbols-rounded">edit</i> Edit
            </a>
        </li>
        @endhasPermission
        @hasPermission('guests.delete')
        <li>
            <a href="javascript:void(0)" onclick="deleteGuest({{ $row->id }})" 
                class="dropdown-item d-flex align-items-center gap-1 text-danger">
                <i class="material-symbols-rounded">delete</i> Delete
            </a>
        </li>
        @endhasPermission
    </ul>
</div>

<script>
function deleteGuest(guestId) {
    if (confirm('Are you sure you want to delete this guest? This action cannot be undone.')) {
        fetch(`/guests/${guestId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || response.ok) {
                location.reload();
            } else {
                alert(data.message || 'Error deleting guest');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting guest');
        });
    }
}
</script>

