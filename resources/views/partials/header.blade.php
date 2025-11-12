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
        <div class="px-6 py-3 flex items-center justify-between w-full">
            
            <!-- LEFT: Logo + Menu -->
            <div class="flex items-center space-x-6">
                <!-- Logo + Tên -->
                <div class="flex items-center space-x-3 cursor-pointer">
                    <div
                        class="h-10 w-10 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-500 flex items-center justify-center text-white font-bold shadow-lg shadow-cyan-500/30">
                        🛍️
                    </div>
                    <div class="flex flex-col leading-tight">
                        <h1
                            class="text-xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">
                            Cửa Hàng Công Nghệ
                        </h1>
                        <small class="text-gray-400 text-sm">Cửa Hàng</small>
                    </div>
                </div>

                <!-- Menu -->
                <nav class="flex items-center space-x-3">
                    <a href="{{ url('/') }}"
                       class="flex items-center gap-1 px-3 py-1 rounded transition-all duration-300 font-medium
                       {{ request()->is('/') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow' : 'text-gray-700 hover:bg-gray-100 hover:text-cyan-600' }}">
                        <i class="fas fa-home"></i> Trang Chủ
                    </a>
                    <a href="{{ url('/warranty') }}"
                       class="flex items-center gap-1 px-3 py-1 rounded transition-all duration-300 font-medium
                       {{ request()->is('warranty') ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow' : 'text-gray-700 hover:bg-gray-100 hover:text-purple-600' }}">
                        <i class="fas fa-shield"></i> Bảo Hành
                    </a>
                </nav>
            </div>

            <!-- RIGHT: User, Cart, Language, Admin/Customer -->
            <div class="flex items-center space-x-3">
                <!-- User icon -->
                <a href="#" class="text-gray-700 hover:text-cyan-600">
                    <i class="fas fa-user"></i>
                </a>

                <!-- Cart -->
                <a href="/cart" class="relative text-gray-700 hover:text-cyan-600">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge absolute -top-2 -right-2 w-5 h-5 flex items-center justify-center text-[10px] bg-red-500 text-white rounded-full">
                        {{ count(session('cart', [])) }}
                    </span>
                </a>

                <!-- Language selector -->
                <select class="text-sm border rounded px-2 py-1 focus:ring-2 focus:ring-cyan-500">
                    <option>VN</option>
                    <option>EN</option>
                </select>

                <!-- Admin / Customer buttons -->
                <a href="{{ route('admin.dashboard') }}"
                   class="px-4 py-2 rounded text-sm font-medium transition-all duration-300
                   {{ request()->is('admin*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'border border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-cyan-600' }}">
                    Quản Trị
                </a>

                <a href="{{ url('/') }}"
                   class="px-4 py-2 rounded text-sm font-medium transition-all duration-300
                   {{ request()->is('/') ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg shadow-pink-500/30' : 'border border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-purple-600' }}">
                    Khách Hàng
                </a>
            </div>
        </div>
    </header>

    @stack('scripts')
</body>
</html>
