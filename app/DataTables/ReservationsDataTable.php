<?php

namespace App\DataTables;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ReservationsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Reservation> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('reservation_number', function($row) {
                $badge = $row->isAdminOverride() ? '<span class="badge bg-primary">🔵</span> ' : '';
                return $badge . '<a href="' . route('reservations.show', $row->id) . '">' . $row->reservation_number . '</a>';
            })
            ->addColumn('guest_name', function($row) {
                return $row->guest ? $row->guest->full_name : '--';
            })
            ->addColumn('hotel_name', function($row) {
                return $row->hotel ? $row->hotel->name : '--';
            })
            ->addColumn('room_number', function($row) {
                return $row->room ? $row->room->room_number : '--';
            })
            ->editColumn('check_in_date', fn($row) => $row->check_in_date->format('d/m/Y'))
            ->editColumn('check_out_date', fn($row) => $row->check_out_date->format('d/m/Y'))
            ->editColumn('status', function($row) {
                $badges = [
                    'pending' => '<span class="badge bg-warning">Pending</span>',
                    'confirmed' => '<span class="badge bg-info">Confirmed</span>',
                    'checked_in' => '<span class="badge bg-success">Checked In</span>',
                    'checked_out' => '<span class="badge bg-secondary">Checked Out</span>',
                    'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                    'no_show' => '<span class="badge bg-dark">No Show</span>',
                ];
                return $badges[$row->status] ?? $row->status;
            })
            ->editColumn('payment_status', function($row) {
                $badges = [
                    'pending' => '<span class="badge bg-warning">Pending</span>',
                    'partial' => '<span class="badge bg-info">Partial</span>',
                    'paid' => '<span class="badge bg-success">Paid</span>',
                    'refunded' => '<span class="badge bg-danger">Refunded</span>',
                ];
                return $badges[$row->payment_status] ?? $row->payment_status;
            })
            ->editColumn('total_amount', fn($row) => number_format($row->total_amount, 2))
            ->addColumn('action', fn($row) => view('reservations.components.action', compact('row'))->render())
            ->rawColumns(['reservation_number', 'status', 'payment_status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Reservation>
     */
    public function query(Reservation $model): QueryBuilder
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with(['guest', 'hotel', 'room']);

        // Super admin sees all reservations
        if ($user->isSuperAdmin()) {
            return $query->orderBy('id', 'desc');
        }

        // Hotel owners see reservations from their hotels
        if ($user->isHotelOwner()) {
            $hotelIds = $user->hotels()->pluck('id');
            return $query->whereIn('hotel_id', $hotelIds)->orderBy('id', 'desc');
        }

        // Staff see reservations from hotels they have access to
        if ($user->isStaff()) {
            $hotelIds = $user->accessibleHotels()->pluck('hotels.id');
            return $query->whereIn('hotel_id', $hotelIds)->orderBy('id', 'desc');
        }

        return $query->orderBy('id', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('reservations-table')
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
            Column::make('reservation_number')->title('Reservation #'),
            Column::make('guest_name')->title('Guest')
                ->searchable(false)
                ->orderable(false),
            Column::make('hotel_name')->title('Hotel')
                ->searchable(false)
                ->orderable(false),
            Column::make('room_number')->title('Room')
                ->searchable(false)
                ->orderable(false),
            Column::make('check_in_date')->title('Check In'),
            Column::make('check_out_date')->title('Check Out'),
            Column::make('status'),
            Column::make('payment_status')->title('Payment'),
            Column::make('total_amount')->title('Amount'),
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
        return 'Reservations_' . date('YmdHis');
    }
}
