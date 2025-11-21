@extends('layouts.app')

@section('title', $product['TenSP'] ?? ($product['name'] ?? 'Chi tiết sản phẩm'))

@section('content')

    @php
        // LOGIC XỬ LÝ ẢNH (Copy từ home.blade.php để đồng bộ)
        $imageUrl = 'https://via.placeholder.com/500?text=No+Image';

        if (!empty($product['HinhAnh'])) {
            $imageUrl = $product['HinhAnh'];
        } elseif (!empty($product['image'])) {
            $imageUrl = $product['image'];
        } elseif (!empty($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
            $firstImage = $product['images'][0];
            if (is_array($firstImage) && !empty($firstImage['url'])) {
                $imageUrl = $firstImage['url'];
            } elseif (is_string($firstImage)) {
                $imageUrl = $firstImage;
            }
        }

        // Xử lý giá
        $price = $product['GiaBan'] ?? ($product['price'] ?? 0);
        $stock = $product['SoLuongTon'] ?? ($product['stock'] ?? 0);
        $name = $product['TenSP'] ?? ($product['name'] ?? 'Tên sản phẩm');
        $description = $product['MoTa'] ?? ($product['description'] ?? 'Đang cập nhật mô tả...');
        $rating = $product['rating'] ?? 5;
    @endphp

    <div class="bg-white py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb / Nút Quay lại --}}
            <div class="mb-6">
                <a href="{{ url('/') }}" class="flex items-center text-gray-600 hover:text-cyan-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Quay Lại Sản Phẩm
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                {{-- CỘT TRÁI: ẢNH SẢN PHẨM --}}
                <div class="flex flex-col items-center">
                    <div class="w-full aspect-square bg-gray-100 rounded-2xl overflow-hidden shadow-sm relative group">
                        <img src="{{ $imageUrl }}" alt="{{ $name }}"
                            class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                    </div>
                </div>

                {{-- CỘT PHẢI: THÔNG TIN --}}
                <div class="flex flex-col">

                    {{-- Category tag --}}
                    <span class="text-cyan-500 font-medium text-sm mb-2 uppercase tracking-wider">
                        {{ $product['categoryName'] ?? 'Electronics' }}
                    </span>

                    {{-- Tên sản phẩm --}}
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $name }}</h1>

                    {{-- Rating --}}
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400 text-sm">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-gray-500 text-sm ml-2">({{ $rating }} out of 5)</span>
                    </div>

                    {{-- Giá và Tình trạng --}}
                    <div class="flex items-center space-x-4 mb-6">
                        <span class="text-2xl font-bold text-cyan-600">${{ number_format($price) }}</span>

                        @if ($stock > 0)
                            <span
                                class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold flex items-center">
                                <i class="fas fa-check-circle mr-1"></i> Còn Hàng ({{ $stock }})
                            </span>
                        @else
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                                Hết Hàng
                            </span>
                        @endif
                    </div>

                    {{-- Mô tả ngắn --}}
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ $description }}
                    </p>

                    {{-- Khối Bảo Hành --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 flex items-start space-x-3">
                        <div
                            class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-shield-alt text-xs"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm">Bảo Hành Sản Phẩm</h3>

                            {{-- HIỂN THỊ TÊN GÓI (VD: 1 Year Standard) --}}
                            <p class="text-blue-600 text-xs font-bold mt-0.5">
                                {{-- Dữ liệu này đã được Controller Bước 2 gán vào --}}
                                {{ $product['warrantyPolicy']['name'] }}
                            </p>

                            {{-- HIỂN THỊ MÔ TẢ --}}
                            <p class="text-gray-500 text-xs mt-1 leading-tight">
                                {{ $product['warrantyPolicy']['coverage'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Tính năng chính (List) --}}
                    <div class="mb-8">
                        <h3 class="font-semibold text-gray-800 mb-3">Tính Năng Chính</h3>
                        <ul class="space-y-2 text-gray-600 text-sm">
                            <li class="flex items-center">
                                <i class="far fa-check-circle text-green-500 mr-2"></i> Chất liệu cao cấp
                            </li>
                            <li class="flex items-center">
                                <i class="far fa-check-circle text-green-500 mr-2"></i> Thiết kế công thái học thoải mái
                            </li>
                            <li class="flex items-center">
                                <i class="far fa-check-circle text-green-500 mr-2"></i> Dễ dàng cài đặt và sử dụng
                            </li>
                            <li class="flex items-center">
                                <i class="far fa-check-circle text-green-500 mr-2"></i> Tương thích với mọi hệ thống
                            </li>
                        </ul>
                    </div>

                    {{-- Nút Thêm Vào Giỏ --}}
                    <div class="mt-auto">
                        <button
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg shadow-blue-500/30 transition-all duration-300 flex items-center justify-center gap-2 add-to-cart-btn"
                            data-product-json="{{ json_encode($product) }}">
                            <i class="fas fa-shopping-cart"></i>
                            Thêm Vào Giỏ
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
