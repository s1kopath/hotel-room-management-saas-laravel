@extends('layouts.app')

@section('title', 'Manage Hotel Access')

@section('content')
    <div class="mb-3">
        <a href="{{ route('users.show', $staff->id) }}" class="btn btn-secondary">
            <i class="material-symbols-rounded">arrow_back</i> Back to User
        </a>
    </div>

    <div class="card">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark bg-brand shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">
                    Hotel Access for {{ $staff->full_name ?? $staff->username }}
                </h6>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="material-symbols-rounded">info</i>
                Grant or revoke access to hotels for this staff member. They will only be able to manage rooms and reservations for hotels they have access to.
            </div>

            <form action="{{ route('users.hotel-access.update', $staff->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    @foreach($ownerHotels as $hotel)
                    <div class="col-md-6 mb-3">
                        <div class="card {{ in_array($hotel->id, $accessibleHotels) ? 'border border-success' : '' }}">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                        name="hotels[]" value="{{ $hotel->id }}" 
                                        id="hotel-{{ $hotel->id }}"
                                        {{ in_array($hotel->id, $accessibleHotels) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="hotel-{{ $hotel->id }}">
                                        <h6 class="mb-1">{{ $hotel->name }}</h6>
                                        <p class="text-xs text-secondary mb-0">
                                            <i class="material-symbols-rounded text-xs">location_on</i>
                                            {{ $hotel->city }}, {{ $hotel->country }}
                                        </p>
                                        <p class="text-xs text-secondary mb-0">
                                            <i class="material-symbols-rounded text-xs">bed</i>
                                            {{ $hotel->rooms()->count() }} rooms
                                        </p>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(count($ownerHotels) == 0)
                <div class="alert alert-warning">
                    <i class="material-symbols-rounded">warning</i>
                    No hotels available. Please create hotels first.
                </div>
                @endif

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Hotel Access</button>
                    <a href="{{ route('users.show', $staff->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

