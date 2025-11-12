@props(['name', 'class' => 'h-5 w-5'])

@php
    // Đảm bảo stroke mảnh và đồng nhất
    $defaultClass = 'w-5 h-5 stroke-current';
    $svgClass = $class . ' ' . $defaultClass;
@endphp

@switch($name)
    @case('dashboard')
        <!-- Tổng Quan -->
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        @break

    @case('products')
        <!-- Sản Phẩm -->
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.24 2.118H6.615a2.25 2.25 0 01-2.24-2.118L3.75 7.5M10 11.25h4M12 3.75v3.5m-6.375.75L12 3.75l6.375 3.75M12 21.75V12" />
        </svg>
        @break

    @case('orders')
        <!-- Đơn Hàng -->
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        @break

    @case('users')
        <!-- Người Dùng -->
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 11-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 7.5a3 3 0 11-6 0 3 3 0 016 0zM5.776 16.5a9 9 0 00-.375 2.003" />
        </svg>
        @break

    @case('warranty')
        <!-- Yêu Cầu Bảo Hành -->
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
        </svg>
        @break

    @case('policies')
        <!-- Chính Sách Bảo Hành -->
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
        </svg>
        @break

    @case('settings')
        <!-- Theme / Cài đặt -->
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 1115 0 7.5 7.5 0 01-15 0zM12 9.75a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" />
        </svg>
        @break

    @case('chevron-right')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        @break

    @default
        <span class="{{ $class }}">?</span>
@endswitch