<div class="dropdown d-inline-block">
    <button class="btn btn-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="material-symbols-rounded">tune</i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a href="{{ route('rooms.show', $row->id) }}" class="dropdown-item d-flex align-items-center gap-1">
                <i class="material-symbols-rounded">visibility</i> View
            </a>
        </li>
        @hasPermission('rooms.edit')
        <li>
            <a href="javascript:void(0)" onclick="loadModal('{{ route('rooms.edit', $row->id) }}')" 
                class="dropdown-item d-flex align-items-center gap-1">
                <i class="material-symbols-rounded">edit</i> Edit
            </a>
        </li>
        @endhasPermission
        @hasPermission('rooms.change-status')
        <li>
            <a href="javascript:void(0)" onclick="changeRoomStatus({{ $row->id }}, '{{ $row->status }}')" 
                class="dropdown-item d-flex align-items-center gap-1">
                <i class="material-symbols-rounded">swap_horiz</i> Change Status
            </a>
        </li>
        @endhasPermission
        @hasPermission('rooms.delete')
        <li>
            <a href="javascript:void(0)" onclick="deleteRoom({{ $row->id }})" 
                class="dropdown-item d-flex align-items-center gap-1 text-danger">
                <i class="material-symbols-rounded">delete</i> Delete
            </a>
        </li>
        @endhasPermission
    </ul>
</div>

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

function deleteRoom(roomId) {
    if (confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
        fetch(`/rooms/${roomId}`, {
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
                alert(data.message || 'Error deleting room');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting room');
        });
    }
}
</script>

