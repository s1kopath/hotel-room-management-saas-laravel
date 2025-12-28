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
});

Route::get('test', function () {
    return view('welcome');
})->name('test');

Route::get('load-modal', function () {
    return view('load-modal');
})->name('load-modal');
