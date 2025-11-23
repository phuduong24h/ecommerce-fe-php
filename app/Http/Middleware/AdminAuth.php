<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Nếu chưa đăng nhập admin → chuyển về trang login
        if (!session()->has('admin_token')) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
