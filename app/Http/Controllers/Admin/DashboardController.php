<?php

// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // DỮ LIỆU GIẢ NHƯ HÌNH BẠN GỬI
        $revenue = 34200;
        $activeOrders = 1140;
        $totalProducts = 342;
        $lowStock = 28;
        $warrantyClaims = 23;
        $pendingReviews = 5;

        return view('admin.dashboard.index', compact(
            'revenue', 'activeOrders', 'totalProducts',
            'lowStock', 'warrantyClaims', 'pendingReviews'
        ));
    }

    public function salesReport($year = null)
    {
        // Dữ liệu giả cho API
        return response()->json([
            1 => 4000, 2 => 5000, 3 => 4500, 4 => 7000, 5 => 8000, 6 => 7500
        ]);
    }
}
