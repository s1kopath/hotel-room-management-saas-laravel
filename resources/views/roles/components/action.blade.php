<div class="dropdown d-inline-block">
    <button class="btn btn-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="material-symbols-rounded">tune</i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a href="{{ route('roles.show', $row->id) }}" class="dropdown-item d-flex align-items-center gap-1">
                <i class="material-symbols-rounded">visibility</i> View
            </a>
        </li>
        @if(auth()->user()->isSuperAdmin() || ($row->scope !== 'system' && $row->created_by === auth()->id()))
        <li>
            <a href="javascript:void(0)" onclick="loadModal('{{ route('roles.edit', $row->id) }}')" 
                class="dropdown-item d-flex align-items-center gap-1">
                <i class="material-symbols-rounded">edit</i> Edit
            </a>
        </li>
        <li>
            <form action="{{ route('roles.destroy', $row->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="dropdown-item d-flex align-items-center gap-1 text-danger" 
                    onclick="return confirm('Are you sure you want to delete this role?')">
                    <i class="material-symbols-rounded">delete</i> Delete
                </button>
            </form>
        </li>
        @endif
    </ul>
</div>

