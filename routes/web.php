<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\WarrantyClaimController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\WarrantyPolicyController;

use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Warranty\WarrantyController;



/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
*/

/* ======================================================================
   0️⃣ REDIRECT /admin → /admin/login
   ====================================================================== */
Route::get('/admin', function () {
    return redirect()->route('admin.login');
});


/* ======================================================================
   1️⃣ AUTH ROUTES (Login, Logout)
   ====================================================================== */
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');


/* ======================================================================
   2️⃣ ADMIN PANEL (bắt buộc đăng nhập)
   ====================================================================== */
Route::prefix('admin')
    ->middleware('auth')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // PRODUCTS CRUD
        Route::resource('products', ProductController::class);

        // ORDERS CRUD
        Route::resource('orders', OrderController::class);

        // WARRANTY CLAIM CRUD
        Route::resource('warranty', WarrantyClaimController::class);

        // USERS (chỉ index, edit, update)
        Route::resource('users', UserController::class)
            ->only(['index', 'edit', 'update']);

        // WARRANTY POLICIES
        Route::resource('warranty_policies', WarrantyPolicyController::class)
            ->only(['index', 'store', 'destroy']);

        // SETTINGS
        Route::prefix('settings')->name('settings.')->group(function () {

            Route::get('/', function () {
                return redirect()->route('admin.settings.categories.index');
            })->name('index');

            // Categories
            Route::get('categories', [SettingController::class, 'index'])->name('categories.index');
            Route::get('categories/create', [SettingController::class, 'createCategoryForm'])->name('categories.create');
            Route::post('categories', [SettingController::class, 'createCategory'])->name('categories.store');
            Route::get('categories/{id}/edit', [SettingController::class, 'editCategoryForm'])->name('categories.edit');
            Route::put('categories/{id}', [SettingController::class, 'updateCategory'])->name('categories.update');
            Route::delete('categories/{id}', [SettingController::class, 'deleteCategory'])->name('categories.destroy');

            // Promotions
            Route::get('promotions', [SettingController::class, 'index'])->name('promotions.index');
            Route::get('promotions/create', [SettingController::class, 'createPromotionForm'])->name('promotions.create');
            Route::post('promotions', [SettingController::class, 'createPromotion'])->name('promotions.store');
            Route::get('promotions/{id}/edit', [SettingController::class, 'editPromotionForm'])->name('promotions.edit');
            Route::put('promotions/{id}', [SettingController::class, 'updatePromotion'])->name('promotions.update');
            Route::delete('promotions/{id}', [SettingController::class, 'deletePromotion'])->name('promotions.destroy');

            // Centers
            Route::get('centers', [SettingController::class, 'index'])->name('centers.index');
            Route::get('centers/create', [SettingController::class, 'createCenterForm'])->name('centers.create');
            Route::post('centers', [SettingController::class, 'createCenter'])->name('centers.store');
            Route::get('centers/{id}/edit', [SettingController::class, 'editCenterForm'])->name('centers.edit');
            Route::put('centers/{id}', [SettingController::class, 'updateCenter'])->name('centers.update');
            Route::delete('centers/{id}', [SettingController::class, 'deleteCenter'])->name('centers.destroy');

            // Serials
            Route::get('serials', [SettingController::class, 'index'])->name('serials.index');
            Route::get('serials/create', [SettingController::class, 'createSerialForm'])->name('serials.create');
            Route::post('serials', [SettingController::class, 'createSerial'])->name('serials.store');
            Route::get('serials/{id}/edit', [SettingController::class, 'editSerialForm'])->name('serials.edit');
            Route::put('serials/{id}', [SettingController::class, 'updateSerial'])->name('serials.update');
            Route::delete('serials/{id}', [SettingController::class, 'deleteSerial'])->name('serials.destroy');

            // Logs
            Route::get('logs', [SettingController::class, 'index'])->name('logs.index');
            Route::delete('logs/{id}', [SettingController::class, 'deleteLog'])->name('logs.destroy');
        });

        // Sales report
        Route::get('reports/sales/{year?}', [DashboardController::class, 'salesReport'])
            ->name('reports.sales')
            ->where('year', '[0-9]{4}');
    });


/* ======================================================================
   3️⃣ CART ROUTES (người dùng)
   ====================================================================== */
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');


/* ======================================================================
   4️⃣ WARRANTY PAGE (người dùng)
   ====================================================================== */
Route::get('/warranty', [WarrantyController::class, 'index'])->name('warranty.index');
Route::post('/warranty/check', [WarrantyController::class, 'checkSerial'])->name('warranty.check');
Route::post('/warranty/claim', [WarrantyController::class, 'submitClaim'])->name('warranty.claim');


/* ======================================================================
   5️⃣ API PROXY đến NodeJS (localhost:3000)
   ====================================================================== */
Route::prefix('api')->group(function () {
    Route::any('{any}', function ($any) {

        $nodeUrl = config('services.api.url') . '/api/' . $any;
        $method = request()->method();

        $headers = request()->headers->all();
        unset($headers['host'], $headers['content-length']);

        // Gửi token từ .env
        $headers['authorization'] = 'Bearer ' . config('services.api.token');

        $response = Http::withHeaders($headers)
            ->withOptions(['timeout' => 30])
            ->withoutVerifying()
            ->{$method === 'GET' ? 'get' : strtolower($method)}(
                $nodeUrl,
                $method === 'GET' ? [] : request()->all()
            );

        return response($response->body(), $response->status())
            ->withHeaders($response->headers());

    })->where('any', '.*');
});
