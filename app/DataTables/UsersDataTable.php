<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<User> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('username', fn($row) => $row->username ?? '--')
            ->editColumn('full_name', fn($row) => $row->full_name ?? '--')
            ->editColumn('email', fn($row) => $row->email ?? '--')
            ->editColumn('phone', fn($row) => $row->phone ?? '--')
            ->editColumn('user_type', function ($row) {
                $badges = [
                    'super_admin' => '<span class="badge bg-danger">Super Admin</span>',
                    'hotel_owner' => '<span class="badge bg-primary">Hotel Owner</span>',
                    'staff' => '<span class="badge bg-info">Staff</span>',
                ];
                return $badges[$row->user_type] ?? $row->user_type;
            })
            ->editColumn('status', function ($row) {
                $badges = [
                    'active' => '<span class="badge bg-success">Active</span>',
                    'suspended' => '<span class="badge bg-warning">Suspended</span>',
                    'deleted' => '<span class="badge bg-secondary">Deleted</span>',
                ];
                return $badges[$row->status] ?? $row->status;
            })
            ->editColumn('created_at', fn($row) => date('d/m/Y', strtotime($row->created_at)))
            ->addColumn('action', fn($row) => view('users.components.action', compact('row'))->render())
            ->rawColumns(['user_type', 'status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<User>
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->orderBy('id', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->parameters([
                // 'dom'          => 'Bfrtip',
                // 'buttons'      => ['pageLength','export', 'print'],
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
            Column::make('username')->title('Username'),
            Column::make('full_name')->title('Full Name'),
            Column::make('email'),
            Column::make('phone'),
            Column::make('user_type')->title('User Type'),
            Column::make('status'),
            Column::make('created_at')->title('Joined'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
