<div class="dropdown d-inline-block">
    <button class="btn btn-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="material-symbols-rounded">tune</i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a href="{{ route('hotels.show', $row->id) }}" class="dropdown-item d-flex align-items-center gap-1">
                <i class="material-symbols-rounded">visibility</i> View
            </a>
        </li>
        @hasPermission('hotels.edit-own')
        <li>
            <a href="javascript:void(0)" onclick="loadModal('{{ route('hotels.edit', $row->id) }}')" 
                class="dropdown-item d-flex align-items-center gap-1">
                <i class="material-symbols-rounded">edit</i> Edit
            </a>
        </li>
        @endhasPermission
        @hasPermission('hotels.delete-own')
        <li>
            <a href="javascript:void(0)" onclick="deleteHotel({{ $row->id }})" 
                class="dropdown-item d-flex align-items-center gap-1 text-danger">
                <i class="material-symbols-rounded">delete</i> Archive
            </a>
        </li>
        @endhasPermission
    </ul>
</div>

<script>
function deleteHotel(hotelId) {
    if (confirm('Are you sure you want to archive this hotel? This will set its status to archived.')) {
        fetch(`/hotels/${hotelId}`, {
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
                alert(data.message || 'Error archiving hotel');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error archiving hotel');
        });
    }
}
</script>

