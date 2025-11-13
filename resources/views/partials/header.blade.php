<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bảng điều khiển')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/styles.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col">
    <!-- HEADER -->
    <header class="header">
        <nav class="header__navbar">

            <ul class="header__navbar-list">

                <li class="header__navbar-item header__navbar-item--logo">
                    <i class="header__logo-icon fa-solid fa-shield"></i>
                    <div class="header__logo-text">
                        <span class="header__logo-title">Cửa Hàng Công Nghệ</span>
                        <span class="header__logo-subtitle">Cửa Hàng</span>
                    </div>
                </li>

                <li class="header__navbar-item">
                    <a href="{{ url('/') }}"
                    class="header__navbar-link {{ request()->is('/') ? 'header__navbar-link--active' : '' }}"
                    style="text-decoration: none;">
                        <i class="header__navbar-icon fa-solid fa-house"></i>
                        Trang Chủ
                    </a>
                </li>

                <li class="header__navbar-item">
                    <a href="{{ url('/warranty') }}"
                    class="header__navbar-link {{ request()->is('warranty') ? 'header__navbar-link--active' : '' }}"
                    style="text-decoration: none;">
                        <i class="header__navbar-icon fa-solid fa-shield"></i>
                        Bảo Hành
                    </a>
                </li>
            </ul>

            <ul class="header__navbar-list">

                <li class="header__navbar-item">
                    <a href="#" class="header__navbar-icon header__navbar-icon-right fa-regular fa-user"
                    style="text-decoration: none; color: inherit;">
                    </a>
                </li>

                <li class="header__navbar-item">
                    <a href="/cart" class="header__navbar-icon header__navbar-icon-right fa-solid fa-cart-shopping"
                    style="text-decoration: none; color: inherit; position: relative;">

                        @if(session('cart') && count(session('cart', [])) > 0)
                            <span style="position: absolute; top: -8px; right: -8px; font-size: 1rem; background: red; color: white; border-radius: 50%; padding: 2px 5px; line-height: 1;">
                                {{ count(session('cart', [])) }}
                            </span>
                        @endif
                    </a>
                </li>

                <li class="header__navbar-item">
                    <i class="header__navbar-icon header__navbar-icon-right fa-solid fa-language"></i>
                    <span style="font-size: 1.2rem; margin-left: 4px;">EN</span>
                </li>

                <li class="header__navbar-item header__navbar-item--strong" style="border: 1px solid #ccc; padding: 6px 12px; border-radius: 7px; font-size: 1.3rem;">
                    <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; color: inherit;">
                        Quản trị
                    </a>
                </li>

                <li class="header__navbar-item header__navbar-item--strong header__navbar-item--highlight">
                    <a href="{{ url('/') }}" style="text-decoration: none; color: white;">
                        Khách hàng
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    @stack('scripts')
</body>
</html>
