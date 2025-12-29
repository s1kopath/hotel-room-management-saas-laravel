@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">hotel</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Hotels</p>
                        <h4 class="mb-0">{{ $stats['total_hotels'] }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">System-wide </span>all owners</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">bed</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Admin Reserved</p>
                        <h4 class="mb-0">{{ $stats['admin_reserved_rooms'] }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-primary text-sm font-weight-bolder">🔵 </span>Blue rooms</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">event</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Active Reservations</p>
                        <h4 class="mb-0">{{ $stats['active_reservations'] }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">All </span>hotels</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">people</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Users</p>
                        <h4 class="mb-0">{{ $stats['total_users'] }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-info text-sm font-weight-bolder">All </span>system users</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
            <div class="card z-index-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                        <h6 class="text-white text-capitalize ps-3">Room Status Distribution</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-success">🟢 Vacant</span></td>
                                    <td class="text-end">{{ $roomsByStatus['vacant'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">🟡 Reserved</span></td>
                                    <td class="text-end">{{ $roomsByStatus['reserved'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">🔴 Occupied</span></td>
                                    <td class="text-end">{{ $roomsByStatus['occupied'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary">🔵 Admin Reserved</span></td>
                                    <td class="text-end">{{ $roomsByStatus['admin_reserved'] ?? 0 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
            <div class="card z-index-2 ">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                        <h6 class="text-white text-capitalize ps-3">Reservation Status</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <tbody>
                                <tr>
                                    <td>Pending</td>
                                    <td class="text-end">{{ $reservationsByStatus['pending'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Confirmed</td>
                                    <td class="text-end">{{ $reservationsByStatus['confirmed'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Checked In</td>
                                    <td class="text-end">{{ $reservationsByStatus['checked_in'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Checked Out</td>
                                    <td class="text-end">{{ $reservationsByStatus['checked_out'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Cancelled</td>
                                    <td class="text-end">{{ $reservationsByStatus['cancelled'] ?? 0 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mt-4 mb-4">
            <div class="card z-index-2 ">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-info shadow-info border-radius-lg py-3 pe-1">
                        <h6 class="text-white text-capitalize ps-3">User Types</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <tbody>
                                <tr>
                                    <td>Super Admin</td>
                                    <td class="text-end">{{ $usersByType['super_admin'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Hotel Owners</td>
                                    <td class="text-end">{{ $usersByType['hotel_owner'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Staff</td>
                                    <td class="text-end">{{ $usersByType['staff'] ?? 0 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Hotels with Admin Reserved Rooms</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @if(count($hotelsWithAdminRooms) > 0)
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hotel</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Owner</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Reserved Rooms</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hotelsWithAdminRooms as $hotel)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $hotel->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $hotel->city }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $hotel->user->full_name ?? $hotel->user->username }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge bg-primary">{{ $hotel->admin_reserved_count }} rooms</span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('hotels.show', $hotel->id) }}" class="text-secondary font-weight-bold text-xs">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info mx-3">
                        <i class="material-symbols-rounded">info</i>
                        No admin reserved rooms currently.
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Recent Admin Actions</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @if(count($recentAdminActions) > 0)
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Reservation</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAdminActions as $action)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            @if($action->action_type == 'created')
                                                <span class="badge bg-success">Created</span>
                                            @elseif($action->action_type == 'modified')
                                                <span class="badge bg-info">Modified</span>
                                            @else
                                                <span class="badge bg-warning">Released</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($action->reservation)
                                        <a href="{{ route('reservations.show', $action->reservation_id) }}">
                                            {{ $action->reservation->reservation_number }}
                                        </a>
                                        @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $action->action_at->format('d/m/Y H:i') }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info mx-3">
                        <i class="material-symbols-rounded">info</i>
                        No recent admin actions.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">All Hotels (Recent 20)</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hotel</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Owner</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Rooms</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allHotels as $hotel)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $hotel->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $hotel->city }}, {{ $hotel->country }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $hotel->user->full_name ?? $hotel->user->username }}</p>
                                        <p class="text-xs text-secondary mb-0">{{ $hotel->user->email }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $hotel->rooms_count }}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if($hotel->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $hotel->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('hotels.show', $hotel->id) }}" class="text-secondary font-weight-bold text-xs">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3 py-2">
                        <a href="{{ route('hotels.index') }}" class="btn btn-sm btn-primary">View All Hotels</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

