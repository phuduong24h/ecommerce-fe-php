<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Services\ApiClientService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Load helpers
        foreach (glob(app_path('Helpers/*.php')) as $helper) {
            require_once $helper;
        }

        Paginator::useTailwind();

        // Share cart badge count
        try {
            $api = new ApiClientService();
            $res = $api->get("/cart");   // 👈 CHỈ SỬA ĐÚNG DÒNG NÀY

            $cart = $res['data'] ?? [];

            // TÍNH THEO SỐ SẢN PHẨM (KHÔNG PHẢI QUANTITY)
            View::share('cart_count', count($cart));

        } catch (\Exception $e) {
            View::share('cart_count', 0);
        }
    }
}
