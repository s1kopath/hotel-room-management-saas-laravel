@extends('layouts.app')

@section('title', 'Hotel Details')

@section('content')
    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Hotel Details</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="mb-3">{{ $hotel->name }}</h4>
                    
                    @if($hotel->images->count() > 0)
                    <div class="mb-4">
                        <div id="hotelCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($hotel->images as $index => $image)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $image->image_url) }}" 
                                            class="d-block w-100" alt="Hotel Image" 
                                            style="height: 400px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                            @if($hotel->images->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#hotelCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#hotelCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                            @endif
                        </div>
                    </div>
                    @endif

                    <h6 class="text-uppercase text-dark font-weight-bolder">Hotel Information</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $hotel->name }}</td>
                        </tr>
                        @if($hotel->description)
                        <tr>
                            <td><strong>Description:</strong></td>
                            <td>{{ $hotel->description }}</td>
                        </tr>
                        @endif
                        @if($hotel->address)
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td>{{ $hotel->address }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Location:</strong></td>
                            <td>
                                {{ $hotel->city ?? '' }}
                                {{ $hotel->state ? ($hotel->city ? ', ' : '') . $hotel->state : '' }}
                                {{ $hotel->country ? ($hotel->city || $hotel->state ? ', ' : '') . $hotel->country : '' }}
                                {{ $hotel->postal_code ? ' (' . $hotel->postal_code . ')' : '' }}
                            </td>
                        </tr>
                        @if($hotel->phone)
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $hotel->phone }}</td>
                        </tr>
                        @endif
                        @if($hotel->email)
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $hotel->email }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Total Rooms:</strong></td>
                            <td>{{ $hotel->total_rooms }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @if($hotel->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($hotel->status == 'inactive')
                                    <span class="badge bg-warning">Inactive</span>
                                @else
                                    <span class="badge bg-secondary">Archived</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <h6 class="text-uppercase text-dark font-weight-bolder">Owner Information</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Owner:</strong></td>
                            <td>{{ $hotel->owner->full_name ?? $hotel->owner->username }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $hotel->owner->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Created At:</strong></td>
                            <td>{{ $hotel->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    <div class="mt-4">
                        <h6 class="text-uppercase text-dark font-weight-bolder">Quick Stats</h6>
                        <div class="card bg-gradient-primary text-white">
                            <div class="card-body">
                                <h5 class="mb-0">{{ $hotel->rooms->count() }}</h5>
                                <p class="mb-0">Total Rooms</p>
                            </div>
                        </div>
                        <div class="card bg-gradient-success text-white mt-2">
                            <div class="card-body">
                                <h5 class="mb-0">{{ $hotel->reservations->where('status', 'checked_in')->count() }}</h5>
                                <p class="mb-0">Currently Occupied</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('hotels.index') }}" class="btn btn-secondary">Back to List</a>
                @hasPermission('hotels.edit-own')
                <a href="javascript:void(0)" onclick="loadModal('{{ route('hotels.edit', $hotel->id) }}')" 
                    class="btn btn-primary">Edit Hotel</a>
                @endhasPermission
                <a href="{{ route('rooms.index', ['hotel_id' => $hotel->id]) }}" class="btn btn-info">Manage Rooms</a>
            </div>
        </div>
    </div>
@endsection

