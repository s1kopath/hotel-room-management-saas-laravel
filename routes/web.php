<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\AdminReservationHistoryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserHotelAccessController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::get('logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes - All authenticated users
Route::middleware(['admin'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Users - Only super admin can manage users
    Route::middleware(['super.admin'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Hotel Access Management (Hotel Owners can manage their staff)
    Route::middleware(['admin'])->group(function () {
        Route::get('/users/{user}/hotel-access', [UserHotelAccessController::class, 'index'])->name('users.hotel-access');
        Route::put('/users/{user}/hotel-access', [UserHotelAccessController::class, 'update'])->name('users.hotel-access.update');
        Route::post('/users/{user}/hotel-access/toggle/{hotel}', [UserHotelAccessController::class, 'toggle'])->name('users.hotel-access.toggle');
    });

    // Hotels - Permission-based access
    // IMPORTANT: Create route must come before {hotel} route to avoid route conflicts
    Route::middleware(['permission:hotels.create'])->group(function () {
        Route::get('/hotels/create', [HotelController::class, 'create'])->name('hotels.create');
        Route::post('/hotels', [HotelController::class, 'store'])->name('hotels.store');
    });

    Route::middleware(['permission:hotels.view-own,hotels.view-all'])->group(function () {
        Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
        Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');
    });

    Route::middleware(['permission:hotels.edit-own,hotels.edit-all'])->group(function () {
        Route::get('/hotels/{hotel}/edit', [HotelController::class, 'edit'])->name('hotels.edit');
        Route::put('/hotels/{hotel}', [HotelController::class, 'update'])->name('hotels.update');
    });

    Route::middleware(['permission:hotels.delete-own'])->group(function () {
        Route::delete('/hotels/{hotel}', [HotelController::class, 'destroy'])->name('hotels.destroy');
    });

    // Rooms - Permission-based access with hotel filtering
    // IMPORTANT: Create route must come before {room} route to avoid route conflicts
    Route::middleware(['permission:rooms.create'])->group(function () {
        Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
        Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    });

    Route::middleware(['permission:rooms.view-own,rooms.view-all'])->group(function () {
        Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    });

    Route::middleware(['permission:rooms.edit'])->group(function () {
        Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    });

    Route::middleware(['permission:rooms.change-status'])->group(function () {
        Route::post('/rooms/{room}/change-status', [RoomController::class, 'changeStatus'])->name('rooms.change-status');
    });

    Route::middleware(['permission:rooms.delete'])->group(function () {
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    });

    // Guests - Permission-based access
    // IMPORTANT: Create route must come before {guest} route to avoid route conflicts
    Route::middleware(['permission:guests.create'])->group(function () {
        Route::get('/guests/create', [GuestController::class, 'create'])->name('guests.create');
        Route::post('/guests', [GuestController::class, 'store'])->name('guests.store');
    });

    Route::middleware(['permission:guests.view-own,guests.view-all'])->group(function () {
        Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
        Route::get('/guests/{guest}', [GuestController::class, 'show'])->name('guests.show');
    });

    Route::middleware(['permission:guests.edit'])->group(function () {
        Route::get('/guests/{guest}/edit', [GuestController::class, 'edit'])->name('guests.edit');
        Route::put('/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
    });

    Route::middleware(['permission:guests.delete'])->group(function () {
        Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');
    });

    // Guest search (for AJAX)
    Route::middleware(['permission:guests.view-own,guests.view-all'])->group(function () {
        Route::get('/guests/search', [GuestController::class, 'search'])->name('guests.search');
    });

    // Reservations - Permission-based access
    // IMPORTANT: Create route must come before {reservation} route to avoid route conflicts
    Route::middleware(['permission:reservations.create'])->group(function () {
        Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    });

    Route::middleware(['permission:reservations.view-own,reservations.view-all'])->group(function () {
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    });

    Route::middleware(['permission:reservations.edit'])->group(function () {
        Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
        Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    });

    Route::middleware(['permission:reservations.check-in'])->group(function () {
        Route::post('/reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.check-in');
    });

    Route::middleware(['permission:reservations.check-out'])->group(function () {
        Route::post('/reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('reservations.check-out');
    });

    Route::middleware(['permission:reservations.cancel'])->group(function () {
        Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    });

    // Get available rooms (AJAX)
    Route::middleware(['permission:reservations.create'])->group(function () {
        Route::get('/reservations/available-rooms', [ReservationController::class, 'getAvailableRooms'])->name('reservations.available-rooms');
    });

    // Admin Override Reservations (Super Admin only)
    Route::middleware(['super.admin'])->group(function () {
        Route::get('/reservations/admin-override/create', [ReservationController::class, 'createAdminOverride'])->name('reservations.admin-override.create');
        Route::post('/reservations/admin-override', [ReservationController::class, 'storeAdminOverride'])->name('reservations.admin-override.store');
        Route::get('/reservations/{reservation}/admin-override/edit', [ReservationController::class, 'editAdminOverride'])->name('reservations.admin-override.edit');
        Route::put('/reservations/{reservation}/admin-override', [ReservationController::class, 'updateAdminOverride'])->name('reservations.admin-override.update');
        Route::post('/reservations/{reservation}/admin-override/release', [ReservationController::class, 'releaseAdminOverride'])->name('reservations.admin-override.release');

        // Admin Reservation History (Super Admin only)
        Route::get('/admin/reservation-history', [AdminReservationHistoryController::class, 'index'])->name('admin.reservation-history.index');
        Route::post('/admin/reservation-history/archive', [AdminReservationHistoryController::class, 'archive'])->name('admin.reservation-history.archive');
        Route::get('/admin/reservation-history/archives', [AdminReservationHistoryController::class, 'listArchives'])->name('admin.reservation-history.archives');
        Route::get('/admin/reservation-history/archive/{month}', [AdminReservationHistoryController::class, 'viewArchive'])->name('admin.reservation-history.archive.view');
        Route::post('/admin/reservation-history/archive/{month}/clear', [AdminReservationHistoryController::class, 'clearArchive'])->name('admin.reservation-history.archive.clear');
    });

    // Roles & Permissions Management
    // Hotel owners and super admin can manage roles
    Route::middleware(['admin'])->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});

Route::get('test', function () {
    return view('welcome');
})->name('test');

Route::get('load-modal', function () {
    return view('load-modal');
})->name('load-modal');
