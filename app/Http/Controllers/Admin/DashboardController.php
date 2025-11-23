<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\OrderService;
use App\Services\WarrantyService;

class DashboardController extends Controller
{
    protected $productService;
    protected $orderService;
    protected $warrantyService;

    public function __construct(
        ProductService $productService,
        OrderService $orderService,
        WarrantyService $warrantyService
    ) {
        $this->productService = $productService;
        $this->orderService = $orderService;
        $this->warrantyService = $warrantyService;
    }

    public function index()
    {
        // --- PRODUCTS ---
        $productsData = $this->productService->getAllProducts();
        $totalProducts = count($productsData);
        $lowStock = count(array_filter($productsData, fn($p) => $p['stock'] > 10 && $p['stock'] <= 50));


        // --- ORDERS ---
        $orders = $this->orderService->getAllOrders();
        $activeOrders = count(array_filter($orders, fn($o) => in_array($o['status'], ['PENDING', 'PROCESSING'])));

        // --- REVENUE (MONTHLY) ---
        // --- REVENUE (MONTHLY) ---
        $currentMonth = date('n');
        $currentYear = date('Y');

        // Doanh thu tháng này
        $currentMonthRevenue = array_sum(array_map(function ($o) use ($currentMonth, $currentYear) {
            return (date('n', strtotime($o['createdAt'])) == $currentMonth
                && date('Y', strtotime($o['createdAt'])) == $currentYear)
                ? $o['totalAmount'] : 0;
        }, $orders));

        // Doanh thu tháng trước
        $lastMonth = $currentMonth - 1;
        $lastMonthYear = $currentYear;
        if ($currentMonth == 1) { // Nếu tháng 1 → quay về tháng 12 năm trước
            $lastMonth = 12;
            $lastMonthYear -= 1;
        }

        $lastMonthRevenue = array_sum(array_map(function ($o) use ($lastMonth, $lastMonthYear) {
            return (date('n', strtotime($o['createdAt'])) == $lastMonth
                && date('Y', strtotime($o['createdAt'])) == $lastMonthYear)
                ? $o['totalAmount'] : 0;
        }, $orders));

        // % tăng trưởng
        $growth = $lastMonthRevenue > 0
            ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 2)
            : 0;
        // Monthly orders and revenue for charts
        $monthlyRevenue = [];
        $monthlyOrders = [];
        foreach (range(1, 12) as $m) {
            $monthOrders = array_filter($orders, fn($o) => date('n', strtotime($o['createdAt'])) == $m);
            $monthlyOrders[$m] = count($monthOrders);
            $monthlyRevenue[$m] = array_sum(array_map(fn($o) => $o['totalAmount'], $monthOrders));
        }

        // --- SALES BY CATEGORY ---
// Đếm số sản phẩm theo danh mục
        $salesByCategoryRaw = [];
        foreach ($productsData as $product) {
            $cat = $product['categoryName'] ?? 'Others';
            $salesByCategoryRaw[$cat] = ($salesByCategoryRaw[$cat] ?? 0) + 1;
        }

        // Sắp xếp giảm dần
        arsort($salesByCategoryRaw);

        // Lấy top 3
        $topThree = array_slice($salesByCategoryRaw, 0, 3, true);

        // Phần còn lại
        $others = array_slice($salesByCategoryRaw, 3, null, true);

        // Tổng Others
        $othersTotal = array_sum($others);

        // Gộp lại
        if ($othersTotal > 0) {
            $topThree['Others'] = $othersTotal;
        }

        // Chuyển sang phần trăm
        $totalCount = array_sum($topThree);
        $salesByCategory = [];

        foreach ($topThree as $cat => $count) {
            $salesByCategory[$cat] = round(($count / $totalCount) * 100, 2);
        }


        // --- WARRANTY CLAIMS ---
        $claims = $this->warrantyService->getClaims();
        $totalClaims = count($claims);
        $pendingClaims = count(array_filter($claims, fn($c) => $c['status'] === 'PENDING'));

        return view('admin.dashboard.index', [
            'totalRevenue' => $currentMonthRevenue,
            'revenueGrowth' => $growth,

            'activeOrders' => $activeOrders,
            'products' => [
                'total' => $totalProducts,
                'lowStock' => $lowStock,
            ],
            'warrantyClaims' => [
                'total' => $totalClaims,
                'pending' => $pendingClaims,
            ],
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyOrders' => $monthlyOrders,
            'salesByCategory' => $salesByCategory,
        ]);
    }
}