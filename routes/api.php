<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordResetController;

// auth
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    // forget password
    Route::post('forget-password/otp', [PasswordResetController::class, 'sendOtp']);
    Route::post('forget-password/otp/resend', [PasswordResetController::class, 'resendOtp']);
    Route::post('forget-password/otp/verify', [PasswordResetController::class, 'verifyAndReset']);
});

Route::middleware('auth:api')->group(function () {
    // logout
    Route::get('logout', [AuthController::class, 'logout']);

    // profile
    Route::get('user', [ProfileController::class, 'user']);
    Route::post('profile/change-password', [ProfileController::class, 'changePassword']);
});
