<?php

namespace App\DataTables;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RolesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Role> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function ($row) {
                $badge = '';
                if ($row->scope === 'system') {
                    $badge = '<span class="badge bg-primary">System</span> ';
                } elseif ($row->scope === 'hotel_owner') {
                    $badge = '<span class="badge bg-info">Custom</span> ';
                }
                return $badge . '<a href="' . route('roles.show', $row->id) . '">' . $row->name . '</a>';
            })
            ->addColumn('permissions_count', fn($row) => $row->permissions_count ?? 0)
            ->addColumn('users_count', fn($row) => $row->users_count ?? 0)
            ->addColumn('creator_name', function ($row) {
                return $row->createdBy ? ($row->createdBy->full_name ?? $row->createdBy->username) : '--';
            })
            ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y'))
            ->addColumn('action', fn($row) => view('roles.components.action', compact('row'))->render())
            ->rawColumns(['name', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Role>
     */
    public function query(Role $model): QueryBuilder
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with(['createdBy'])
            ->withCount(['permissions', 'users']);

        // Super admin sees all roles
        if ($user->isSuperAdmin()) {
            return $query->orderBy('scope')->orderBy('name');
        }

        // Hotel owners see system roles and their own custom roles
        if ($user->isHotelOwner()) {
            return $query->where(function ($q) use ($user) {
                $q->where('scope', 'system')
                    ->orWhere(function ($subQ) use ($user) {
                        $subQ->where('scope', 'hotel_owner')
                            ->where('created_by', $user->id);
                    });
            })->orderBy('scope')->orderBy('name');
        }

        // Staff shouldn't normally access this, but just in case
        return $query->where('scope', 'system')->orderBy('name');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('roles-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->parameters([
                'scrollX' => true,
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('Sl')
                ->searchable(false)
                ->orderable(false)
                ->width(40)
                ->addClass('text-center'),
            Column::make('name')->title('Role Name'),
            Column::make('description')->title('Description'),
            Column::computed('permissions_count')->title('Permissions')
                ->searchable(false)
                ->orderable(false),
            Column::computed('users_count')->title('Users')
                ->searchable(false)
                ->orderable(false),
            Column::computed('creator_name')->title('Created By')
                ->searchable(false)
                ->orderable(false),
            Column::make('created_at')->title('Created'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Roles_' . date('YmdHis');
    }
}
