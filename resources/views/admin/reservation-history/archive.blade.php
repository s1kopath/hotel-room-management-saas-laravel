@extends('layouts.app')

@section('title', 'Archived Records - ' . $month)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.reservation-history.archives') }}" class="btn btn-secondary">
            <i class="material-symbols-rounded">arrow_back</i> Back to Archives
        </a>
    </div>
    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">
                    Archived Records - {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                </h6>
            </div>
        </div>
        <div class="card-body">
            @if(count($archivedRecords) > 0)
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Reservation</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Hotel</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Room</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Admin</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date/Time</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archivedRecords as $record)
                        <tr>
                            <td>
                                @if($record->reservation)
                                <a href="{{ route('reservations.show', $record->reservation_id) }}">
                                    {{ $record->reservation->reservation_number }}
                                </a>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($record->reservation && $record->reservation->hotel)
                                    {{ $record->reservation->hotel->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($record->reservation && $record->reservation->room)
                                    {{ $record->reservation->room->room_number }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($record->action_type == 'created')
                                    <span class="badge bg-success">Created</span>
                                @elseif($record->action_type == 'modified')
                                    <span class="badge bg-info">Modified</span>
                                @else
                                    <span class="badge bg-warning">Released</span>
                                @endif
                            </td>
                            <td>
                                {{ $record->admin ? ($record->admin->full_name ?? $record->admin->username) : 'N/A' }}
                            </td>
                            <td>
                                {{ $record->action_at ? $record->action_at->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td>
                                <span title="{{ $record->notes }}">
                                    {{ $record->notes ? (strlen($record->notes) > 50 ? substr($record->notes, 0, 50) . '...' : $record->notes) : '--' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info">
                <i class="material-symbols-rounded">info</i>
                No archived records found for this month.
            </div>
            @endif
        </div>
    </div>
@endsection

