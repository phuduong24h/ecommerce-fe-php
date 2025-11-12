<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bảng điều khiển')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>

<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col">

    <!-- HEADER -->
    <header class="bg-white border-b shadow-sm sticky top-0 z-50">
        <div class="px-6 py-4 flex justify-between items-center">
            <!-- Logo + Tên -->
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-500 flex items-center justify-center text-white font-bold shadow-lg shadow-cyan-500/30">
                    🛍️
                </div>
                <h1 class="text-xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">
                    Cửa Hàng Công Nghệ
                </h1>
            </div>

            <!-- Ngôn ngữ + Chế độ -->
            <div class="flex items-center space-x-3">
                <select class="text-sm border rounded px-2 py-1 focus:ring-2 focus:ring-cyan-500">
                    <option>VN</option>
                    <option>EN</option>
                </select>
                <a href="#"
                   class="{{ ($viewMode ?? '') === 'admin'
                       ? 'bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white shadow-lg shadow-cyan-500/30'
                       : 'border border-gray-300 hover:bg-gray-100 text-gray-700'
                   }} px-4 py-2 rounded text-sm font-medium transition-all duration-300">
                    Quản Trị
                </a>
                <a href="#"
                   class="{{ ($viewMode ?? '') === 'customer'
                       ? 'bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white shadow-lg shadow-pink-500/30'
                       : 'border border-gray-300 hover:bg-gray-100 text-gray-700'
                   }} px-4 py-2 rounded text-sm font-medium transition-all duration-300">
                    Khách Hàng
                </a>
            </div>
        </div>
    </header>

    <!-- BODY -->
    <div class="flex flex-1">
        <!-- SIDEBAR -->
        <aside class="w-64 bg-white border-r shadow-sm min-h-screen p-4">
            <nav class="flex flex-col gap-1">
                @php
                    $menuItems = [
                        ['route' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Tổng Quan'],
                        ['route' => 'admin.products.index', 'icon' => 'products', 'label' => 'Sản Phẩm'],
                        ['route' => 'admin.orders.index', 'icon' => 'orders', 'label' => 'Đơn Hàng'],
                        ['route' => 'admin.users.index', 'icon' => 'users', 'label' => 'Người Dùng'],
                        ['route' => 'admin.warranty.index', 'icon' => 'warranty', 'label' => 'Yêu Cầu Bảo Hành'],
                        ['route' => 'admin.warranty_policies.index', 'icon' => 'policies', 'label' => 'Chính Sách Bảo Hành'],
                        ['route' => 'admin.settings.index', 'icon' => 'settings', 'label' => 'Cài Đặt'],
                    ];
                    $currentRoute = Route::currentRouteName();
                @endphp

                @foreach ($menuItems as $item)
                    @php
                        $isActive = $currentRoute === $item['route'];
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-300
                       {{ $isActive
                           ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30'
                           : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'
                       }}">
                        <!-- Sử dụng component SVG icon -->
                        <x-icon-admin name="{{ $item['icon'] }}" class="h-5 w-5 {{ $isActive ? 'text-white' : 'text-gray-700' }}" />
                        <span>{{ $item['label'] }}</span>

                        @if($isActive)
                            <x-icon-admin name="chevron-right" class="h-4 w-4 text-white ml-auto" />
                        @endif
                    </a>
                @endforeach
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-6 bg-gray-50">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
