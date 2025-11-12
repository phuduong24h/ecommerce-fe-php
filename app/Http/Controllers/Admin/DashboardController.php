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
        $totalRevenue = array_sum(array_map(fn($o) => $o['totalAmount'], $orders));

        // Monthly orders and revenue for charts
        $monthlyRevenue = [];
        $monthlyOrders = [];
        foreach (range(1, 12) as $m) {
            $monthOrders = array_filter($orders, fn($o) => date('n', strtotime($o['createdAt'])) == $m);
            $monthlyOrders[$m] = count($monthOrders);
            $monthlyRevenue[$m] = array_sum(array_map(fn($o) => $o['totalAmount'], $monthOrders));
        }

        // --- SALES BY CATEGORY ---
        $salesByCategory = [];
        foreach ($productsData as $product) {
            $cat = $product['categoryName'] ?? 'Others';
            $salesByCategory[$cat] = ($salesByCategory[$cat] ?? 0) + 1; // số lượng sản phẩm theo category
        }

        // --- WARRANTY CLAIMS ---
        $claims = $this->warrantyService->getClaims();
        $totalClaims = count($claims);
        $pendingClaims = count(array_filter($claims, fn($c) => $c['status'] === 'PENDING'));

        return view('admin.dashboard.index', [
            'totalRevenue' => $totalRevenue,
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
