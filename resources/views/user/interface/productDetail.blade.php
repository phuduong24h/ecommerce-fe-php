@extends('layouts.app')

@section('title', $product['name'] ?? 'Chi tiết sản phẩm')

@section('content')

@php
    // 1. LOGIC XỬ LÝ ẢNH
    $imageUrl = 'https://via.placeholder.com/500?text=No+Image';
    if (!empty($product['HinhAnh'])) {
        $imageUrl = $product['HinhAnh'];
    } elseif (!empty($product['image'])) {
        $imageUrl = $product['image'];
    } elseif (!empty($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
        $firstImage = $product['images'][0];
        $imageUrl = is_array($firstImage) ? ($firstImage['url'] ?? $firstImage) : $firstImage;
    }

    // 2. LOGIC XỬ LÝ BIẾN THỂ (VARIANTS)
    $variants = $product['variants'] ?? [];
    $hasVariants = count($variants) > 0;

    // Giá và kho mặc định (Lấy của variant đầu tiên nếu có, ngược lại lấy của product)
    $currentPrice = $hasVariants ? ($variants[0]['price'] ?? 0) : ($product['price'] ?? $product['GiaBan'] ?? 0);
    $currentStock = $hasVariants ? ($variants[0]['stock'] ?? 0) : ($product['stock'] ?? $product['SoLuongTon'] ?? 0);

    $name = $product['name'] ?? $product['TenSP'] ?? 'Tên sản phẩm';
    $description = $product['description'] ?? $product['MoTa'] ?? 'Đang cập nhật mô tả...';
    $rating = $product['rating'] ?? 5;
@endphp

<div class="bg-white py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <div class="mb-6">
            <a href="{{ url('/') }}" class="flex items-center text-gray-600 hover:text-cyan-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Quay Lại Trang Chủ
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            {{-- CỘT TRÁI: ẢNH SẢN PHẨM --}}
            <div class="flex flex-col items-center">
                <div class="w-full aspect-square bg-gray-100 rounded-2xl overflow-hidden shadow-sm relative group">
                    <img src="{{ $imageUrl }}"
                         alt="{{ $name }}"
                         class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                </div>
            </div>

            {{-- CỘT PHẢI: THÔNG TIN --}}
            <div class="flex flex-col">

                <span class="text-cyan-500 font-medium text-sm mb-2 uppercase tracking-wider">
                    {{ $product['categoryName'] ?? 'Technology' }}
                </span>

                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $name }}</h1>

                {{-- Rating --}}
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400 text-sm">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="{{ $i <= $rating ? 'fas' : 'far' }} fa-star"></i>
                        @endfor
                    </div>
                    <span class="text-gray-500 text-sm ml-2">({{ $rating }} / 5)</span>
                </div>

                {{-- GIÁ VÀ KHO (Có ID để JS cập nhật) --}}
                <div class="flex items-center space-x-4 mb-6">
                    <span id="display-price" class="text-2xl font-bold text-cyan-600">
                        ${{ number_format($currentPrice, 2) }}
                    </span>

                    <span id="stock-badge" class="bg-{{ $currentStock > 0 ? 'green' : 'red' }}-100 text-{{ $currentStock > 0 ? 'green' : 'red' }}-700 px-3 py-1 rounded-full text-xs font-semibold flex items-center">
                        <i class="fas fa-{{ $currentStock > 0 ? 'check-circle' : 'times-circle' }} mr-1"></i>
                        <span id="display-stock-text">
                            {{ $currentStock > 0 ? "Còn Hàng ($currentStock)" : 'Hết Hàng' }}
                        </span>
                    </span>
                </div>

                {{-- KHU VỰC CHỌN VARIANT (HIỂN THỊ NẾU CÓ) --}}
                @if($hasVariants)
                <div class="mb-6 border-t border-b border-gray-100 py-4">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">
                        {{ $variants[0]['name'] ?? 'Tùy chọn' }}:
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($variants as $index => $variant)
                            <button
                                type="button"
                                class="variant-btn px-4 py-2 border rounded-lg text-sm font-medium transition-all duration-200
                                {{ $index === 0 ? 'border-cyan-500 bg-cyan-50 text-cyan-700 ring-1 ring-cyan-500' : 'border-gray-200 text-gray-600 hover:border-cyan-300 hover:text-cyan-600' }}"
                                onclick="selectVariant(this, {{ json_encode($variant) }})"
                            >
                                {{ $variant['value'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <p class="text-gray-600 mb-6 leading-relaxed text-sm">
                    {{ $description }}
                </p>

                {{-- Khối Bảo Hành --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 flex items-start space-x-3">
                    <div class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fas fa-shield-alt text-xs"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 text-sm">Bảo Hành Sản Phẩm</h3>
                        <p class="text-blue-600 text-xs font-bold mt-0.5">
                            {{ $product['warrantyPolicy']['name'] ?? 'Tiêu chuẩn' }}
                        </p>
                        <p class="text-gray-500 text-xs mt-1 leading-tight">
                            {{ $product['warrantyPolicy']['coverage'] ?? 'Liên hệ để biết thêm chi tiết.' }}
                        </p>
                    </div>
                </div>

                {{-- Nút Thêm Vào Giỏ --}}
                <div class="mt-auto">
                    <button id="add-to-cart-btn"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg shadow-blue-500/30 transition-all duration-300 flex items-center justify-center gap-2 add-to-cart-btn"
                            data-product-json="{{ json_encode($product) }}"
                            {{ $currentStock == 0 ? 'disabled' : '' }}>
                        <i class="fas fa-shopping-cart"></i>
                        {{ $currentStock > 0 ? 'Thêm Vào Giỏ' : 'Tạm Hết Hàng' }}
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- SCRIPT XỬ LÝ CHỌN BIẾN THỂ --}}
<script>
    // 1. Khởi tạo biến thể mặc định (nếu có)
    let selectedVariant = @json($hasVariants ? $variants[0] : null);

    // 2. Hàm xử lý khi click chọn
    function selectVariant(btn, variantData) {
        // Reset style tất cả nút
        document.querySelectorAll('.variant-btn').forEach(b => {
            b.className = 'variant-btn px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:border-cyan-300 hover:text-cyan-600 transition-all duration-200';
        });
        // Active nút được bấm
        btn.className = 'variant-btn px-4 py-2 border rounded-lg text-sm font-medium transition-all duration-200 border-cyan-500 bg-cyan-50 text-cyan-700 ring-1 ring-cyan-500';

        // Cập nhật biến toàn cục
        selectedVariant = variantData;

        // Cập nhật UI (Giá, Kho)
        updateUI(variantData);

        // Cập nhật dữ liệu cho nút Thêm giỏ
        updateAddToCartData();
    }

    function updateUI(data) {
        // Format tiền tệ
        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2
        });

        document.getElementById('display-price').innerText = formatter.format(data.price);

        const stockText = data.stock > 0 ? `Còn Hàng (${data.stock})` : 'Hết Hàng';
        document.getElementById('display-stock-text').innerText = stockText;

        // Update màu sắc badge kho
        const badge = document.getElementById('stock-badge');
        const btn = document.getElementById('add-to-cart-btn');

        if (data.stock > 0) {
            badge.className = "bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold flex items-center";
            badge.innerHTML = `<i class="fas fa-check-circle mr-1"></i> <span id="display-stock-text">${stockText}</span>`;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-shopping-cart"></i> Thêm Vào Giỏ';
            btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            btn.classList.add('bg-blue-500', 'hover:bg-blue-600');
        } else {
            badge.className = "bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold flex items-center";
            badge.innerHTML = `<i class="fas fa-times-circle mr-1"></i> <span id="display-stock-text">${stockText}</span>`;
            btn.disabled = true;
            btn.innerHTML = 'Tạm Hết Hàng';
            btn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            btn.classList.add('bg-gray-400', 'cursor-not-allowed');
        }
    }

    function updateAddToCartData() {
        const btn = document.getElementById('add-to-cart-btn');
        // Lấy JSON gốc ban đầu
        let productData = JSON.parse(btn.dataset.productJson);

        // Gán thêm thông tin variant
        if (selectedVariant) {
            productData.selected_variant = selectedVariant;
            productData.price = selectedVariant.price; // Cập nhật giá để AddCartController lưu đúng
        }

        // Gán ngược lại vào nút
        btn.dataset.productJson = JSON.stringify(productData);
    }

    // Chạy lần đầu khi tải trang để gán variant mặc định
    document.addEventListener('DOMContentLoaded', () => {
        if(selectedVariant) updateAddToCartData();
    });
</script>
@endsection
