<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HotelController;

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
    Route::middleware(['permission:hotels.view-own,hotels.view-all'])->group(function () {
        Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
        Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');
    });

    Route::middleware(['permission:hotels.create'])->group(function () {
        Route::get('/hotels/create', [HotelController::class, 'create'])->name('hotels.create');
        Route::post('/hotels', [HotelController::class, 'store'])->name('hotels.store');
    });

    Route::middleware(['permission:hotels.edit-own,hotels.edit-all'])->group(function () {
        Route::get('/hotels/{hotel}/edit', [HotelController::class, 'edit'])->name('hotels.edit');
        Route::put('/hotels/{hotel}', [HotelController::class, 'update'])->name('hotels.update');
    });

    Route::middleware(['permission:hotels.delete-own'])->group(function () {
        Route::delete('/hotels/{hotel}', [HotelController::class, 'destroy'])->name('hotels.destroy');
    });
});

Route::get('test', function () {
    return view('welcome');
})->name('test');

Route::get('load-modal', function () {
    return view('load-modal');
})->name('load-modal');
