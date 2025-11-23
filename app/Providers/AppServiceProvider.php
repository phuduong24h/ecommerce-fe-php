<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        foreach (glob(app_path('Helpers/*.php')) as $helper) {
            require_once $helper;
        }
        Paginator::useTailwind();

        // --- SỬA ĐOẠN NÀY ---
        // Dùng View Composer để luôn cập nhật đúng session mới nhất
        // Trong hàm boot() -> View::composer...
        View::composer('*', function ($view) {
            $cart = session('user.cart', []);
            
            // --- SỬA DÒNG NÀY ---
            // Cũ: $count = collect($cart)->sum('quantity'); (Cộng tổng số lượng)
            // Mới: Đếm số phần tử trong mảng (Số loại sản phẩm)
            $count = count($cart); 
            
            $view->with('cart_count', $count);
        });
    }
}