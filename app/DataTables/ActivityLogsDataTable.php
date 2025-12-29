<?php

namespace App\DataTables;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ActivityLogsDataTable extends DataTable
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<ActivityLog> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('user_name', function($row) {
                if ($row->user) {
                    return '<a href="' . route('users.show', $row->user_id) . '">' . 
                           ($row->user->full_name ?? $row->user->username) . '</a>';
                }
                return '<span class="text-muted">System</span>';
            })
            ->editColumn('action_type', function($row) {
                $badges = [
                    'login' => '<span class="badge bg-success">Login</span>',
                    'logout' => '<span class="badge bg-secondary">Logout</span>',
                    'create_hotel' => '<span class="badge bg-primary">Create Hotel</span>',
                    'update_hotel' => '<span class="badge bg-info">Update Hotel</span>',
                    'delete_hotel' => '<span class="badge bg-danger">Delete Hotel</span>',
                    'create_room' => '<span class="badge bg-primary">Create Room</span>',
                    'update_room' => '<span class="badge bg-info">Update Room</span>',
                    'change_room_status' => '<span class="badge bg-warning">Change Room Status</span>',
                    'create_guest' => '<span class="badge bg-primary">Create Guest</span>',
                    'update_guest' => '<span class="badge bg-info">Update Guest</span>',
                    'create_reservation' => '<span class="badge bg-primary">Create Reservation</span>',
                    'update_reservation' => '<span class="badge bg-info">Update Reservation</span>',
                    'check_in' => '<span class="badge bg-success">Check In</span>',
                    'check_out' => '<span class="badge bg-success">Check Out</span>',
                    'cancel_reservation' => '<span class="badge bg-danger">Cancel Reservation</span>',
                    'create_role' => '<span class="badge bg-primary">Create Role</span>',
                    'update_role' => '<span class="badge bg-info">Update Role</span>',
                    'delete_role' => '<span class="badge bg-danger">Delete Role</span>',
                ];
                return $badges[$row->action_type] ?? '<span class="badge bg-secondary">' . $row->action_type . '</span>';
            })
            ->editColumn('entity_type', function($row) {
                return $row->entity_type ? ucfirst($row->entity_type) : '--';
            })
            ->editColumn('description', function($row) {
                if ($row->description) {
                    $preview = strlen($row->description) > 60 ? substr($row->description, 0, 60) . '...' : $row->description;
                    return '<span title="' . htmlspecialchars($row->description) . '">' . htmlspecialchars($preview) . '</span>';
                }
                return '--';
            })
            ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y H:i:s'))
            ->rawColumns(['user_name', 'action_type', 'description'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<ActivityLog>
     */
    public function query(ActivityLog $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['user'])
            ->orderBy('created_at', 'desc');

        // Filter by specific user if provided
        if ($this->user_id) {
            $query->where('user_id', $this->user_id);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('activity-logs-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
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
            Column::computed('user_name')->title('User')
                ->searchable(false)
                ->orderable(false),
            Column::make('action_type')->title('Action'),
            Column::make('entity_type')->title('Entity Type'),
            Column::make('entity_id')->title('Entity ID'),
            Column::make('description')->title('Description'),
            Column::make('ip_address')->title('IP Address'),
            Column::make('created_at')->title('Date/Time'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ActivityLogs_' . date('YmdHis');
    }
}

