<?php

namespace App\DataTables;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class GuestsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Guest> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('full_name', function($row) {
                $name = $row->full_name;
                if ($row->vip_status) {
                    $name .= ' <span class="badge bg-warning">VIP</span>';
                }
                return '<a href="' . route('guests.show', $row->id) . '">' . $name . '</a>';
            })
            ->editColumn('email', fn($row) => $row->email ?? '--')
            ->editColumn('phone', fn($row) => $row->phone ?? '--')
            ->editColumn('city', fn($row) => $row->city ?? '--')
            ->editColumn('reservations_count', function($row) {
                return $row->reservations_count ?? 0;
            })
            ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y'))
            ->addColumn('action', fn($row) => view('guests.components.action', compact('row'))->render())
            ->rawColumns(['full_name', 'action'])
            ->filterColumn('full_name', function($query, $keyword) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$keyword}%"]);
            })
            ->orderColumn('full_name', 'first_name $1, last_name $1')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Guest>
     */
    public function query(Guest $model): QueryBuilder
    {
        $user = Auth::user();

        $query = $model->newQuery()->withCount('reservations');

        // Super admin sees all guests
        if ($user->isSuperAdmin()) {
            return $query->orderBy('id', 'desc');
        }

        // Hotel owners see their own guests
        if ($user->isHotelOwner()) {
            return $query->where('hotel_owner_id', $user->id)->orderBy('id', 'desc');
        }

        // Staff see guests from their hotel owner
        if ($user->isStaff() && $user->parent_user_id) {
            return $query->where('hotel_owner_id', $user->parent_user_id)->orderBy('id', 'desc');
        }

        return $query->orderBy('id', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('guests-table')
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
            Column::make('full_name')->title('Name')
                ->searchable(true)
                ->orderable(true),
            Column::make('email'),
            Column::make('phone'),
            Column::make('city'),
            Column::make('reservations_count')->title('Reservations')
                ->searchable(false)
                ->orderable(false),
            Column::make('created_at')->title('Added'),
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
        return 'Guests_' . date('YmdHis');
    }
}

