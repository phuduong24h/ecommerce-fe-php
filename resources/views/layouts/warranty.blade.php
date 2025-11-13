
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cửa Hàng Công Nghệ')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @stack('styles')
</head>

<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col">

    <!-- ======================= HEADER ======================= -->
    <header class="bg-white border-b shadow-sm sticky top-0 z-50">
        <div class="px-6 py-3 flex items-center justify-between w-full">

            <!-- LEFT: Logo + Home + Warranty -->
            <div class="flex items-center space-x-6">

                <!-- Logo -->
                <div class="flex items-center space-x-3 cursor-pointer">
                    <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-500 flex items-center justify-center text-white font-bold shadow-md">
                        🛍️
                    </div>
                    <div>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">
                            Cửa Hàng Công Nghệ
                        </h1>
                        <small class="text-gray-400 text-sm">Cửa hàng</small>
                    </div>
                </div>

                <!-- Menu -->
                <nav class="flex items-center space-x-3">
                    <a href="{{ url('/') }}"
                       class="px-3 py-1 rounded text-sm font-medium transition-all
                       {{ request()->is('/') 
                            ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow' 
                            : 'text-gray-700 hover:bg-gray-100 hover:text-cyan-600'
                       }}">
                        <i class="fas fa-home"></i> Trang Chủ
                    </a>

                    <a href="{{ url('/warranty') }}"
                       class="px-3 py-1 rounded text-sm font-medium transition-all
                       {{ request()->is('warranty*') 
                            ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow' 
                            : 'text-gray-700 hover:bg-gray-100 hover:text-purple-600'
                       }}">
                        <i class="fas fa-shield"></i> Bảo Hành
                    </a>
                </nav>
            </div>

            <!-- RIGHT: User | Cart | Language | Admin -->
            <div class="flex items-center space-x-3">

                <a href="#" class="text-gray-700 hover:text-cyan-600">
                    <i class="fas fa-user text-lg"></i>
                </a>

                <a href="/cart" class="relative text-gray-700 hover:text-cyan-600">
                    <i class="fas fa-shopping-cart text-lg"></i>
                    <span class="absolute -top-2 -right-2 w-5 h-5 flex items-center justify-center text-[10px] bg-red-500 text-white rounded-full">
                        {{ count(session('cart', [])) }}
                    </span>
                </a>

                <select class="text-sm border rounded px-2 py-1 focus:ring-2 focus:ring-cyan-500">
                    <option>VN</option>
                    <option>EN</option>
                </select>

                <a href="{{ route('admin.login') }}"
                   class="px-4 py-2 rounded text-sm font-medium border border-gray-300 hover:bg-gray-100 hover:text-cyan-600 transition">
                    Quản Trị
                </a>

                <a href="{{ url('/') }}"
                   class="px-4 py-2 rounded text-sm font-medium border border-gray-300 hover:bg-gray-100 hover:text-purple-600 transition">
                    Khách Hàng
                </a>
            </div>

        </div>
    </header>

    <!-- ======================= PAGE CONTENT ======================= -->
    <main class="px-6 py-8 max-w-5xl mx-auto w-full">
        @yield('content')
    </main>

    @stack('scripts')

</body>
</html>
