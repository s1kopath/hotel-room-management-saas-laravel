<?php

namespace App\DataTables;

use App\Models\AdminReservationHistory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AdminReservationHistoryDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<AdminReservationHistory> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('reservation_number', function($row) {
                if ($row->reservation) {
                    return '<a href="' . route('reservations.show', $row->reservation_id) . '">' . 
                           $row->reservation->reservation_number . '</a>';
                }
                return '--';
            })
            ->addColumn('hotel_name', function($row) {
                return $row->reservation && $row->reservation->hotel 
                    ? $row->reservation->hotel->name 
                    : '--';
            })
            ->addColumn('room_number', function($row) {
                return $row->reservation && $row->reservation->room 
                    ? $row->reservation->room->room_number 
                    : '--';
            })
            ->addColumn('guest_name', function($row) {
                return $row->reservation && $row->reservation->guest 
                    ? $row->reservation->guest->full_name 
                    : '--';
            })
            ->editColumn('action_type', function($row) {
                $badges = [
                    'created' => '<span class="badge bg-success">Created</span>',
                    'modified' => '<span class="badge bg-info">Modified</span>',
                    'released' => '<span class="badge bg-warning">Released</span>',
                ];
                return $badges[$row->action_type] ?? $row->action_type;
            })
            ->editColumn('admin_name', function($row) {
                return $row->admin ? $row->admin->full_name ?? $row->admin->username : '--';
            })
            ->editColumn('action_at', fn($row) => $row->action_at->format('d/m/Y H:i'))
            ->addColumn('notes_preview', function($row) {
                if ($row->notes) {
                    $preview = strlen($row->notes) > 50 ? substr($row->notes, 0, 50) . '...' : $row->notes;
                    return '<span title="' . htmlspecialchars($row->notes) . '">' . htmlspecialchars($preview) . '</span>';
                }
                return '--';
            })
            ->rawColumns(['reservation_number', 'action_type', 'notes_preview'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<AdminReservationHistory>
     */
    public function query(AdminReservationHistory $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['reservation.hotel', 'reservation.room', 'reservation.guest', 'admin'])
            ->whereNull('archive_month') // Only show non-archived records
            ->last30Days() // Only last 30 days
            ->orderBy('action_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('admin-reservation-history-table')
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
            Column::make('reservation_number')->title('Reservation #')
                ->searchable(false)
                ->orderable(false),
            Column::make('hotel_name')->title('Hotel')
                ->searchable(false)
                ->orderable(false),
            Column::make('room_number')->title('Room')
                ->searchable(false)
                ->orderable(false),
            Column::make('guest_name')->title('Guest')
                ->searchable(false)
                ->orderable(false),
            Column::make('action_type')->title('Action'),
            Column::make('admin_name')->title('Admin')
                ->searchable(false)
                ->orderable(false),
            Column::make('action_at')->title('Date/Time'),
            Column::make('notes_preview')->title('Notes')
                ->searchable(false)
                ->orderable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'AdminReservationHistory_' . date('YmdHis');
    }
}

