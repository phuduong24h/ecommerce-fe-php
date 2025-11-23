<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\User\AuthController;

// =============================
// ADMIN AUTH ROUTES (LOGIN + OTP)
// =============================
Route::prefix('admin')->name('admin.')->group(function () {

    // 🔥 ROUTE TEST TOKEN – KIỂM TRA TOKEN LƯU TRONG SESSION
    Route::get('/test-token', function () {
        dd(session('admin_token'));
    });

    // 🔥 ROUTE TEST SẢN PHẨM – KIỂM TRA TOKEN CÓ DÙNG ĐỂ GỌI API KHÔNG
    Route::get('/test-products', function () {
        $token = session('admin_token');

        if (!$token) {
            return "❌ Không có token trong session. Hãy login admin trước.";
        }

        $res = Http::withToken($token)
            ->get("http://localhost:3000/api/v1/admin/products");

        dd($res->json());
    });

    // ========== LOGIN ==========
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // ========== FORGOT PASSWORD ==========
    Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('forgot');
    Route::post('/forgot-password', [AuthController::class, 'submitForgot'])->name('forgot.submit');

    // ========== VERIFY OTP ==========
    Route::get('/verify-otp', [AuthController::class, 'showVerify'])->name('verify');
    Route::post('/verify-otp', [AuthController::class, 'submitVerify'])->name('verify.submit');

    // ========== RESET PASSWORD ==========
    Route::get('/reset-password', [AuthController::class, 'showReset'])->name('reset');
    Route::post('/reset-password', [AuthController::class, 'submitReset'])->name('reset.submit');

    // // ========== LOGOUT ==========
    // Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    //     Route::middleware('admin.auth')->group(function () {
    //     Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    // });
});