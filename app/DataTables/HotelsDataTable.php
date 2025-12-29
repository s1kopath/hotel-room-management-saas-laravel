<?php

namespace App\DataTables;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class HotelsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Hotel> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function($row) {
                return '<a href="' . route('hotels.show', $row->id) . '">' . $row->name . '</a>';
            })
            ->addColumn('owner', function($row) {
                return $row->owner ? ($row->owner->full_name ?? $row->owner->username) : '--';
            })
            ->editColumn('city', fn($row) => $row->city ?? '--')
            ->editColumn('total_rooms', fn($row) => $row->total_rooms ?? 0)
            ->editColumn('status', function($row) {
                $badges = [
                    'active' => '<span class="badge bg-success">Active</span>',
                    'inactive' => '<span class="badge bg-warning">Inactive</span>',
                    'archived' => '<span class="badge bg-secondary">Archived</span>',
                ];
                return $badges[$row->status] ?? $row->status;
            })
            ->editColumn('created_at', fn($row) => date('d/m/Y', strtotime($row->created_at)))
            ->addColumn('action', fn($row) => view('hotels.components.action', compact('row'))->render())
            ->filterColumn('owner', function($query, $keyword) {
                $query->whereHas('owner', function($q) use ($keyword) {
                    $q->where('full_name', 'like', "%{$keyword}%")
                      ->orWhere('username', 'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('owner', 'users.full_name $1, users.username $1')
            ->rawColumns(['name', 'status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Hotel>
     */
    public function query(Hotel $model): QueryBuilder
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with('owner')
            ->leftJoin('users', 'hotels.user_id', '=', 'users.id')
            ->select('hotels.*');

        // Super admin sees all hotels
        if ($user->isSuperAdmin()) {
            return $query->orderBy('hotels.id', 'desc');
        }

        // Hotel owners see their own hotels
        if ($user->isHotelOwner()) {
            return $query->where('hotels.user_id', $user->id)->orderBy('hotels.id', 'desc');
        }

        // Staff see only hotels they have access to
        if ($user->isStaff()) {
            $hotelIds = $user->accessibleHotels()->pluck('hotels.id');
            return $query->whereIn('hotels.id', $hotelIds)->orderBy('hotels.id', 'desc');
        }

        return $query->orderBy('hotels.id', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('hotels-table')
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
            Column::make('name'),
            Column::make('owner')->title('Owner')
                ->searchable(true)
                ->orderable(true),
            Column::make('city'),
            Column::make('total_rooms')->title('Rooms')
                ->searchable(false),
            Column::make('status'),
            Column::make('created_at')->title('Created'),
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
        return 'Hotels_' . date('YmdHis');
    }
}

