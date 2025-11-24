@extends('layouts.admin')

@section('title', 'Tổng Quan')

@section('content')
    <div class="space-y-6 px-4 py-6">

        <!-- Header -->
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-foreground">Tổng Quan</h1>
            <p class="text-muted-foreground text-sm md:text-base">Tổng quan hệ thống bán hàng của bạn</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Total Revenue -->
            <div class="bg-white shadow rounded-lg p-4 flex flex-col justify-between border-l-4 border-cyan-500">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm md:text-base font-medium text-card-foreground">Tổng doanh thu</h3>
                    <i class="fas fa-dollar-sign text-cyan-500 text-lg md:text-xl"></i>
                </div>
                <div class="mt-2">
                    <div class="text-2xl md:text-3xl font-bold text-cyan-600">${{ number_format($totalRevenue, 2) }}</div>
                    <p class="text-xs md:text-sm text-muted-foreground mt-1">
                        {{ $revenueGrowth > 0 ? '+' : '' }}{{ $revenueGrowth }}% so với tháng trước
                    </p>
                </div>
            </div>

            <!-- Active Orders -->
            <div class="bg-white shadow rounded-lg p-4 flex flex-col justify-between border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm md:text-base font-medium text-card-foreground">Đơn hàng đang xử lý</h3>
                    <i class="fas fa-shopping-cart text-purple-500 text-lg md:text-xl"></i>
                </div>
                <div class="mt-2">
                    <div class="text-2xl md:text-3xl font-bold text-purple-600">{{ $activeOrders }}</div>
                    <p class="text-xs md:text-sm text-muted-foreground mt-1">Đơn hàng đang tiến hành</p>
                </div>
            </div>

            <!-- Products -->
            <div class="bg-white shadow rounded-lg p-4 flex flex-col justify-between border-l-4 border-pink-500">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm md:text-base font-medium text-card-foreground">Sản phẩm</h3>
                    <i class="fas fa-box text-pink-500 text-lg md:text-xl"></i>
                </div>
                <div class="mt-2">
                    <div class="text-2xl md:text-3xl font-bold text-pink-600">{{ $products['total'] }}</div>
                    <p class="text-xs md:text-sm text-muted-foreground mt-1">{{ $products['lowStock'] }} sản phẩm sắp hết hàng</p>
                </div>
            </div>

            <!-- Warranty Claims -->
            <div class="bg-white shadow rounded-lg p-4 flex flex-col justify-between border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm md:text-base font-medium text-card-foreground">Yêu cầu bảo hành</h3>
                    <i class="fas fa-shield-alt text-blue-500 text-lg md:text-xl"></i>
                </div>
                <div class="mt-2">
                    <div class="text-2xl md:text-3xl font-bold text-blue-600">{{ $warrantyClaims['total'] }}</div>
                    <p class="text-xs md:text-sm text-muted-foreground mt-1">{{ $warrantyClaims['pending'] }} đang chờ xét duyệt</p>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Sales Overview -->
            <div class="bg-white shadow rounded-lg p-4">
                <h3 class="text-lg md:text-xl font-semibold mb-4">Tổng quan doanh thu</h3>
                <canvas id="salesChart"></canvas>
            </div>

            <!-- Sales by Category -->
            <div class="bg-white shadow rounded-lg p-4">
                <h3 class="text-lg md:text-xl font-semibold mb-4">Doanh thu theo danh mục</h3>
                <canvas id="pieChart"></canvas>
            </div>
        </div>

        <!-- Monthly Orders Bar Chart -->
        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="text-lg md:text-xl font-semibold mb-4">Xu hướng đơn hàng hàng tháng</h3>
            <canvas id="barChart"></canvas>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const monthlyRevenue = @json(array_values($monthlyRevenue));
            const monthlyRevenueLabels = @json(array_keys($monthlyRevenue));
            const salesByCategoryData = @json(array_values($salesByCategory));
            const salesByCategoryLabels = @json(array_keys($salesByCategory));
            const monthlyOrders = @json(array_values($monthlyOrders));
            const monthlyOrdersLabels = @json(array_keys($monthlyOrders));

            // Biểu đồ doanh thu
            new Chart(document.getElementById('salesChart'), {
                type: 'line',
                data: {
                    labels: monthlyRevenueLabels,
                    datasets: [{
                        label: 'Doanh thu',
                        data: monthlyRevenue,
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6,182,212,0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } } }
            });

            // Biểu đồ Doughnut - Doanh thu theo danh mục
            new Chart(document.getElementById('pieChart'), {
                type: 'doughnut',
                data: {
                    labels: salesByCategoryLabels,
                    datasets: [{
                        data: salesByCategoryData,
                        backgroundColor: ['#06b6d4', '#8b5cf6', '#ec4899', '#f59e0b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'right' },
                        tooltip: { callbacks: { label: ctx => `${ctx.label}: ${ctx.raw}%` } }
                    }
                }
            });

            // Biểu đồ Bar - Xu hướng đơn hàng
            new Chart(document.getElementById('barChart'), {
                type: 'bar',
                data: {
                    labels: monthlyOrdersLabels,
                    datasets: [{
                        label: 'Đơn hàng',
                        data: monthlyOrders,
                        backgroundColor: '#8b5cf6',
                        borderRadius: 8
                    }]
                },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });
        });
    </script>
@endpush
