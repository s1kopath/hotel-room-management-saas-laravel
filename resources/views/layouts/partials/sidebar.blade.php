<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="material-symbols-rounded p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-1 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav">close</i>
        <a class="navbar-brand px-4 py-3 m-0" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/img/logo-ct-dark.png') }}" class="navbar-brand-img" width="auto" height="30"
                alt="main_logo">
            <span class="ms-1 text-sm text-dark">{{ config('app.name') }}</span>
        </a>
    </div>

    <hr class="horizontal dark mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active bg-gradient-dark bg-brand-secondary text-white' : 'text-brand' }}"
                    href="{{ route('dashboard') }}">
                    <i class="material-symbols-rounded opacity-5">dashboard</i>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            @if(auth()->user()->isSuperAdmin())
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active bg-gradient-dark bg-brand-secondary text-white' : 'text-brand' }}"
                    href="{{ route('users.index') }}">
                    <i class="material-symbols-rounded opacity-5">people</i>
                    <span class="nav-link-text ms-1">Users</span>
                </a>
            </li>
            @endif

            @hasPermission('hotels.view-own')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hotels.*') ? 'active bg-gradient-dark bg-brand-secondary text-white' : 'text-brand' }}"
                    href="{{ route('hotels.index') }}">
                    <i class="material-symbols-rounded opacity-5">hotel</i>
                    <span class="nav-link-text ms-1">Hotels</span>
                </a>
            </li>
            @endhasPermission

            @hasPermission('rooms.view-own')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('rooms.*') ? 'active bg-gradient-dark bg-brand-secondary text-white' : 'text-brand' }}"
                    href="{{ route('rooms.index') }}">
                    <i class="material-symbols-rounded opacity-5">bed</i>
                    <span class="nav-link-text ms-1">Rooms</span>
                </a>
            </li>
            @endhasPermission

            @hasPermission('guests.view-own')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('guests.*') ? 'active bg-gradient-dark bg-brand-secondary text-white' : 'text-brand' }}"
                    href="{{ route('guests.index') }}">
                    <i class="material-symbols-rounded opacity-5">person</i>
                    <span class="nav-link-text ms-1">Guests</span>
                </a>
            </li>
            @endhasPermission

            @hasPermission('reservations.view-own')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reservations.*') ? 'active bg-gradient-dark bg-brand-secondary text-white' : 'text-brand' }}"
                    href="{{ route('reservations.index') }}">
                    <i class="material-symbols-rounded opacity-5">event</i>
                    <span class="nav-link-text ms-1">Reservations</span>
                </a>
            </li>
            @endhasPermission

            @if(auth()->user()->isSuperAdmin())
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reservation-history.*') ? 'active bg-gradient-dark bg-brand-secondary text-white' : 'text-brand' }}"
                    href="{{ route('admin.reservation-history.index') }}">
                    <i class="material-symbols-rounded opacity-5">history</i>
                    <span class="nav-link-text ms-1">Admin History</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->isSuperAdmin() || auth()->user()->isHotelOwner())
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('roles.*') ? 'active bg-gradient-dark bg-brand-secondary text-white' : 'text-brand' }}"
                    href="{{ route('roles.index') }}">
                    <i class="material-symbols-rounded opacity-5">admin_panel_settings</i>
                    <span class="nav-link-text ms-1">Roles</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0">
        <div class="mx-3">
            <a class="btn btn-outline-dark mt-4 w-100" href="{{ route('logout') }}" type="button">Logout</a>
        </div>
    </div>
</aside>
