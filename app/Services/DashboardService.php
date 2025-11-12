<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class DashboardService
{
    protected $orderService;
    protected $productService;
    protected $warrantyService;

    public function __construct(
        OrderService $orderService,
        ProductService $productService,
        WarrantyService $warrantyService
    ) {
        $this->orderService = $orderService;
        $this->productService = $productService;
        $this->warrantyService = $warrantyService;
    }

    /**
     * Lấy toàn bộ số liệu cho dashboard
     */
    public function getStats(): array
    {
        try {
            // ===== Orders =====
            $orders = $this->orderService->getAllOrders();

            $totalRevenue = collect($orders)->sum('totalAmount');
            $activeOrders = collect($orders)->whereIn('status', ['PENDING', 'PROCESSING'])->count();

            // Monthly orders (thống kê theo tháng)
            $monthlyOrders = collect($orders)
                ->groupBy(function ($order) {
                    return date('M', strtotime($order['createdAt']));
                })
                ->map(fn($monthOrders) => count($monthOrders))
                ->toArray();

            // Monthly revenue
            $monthlyRevenue = collect($orders)
                ->groupBy(function ($order) {
                    return date('M', strtotime($order['createdAt']));
                })
                ->map(fn($monthOrders) => collect($monthOrders)->sum('totalAmount'))
                ->toArray();

            // ===== Products =====
            $products = $this->productService->getAllProducts();
            $totalProducts = count($products);
            $lowStockProducts = collect($products)->where('stock', '<', 10)->count();

            // Sales by category
            $salesByCategory = [];
            foreach ($products as $product) {
                $cat = $product['categoryName'] ?? 'Others';
                $salesByCategory[$cat] = ($salesByCategory[$cat] ?? 0) + 1;
            }

            // ===== Warranty =====
            $claims = $this->warrantyService->getClaims();
            $totalClaims = count($claims);
            $pendingClaims = collect($claims)->where('status', 'PENDING')->count();

            return [
                'totalRevenue' => $totalRevenue,
                'activeOrders' => $activeOrders,
                'products' => [
                    'total' => $totalProducts,
                    'lowStock' => $lowStockProducts,
                ],
                'warrantyClaims' => [
                    'total' => $totalClaims,
                    'pending' => $pendingClaims,
                ],
                'monthlyRevenue' => $monthlyRevenue,
                'salesByCategory' => $salesByCategory,
                'monthlyOrders' => $monthlyOrders,
            ];

        } catch (\Exception $e) {
            Log::error('DashboardService getStats error: ' . $e->getMessage());
            return [];
        }
    }
}
