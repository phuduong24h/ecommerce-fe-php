<?php

use App\Http\Controllers\Controller;
use App\Models\WarrantyPolicy;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\WarrantyClaimController;
use App\Http\Controllers\Admin\UserController;        // ← THÊM DÒNG NÀY
use App\Http\Controllers\Admin\SettingController;   // ← THÊM DÒNG NÀY
use App\Http\Controllers\Admin\WarrantyPolicyController;
use App\Http\Controllers\Cart\CartController;


// ADMIN ROUTES - KHÔNG CẦN LOGIN ĐỂ TEST
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Trang chủ Admin = Dashboard
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Trang Dashboard riêng (nếu muốn /admin/dashboard)
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard'); // trùng tên OK
    
        // CRUD Sản phẩm
        Route::resource('products', ProductController::class);

        // CRUD Đơn hàng
        Route::resource('orders', OrderController::class);

        // CRUD Bảo hành
        Route::resource('warranty', WarrantyClaimController::class);

        // Quản lý Người dùng (chỉ index + edit + update)
        Route::resource('users', UserController::class)
            ->only(['index', 'edit', 'update']);

        // Chính sách bảo hành (Warranty Policies)
        Route::resource('warranty_policies', WarrantyPolicyController::class)
            ->only(['index', 'store', 'destroy']);
        // routes/web.php
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/settings', function () {
                return view('admin.settings.index');
            })->name('settings');
        });

        // Cài đặt hệ thống
        Route::match(['get', 'post'], 'settings', [SettingController::class, 'index'])
            ->name('settings');
        Route::post('settings/update', [SettingController::class, 'update'])
            ->name('settings.update');

        // Báo cáo doanh thu theo năm
        Route::get('reports/sales/{year?}', [DashboardController::class, 'salesReport'])
            ->name('reports.sales')
            ->where('year', '[0-9]{4}');
    });
// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
