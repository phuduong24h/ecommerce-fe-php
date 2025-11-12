<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Auto-load tất cả helpers trong thư mục Helpers
        $helpersPath = glob(app_path('Helpers/*.php'));
        foreach ($helpersPath as $helper) {
            require_once $helper;
        }
            Paginator::useTailwind();
    }
}