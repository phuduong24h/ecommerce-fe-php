<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Services\CategoryServiceUser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(CategoryServiceUser $categoryServiceUser): void
    {
        // 1. Load các helper trong app/Helpers/
        foreach (glob(app_path('Helpers/*.php')) as $helper) {
            require_once $helper;
        }

        // 2. Sử dụng Tailwind cho pagination
        Paginator::useTailwind();

        // 3. Share categories cho mọi view
        $categories = $categoryServiceUser->getCategories();
        View::share('categories', $categories);

        // 4. Share cart_count cho mọi view, lấy từ session
        View::composer('*', function ($view) {
            $cart = session('user.cart', []);
            $count = count($cart); // Hoặc tổng số lượng: collect($cart)->sum('quantity')
            $view->with('cart_count', $count);
        });
    }
}
