<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\WarrantyClaimController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\WarrantyPolicyController;

use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\CheckoutController;

use App\Http\Middleware\AdminAuth;

use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\InterfaceController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\AddCartController; // <-- THÊM DÒNG NÀY

use App\Http\Controllers\User\ProductDetailController;
use App\Http\Controllers\User\AuthController; //hải thêm

use App\Http\Controllers\User\WarrantyController; //bao hanh cua hải

// ========================================
// 1. ROUTE TRANG CHỦ (GIAO DIỆN CHÍNH)
// ========================================
Route::get('/', [InterfaceController::class, 'index'])->name('home');
Route::get('/category/{id}', [CategoryController::class, 'show'])->name('category.show');
// ***********************************************

// ========================================
// 1. ADMIN ROUTES (GIAO DIỆN)product
// ========================================
Route::prefix('admin')->name('admin.')->middleware([AdminAuth::class])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    //Dashboard
    //Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    //Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    //Oders
    Route::resource('orders', OrderController::class);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    ///Users
    Route::resource('users', UserController::class);

    //warranty
    Route::resource('warranty', WarrantyClaimController::class);

    Route::get('/policies', [WarrantyPolicyController::class, 'index'])->name('warranty_policies.index');
    Route::get('/policies/create', [WarrantyPolicyController::class, 'create'])->name('warranty_policies.create');
    Route::post('/policies', [WarrantyPolicyController::class, 'store'])->name('warranty_policies.store');
    Route::get('/policies/{id}/edit', [WarrantyPolicyController::class, 'edit'])->name('warranty_policies.edit');
    Route::put('/policies/{id}', [WarrantyPolicyController::class, 'update'])->name('warranty_policies.update');
    Route::delete('/policies/{id}', [WarrantyPolicyController::class, 'destroy'])->name('warranty_policies.destroy');

    //Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        // Index settings tổng quát (nếu muốn)
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
        Route::get('promotions', [SettingController::class, 'index'])->name('promotions.index'); // danh sách promotions
        Route::get('promotions/create', [SettingController::class, 'createPromotionForm'])->name('promotions.create'); // form tạo mới
        Route::post('promotions', [SettingController::class, 'createPromotion'])->name('promotions.store'); // tạo mới
        Route::get('promotions/{id}/edit', [SettingController::class, 'editPromotionForm'])->name('promotions.edit'); // form edit
        Route::put('promotions/{id}', [SettingController::class, 'updatePromotion'])->name('promotions.update'); // cập nhật
        Route::delete('promotions/{id}', [SettingController::class, 'deletePromotion'])->name('promotions.destroy'); // xóa
        // Centers
        Route::get('centers', [SettingController::class, 'index'])->name('centers.index'); // danh sách centers
        Route::get('centers/create', [SettingController::class, 'createCenterForm'])->name('centers.create'); // form tạo mới
        Route::post('centers', [SettingController::class, 'createCenter'])->name('centers.store'); // tạo mới
        Route::get('centers/{id}/edit', [SettingController::class, 'editCenterForm'])->name('centers.edit'); // form edit
        Route::put('centers/{id}', [SettingController::class, 'updateCenter'])->name('centers.update'); // cập nhật
        Route::delete('centers/{id}', [SettingController::class, 'deleteCenter'])->name('centers.destroy'); // xóa
        // product serials
        Route::get('serials', [SettingController::class, 'index'])->name('serials.index'); // danh sách serials
        Route::get('serials/create', [SettingController::class, 'createSerialForm'])->name('serials.create'); // form tạo mới
        Route::post('serials', [SettingController::class, 'createSerial'])->name('serials.store'); // tạo mới
        Route::get('serials/{id}/edit', [SettingController::class, 'editSerialForm'])->name('serials.edit'); // form edit
        Route::put('serials/{id}', [SettingController::class, 'updateSerial'])->name('serials.update'); // cập nhật
        Route::delete('serials/{id}', [SettingController::class, 'deleteSerial'])->name('serials.destroy'); // xóa
        Route::get('logs', [SettingController::class, 'index'])->name('logs.index'); // danh sách logs,
        Route::delete('logs/{id}', [SettingController::class, 'deleteLog'])->name('logs.destroy'); // xóa log,
    });
});

// ========================================
// 2. AUTH ROUTES (CUSTOMER LOGIN/REGISTER)
// ========================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register'])->name('register');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/cart/add', [AddCartController::class, 'add'])->name('cart.add');

// ========================================
// 2. API PROXY - /api/v1/admin/... → Node.js (localhost:3000)
// ========================================
// routes/web.php → phần API PROXY
Route::prefix('api')->group(function () {
    Route::any('{any}', function ($any) {
        $nodeUrl = config('services.api.url') . '/api/' . $any;
        $method = request()->method();

        $headers = request()->headers->all();
        unset($headers['host'], $headers['content-length']);

        // GỬI TOKEN TỪ .env (admin token)
        $headers['authorization'] = 'Bearer ' . config('services.api.token');

        $response = Http::withHeaders($headers)
                    ->withOptions(['timeout' => 30])
                    ->withoutVerifying()
            ->{$method === 'GET' ? 'get' : strtolower($method)}($nodeUrl, $method === 'GET' ? [] : request()->all());

        return response($response->body(), $response->status())
            ->withHeaders($response->headers());
    })->where('any', '.*');
});
// routes/web.php

// Account Routes
Route::prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/warranty', [AccountController::class, 'warranty'])->name('warranty');
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
});

//checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/submit', [CheckoutController::class, 'submit']);
// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/product/{id}', [ProductDetailController::class, 'show'])->name('product.detail');

//kiem tra bao hanh
Route::prefix('warranty')->group(function () {
    Route::get('/', [WarrantyController::class, 'index'])->name('warranty.index');
    Route::post('/check', [WarrantyController::class, 'checkSerial'])->name('warranty.check');
    Route::post('/claim', [WarrantyController::class, 'submitClaim'])->name('warranty.claim');

    // AJAX
    Route::post(
        '/ajax-serial',
        [WarrantyController::class, 'ajaxGetProductBySerial']
    )->name('warranty.ajax.serial');
});

require __DIR__ . '/admin_auth.php';

