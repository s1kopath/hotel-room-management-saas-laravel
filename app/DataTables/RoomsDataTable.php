<?php

namespace App\DataTables;

use App\Models\Room;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RoomsDataTable extends DataTable
{
    protected $hotelId = null;

    /**
     * Set hotel ID for filtering
     */
    public function forHotel(int $hotelId): self
    {
        $this->hotelId = $hotelId;
        return $this;
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Room> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('room_number', function($row) {
                return '<a href="' . route('rooms.show', $row->id) . '">' . $row->room_number . '</a>';
            })
            ->editColumn('hotel.name', function($row) {
                return $row->hotel->name ?? '--';
            })
            ->editColumn('room_type', fn($row) => $row->room_type ?? '--')
            ->editColumn('floor_number', fn($row) => $row->floor_number ?? '--')
            ->editColumn('capacity', fn($row) => $row->capacity ?? '--')
            ->editColumn('status', function($row) {
                $badges = [
                    'vacant' => '<span class="badge bg-success">🟢 Vacant</span>',
                    'reserved' => '<span class="badge bg-warning">🟡 Reserved</span>',
                    'occupied' => '<span class="badge bg-danger">🔴 Occupied</span>',
                    'admin_reserved' => '<span class="badge bg-primary">🔵 Admin Reserved</span>',
                ];
                return $badges[$row->status] ?? $row->status;
            })
            ->editColumn('last_status_change', function($row) {
                return $row->last_status_change ? $row->last_status_change->format('d/m/Y H:i') : '--';
            })
            ->editColumn('status_updated_by', function($row) {
                return $row->statusUpdatedBy ? ($row->statusUpdatedBy->full_name ?? $row->statusUpdatedBy->username) : '--';
            })
            ->addColumn('action', fn($row) => view('rooms.components.action', compact('row'))->render())
            ->rawColumns(['room_number', 'status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Room>
     */
    public function query(Room $model): QueryBuilder
    {
        $user = Auth::user();

        $query = $model->newQuery()->with(['hotel', 'statusUpdatedBy']);

        // Filter by hotel if specified
        if ($this->hotelId) {
            $query->where('hotel_id', $this->hotelId);
        }

        // Super admin sees all rooms
        if ($user->isSuperAdmin()) {
            return $query->orderBy('hotel_id')->orderBy('room_number');
        }

        // Hotel owners see rooms from their hotels
        if ($user->isHotelOwner()) {
            $hotelIds = $user->hotels()->pluck('id');
            return $query->whereIn('hotel_id', $hotelIds)
                ->orderBy('hotel_id')
                ->orderBy('room_number');
        }

        // Staff see only rooms from hotels they have access to
        if ($user->isStaff()) {
            $hotelIds = $user->accessibleHotels()->pluck('hotels.id');
            return $query->whereIn('hotel_id', $hotelIds)
                ->orderBy('hotel_id')
                ->orderBy('room_number');
        }

        return $query->orderBy('hotel_id')->orderBy('room_number');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('rooms-table')
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
        $columns = [
            Column::computed('DT_RowIndex')
                ->title('Sl')
                ->searchable(false)
                ->orderable(false)
                ->width(40)
                ->addClass('text-center'),
            Column::make('room_number')->title('Room Number'),
        ];

        // Only show hotel column if not filtering by hotel
        if (!$this->hotelId) {
            $columns[] = Column::make('hotel.name')->title('Hotel');
        }

        $columns = array_merge($columns, [
            Column::make('room_type')->title('Type'),
            Column::make('floor_number')->title('Floor'),
            Column::make('capacity')->title('Capacity'),
            Column::make('status'),
            Column::make('last_status_change')->title('Last Status Change'),
            Column::make('status_updated_by')->title('Updated By'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('text-center'),
        ]);

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Rooms_' . date('YmdHis');
    }
}

