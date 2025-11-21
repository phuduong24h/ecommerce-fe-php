<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bảng điều khiển')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- SỬA LẠI DÒNG NÀY --}}
    @vite(['resources/css/styles.css', 'resources/js/AddCart.js', 'resources/js/app.js'])
    @stack('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        window.myApp = {
            // URL để AJAX gọi đến Controller
            cartAddUrl: "{{ route('cart.add') }}",
            // URL trang đăng nhập
            loginUrl: "{{ route('login') }}",
            // Kiểm tra xem đã đăng nhập chưa
            isLoggedIn: {{ session()->has('user') ? 'true' : 'false' }},
        };
    </script>
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
                    <a href="{{ route('warranty.index') }}"
                        class="flex items-center gap-1 px-3 py-1 rounded transition-all duration-300 font-medium
                    {{ request()->routeIs('warranty.index')
                        ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow'
                        : 'text-gray-700 hover:bg-gray-100 hover:text-purple-600' }}">
                        <i class="fas fa-shield"></i> Bảo Hành
                    </a>

                </nav>
            </div>

            <!-- RIGHT: User, Cart, Language, Admin/Customer -->
            <div class="flex items-center space-x-3">
                <!-- User icon - Link to Account -->
                <a href="{{ route('account.index') }}"
                    class="text-gray-700 hover:text-cyan-600 transition-colors {{ request()->is('account*') ? 'text-cyan-600' : '' }}">
                    <i class="fas fa-user"></i>
                </a>

                <!-- Cart -->
                <!-- Cart -->
                <a href="/cart" class="relative text-gray-700 hover:text-cyan-600">
                    <i class="fas fa-shopping-cart text-xl"></i>

                    @php
                        // Nếu controller truyền $cart_count thì dùng
                        // Nếu không có thì fallback lấy từ session
                        $cartTotal = $cart_count ?? count(session('user.cart', []));
                    @endphp

                    <span id="cart-count"
                        class="absolute -top-2 -right-2 w-5 h-5 flex items-center justify-center 
                            text-[10px] bg-red-500 text-white rounded-full
                            {{ $cart_count > 0 ? '' : 'hidden' }}">
                        {{ $cart_count }}
                    </span>
                </a>


                <!-- Language selector -->
                <select class="text-sm border rounded px-2 py-1 focus:ring-2 focus:ring-cyan-500">
                    <option>VN</option>
                    <option>EN</option>
                </select>

                <!-- Admin / Customer buttons -->
                <a href="{{ route('admin.login') }}"
                    class="px-4 py-2 rounded text-sm font-medium transition-all duration-300
                    {{ request()->is('admin*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/30' : 'border border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-cyan-600' }}">
                    Quản Trị
                </a>

                @if (session('user'))
                    {{-- ĐÃ ĐĂNG NHẬP --}}

                    {{-- 1. Bọc trong div 'relative' và 'group' của Tailwind --}}
                    <div class="relative group">

                        {{-- 2. Nút bấm (trigger), làm giống hình ảnh của bạn --}}
                        <button type="button"
                            class="flex items-center space-x-2 px-3 py-1.5 rounded-full text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none">

                            {{-- Icon user màu xanh --}}
                            <span class="flex items-center justify-center w-6 h-6 bg-blue-500 rounded-full text-white">
                                <i class="fas fa-user text-xs"></i>
                            </span>

                            {{-- Tên người dùng (có dấu 3 chấm) --}}
                            <span class="hidden md:block font-semibold text-gray-800 truncate max-w-[150px]">
                                {{-- Đổi 'Xin chào' thành 'Chào,' cho giống ảnh --}}
                                Chào, {{ session('user')['name'] ?? 'Khách' }}
                            </span>

                            {{-- Icon mũi tên --}}
                            <i class="fas fa-chevron-down text-xs text-gray-500 opacity-75"></i>
                        </button>

                        {{-- 3. Box dropdown (chỉ hiện khi hover 'group') --}}
                        <div
                            class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-xl border border-gray-100 z-50
                                    hidden group-hover:block">
                            <div class="py-1">
                                {{-- Chỉ hiện nút Đăng xuất theo yêu cầu --}}
                                <a href="{{ route('logout') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600">
                                    <i class="fas fa-sign-out-alt w-6 text-gray-500"></i>
                                    Đăng xuất
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- CHƯA ĐĂNG NHẬP (Giữ nguyên) --}}
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 rounded text-sm font-medium transition-all duration-300
                       {{ request()->is('login') ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg shadow-pink-500/30' : 'border border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-purple-600' }}">
                        Khách Hàng
                    </a>
                @endif
            </div>
        </div>
    </header>

    @stack('scripts')
</body>

</html>
