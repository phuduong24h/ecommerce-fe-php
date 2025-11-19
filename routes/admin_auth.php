<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\AuthController;

// =============================
// ADMIN AUTH ROUTES (LOGIN + OTP)
// =============================

Route::prefix('admin')->name('admin.')->group(function () {

    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('forgot');
    Route::post('/forgot-password', [AuthController::class, 'submitForgot'])->name('forgot.submit');

    // Verify OTP
    Route::get('/verify-otp', [AuthController::class, 'showVerify'])->name('verify');
    Route::post('/verify-otp', [AuthController::class, 'submitVerify'])->name('verify.submit');

    // Reset Password
    Route::get('/reset-password', [AuthController::class, 'showReset'])->name('reset');
    Route::post('/reset-password', [AuthController::class, 'submitReset'])->name('reset.submit');

    // Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (bảo vệ bởi middleware)
    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    });
});
