@extends('layouts.app')

@section('title', 'Guest Details')

@section('content')
    <div class="mb-3">
        <a href="{{ route('guests.index') }}" class="btn btn-secondary">
            <i class="material-symbols-rounded">arrow_back</i> Back to Guests
        </a>
    </div>

    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Guest Details - {{ $guest->full_name }}</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="mb-3">
                        {{ $guest->full_name }}
                        @if($guest->vip_status)
                            <span class="badge bg-warning">VIP</span>
                        @endif
                    </h4>

                    <h6 class="text-uppercase text-dark font-weight-bolder">Contact Information</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $guest->email ?? '--' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $guest->phone ?? '--' }}</td>
                        </tr>
                        @if($guest->phone_secondary)
                        <tr>
                            <td><strong>Secondary Phone:</strong></td>
                            <td>{{ $guest->phone_secondary }}</td>
                        </tr>
                        @endif
                        @if($guest->address)
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td>{{ $guest->address }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Location:</strong></td>
                            <td>
                                {{ $guest->city ?? '' }}
                                {{ $guest->state ? ($guest->city ? ', ' : '') . $guest->state : '' }}
                                {{ $guest->country ? ($guest->city || $guest->state ? ', ' : '') . $guest->country : '' }}
                                {{ $guest->postal_code ? ' (' . $guest->postal_code . ')' : '' }}
                            </td>
                        </tr>
                    </table>

                    <h6 class="text-uppercase text-dark font-weight-bolder mt-4">Personal Information</h6>
                    <table class="table table-borderless">
                        @if($guest->date_of_birth)
                        <tr>
                            <td><strong>Date of Birth:</strong></td>
                            <td>{{ $guest->date_of_birth->format('d/m/Y') }}</td>
                        </tr>
                        @endif
                        @if($guest->nationality)
                        <tr>
                            <td><strong>Nationality:</strong></td>
                            <td>{{ $guest->nationality }}</td>
                        </tr>
                        @endif
                        @if($guest->id_type && $guest->id_number)
                        <tr>
                            <td><strong>ID Type:</strong></td>
                            <td>{{ $guest->id_type }}</td>
                        </tr>
                        <tr>
                            <td><strong>ID Number:</strong></td>
                            <td>{{ $guest->id_number }}</td>
                        </tr>
                        @endif
                    </table>

                    @if($guest->preferences && count($guest->preferences) > 0)
                    <h6 class="text-uppercase text-dark font-weight-bolder mt-4">Guest Preferences</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($guest->preferences as $key => $value)
                            @if($value)
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                            @endif
                        @endforeach
                    </div>
                    @endif

                    @if($guest->notes)
                    <h6 class="text-uppercase text-dark font-weight-bolder mt-4">Notes</h6>
                    <p>{{ $guest->notes }}</p>
                    @endif
                </div>
                <div class="col-md-4">
                    <h6 class="text-uppercase text-dark font-weight-bolder">Quick Stats</h6>
                    <div class="card bg-gradient-primary text-white">
                        <div class="card-body">
                            <h5 class="mb-0">{{ $guest->reservations->count() }}</h5>
                            <p class="mb-0">Total Reservations</p>
                        </div>
                    </div>
                    <div class="card bg-gradient-success text-white mt-2">
                        <div class="card-body">
                            <h5 class="mb-0">{{ $guest->reservations->whereIn('status', ['pending', 'confirmed', 'checked_in'])->count() }}</h5>
                            <p class="mb-0">Active Reservations</p>
                        </div>
                    </div>

                    <h6 class="text-uppercase text-dark font-weight-bolder mt-4">Account Information</h6>
                    <table class="table table-borderless">
                        @if($guest->hotelOwner)
                        <tr>
                            <td><strong>Hotel Owner:</strong></td>
                            <td>{{ $guest->hotelOwner->full_name ?? $guest->hotelOwner->username }}</td>
                        </tr>
                        @endif
                        @if($guest->createdBy)
                        <tr>
                            <td><strong>Created By:</strong></td>
                            <td>{{ $guest->createdBy->full_name ?? $guest->createdBy->username }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Created At:</strong></td>
                            <td>{{ $guest->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($guest->reservations->count() > 0)
            <div class="mt-4">
                <h6 class="text-uppercase text-dark font-weight-bolder">Reservation History</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Reservation #</th>
                                <th>Hotel</th>
                                <th>Room</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($guest->reservations->take(10) as $reservation)
                                <tr>
                                    <td>{{ $reservation->reservation_number }}</td>
                                    <td>{{ $reservation->hotel->name ?? '--' }}</td>
                                    <td>{{ $reservation->room->room_number ?? '--' }}</td>
                                    <td>{{ $reservation->check_in_date->format('d/m/Y') }}</td>
                                    <td>{{ $reservation->check_out_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($reservation->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($reservation->status == 'confirmed')
                                            <span class="badge bg-info">Confirmed</span>
                                        @elseif($reservation->status == 'checked_in')
                                            <span class="badge bg-success">Checked In</span>
                                        @elseif($reservation->status == 'checked_out')
                                            <span class="badge bg-secondary">Checked Out</span>
                                        @elseif($reservation->status == 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-dark">No Show</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('guests.index') }}" class="btn btn-secondary">Back to List</a>
                @hasPermission('guests.edit')
                <a href="javascript:void(0)" onclick="loadModal('{{ route('guests.edit', $guest->id) }}')" 
                    class="btn btn-primary">Edit Guest</a>
                @endhasPermission
            </div>
        </div>
    </div>
@endsection

