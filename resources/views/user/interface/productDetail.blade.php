@extends('layouts.app')

@section('title', $product['name'] ?? 'Chi tiết sản phẩm')

@section('content')

@php
    // 1. XỬ LÝ ẢNH
    $galleryImages = [];
    if (!empty($product['images']) && is_array($product['images'])) {
        foreach($product['images'] as $img) {
            $url = is_array($img) ? ($img['url'] ?? '') : $img;
            if($url) $galleryImages[] = $url;
        }
    } elseif (!empty($product['image'])) {
        $galleryImages[] = $product['image'];
    } elseif (!empty($product['HinhAnh'])) {
        $galleryImages[] = $product['HinhAnh'];
    }
    if (empty($galleryImages)) {
        $galleryImages[] = 'https://via.placeholder.com/500?text=No+Image';
    }

    // 2. XỬ LÝ BIẾN THỂ & GIÁ
    $variants = $product['variants'] ?? [];
    $hasVariants = count($variants) > 0;
    $basePrice = $product['price'] ?? 0;
    
    // Lấy thông tin khuyến mãi
    $promo = $product['promo_data'] ?? ['has_discount' => false, 'discount_percent' => 0];
    $discountPercent = $promo['has_discount'] ? $promo['discount_percent'] : 0;

    // Giá của biến thể đầu tiên (nếu có)
    $firstVariantPrice = ($hasVariants && !empty($variants[0]['price'])) ? $variants[0]['price'] : 0;

    // Giá gốc hiển thị (Base + Variant)
    $originalPrice = $basePrice + $firstVariantPrice;
    
    // Giá sau khi giảm
    $currentPrice = $originalPrice * ((100 - $discountPercent) / 100);

    // Tồn kho
    $currentStock = $hasVariants ? ($variants[0]['stock'] ?? 0) : ($product['stock'] ?? 0);

    $name = $product['name'] ?? $product['TenSP'] ?? 'Tên sản phẩm';
    $description = $product['description'] ?? $product['MoTa'] ?? 'Đang cập nhật mô tả...';
    $rating = $product['rating'] ?? 5;

    // 3. CHUẨN BỊ JSON CHO NÚT BẤM
    $productDataForJson = $product;
    $productDataForJson['stock'] = $currentStock;
    
    // 🟢 QUAN TRỌNG: Gửi giá GỐC (chưa giảm) vào JSON.
    // Controller sẽ lấy giá này và tự nhân với % khuyến mãi.
    // Nếu gửi giá $currentPrice (đã giảm), Controller sẽ giảm tiếp -> Lỗi giá thấp.
    $productDataForJson['price'] = $originalPrice; 
@endphp

<div class="bg-white py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ url('/') }}" class="flex items-center text-gray-600 hover:text-cyan-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Quay Lại Trang Chủ
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            {{-- CỘT TRÁI: SLIDER ẢNH --}}
            <div class="flex flex-col">
                <div class="relative w-full aspect-square bg-gray-100 rounded-2xl overflow-hidden shadow-sm group border border-gray-200">
                    
                    {{-- BADGE GIẢM GIÁ --}}
                    @if($promo['has_discount'])
                        <div class="absolute top-4 left-4 bg-red-600 text-white px-3 py-1 rounded shadow-lg z-20 font-bold text-lg">
                            Giảm {{ $discountPercent }}%
                        </div>
                    @endif

                    <img id="main-image" src="{{ $galleryImages[0] }}" alt="{{ $name }}" class="w-full h-full object-contain p-4 transition-transform duration-500">

                    @if(count($galleryImages) > 1)
                        <button onclick="changeImage(-1)" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-md transition-all opacity-0 group-hover:opacity-100 focus:outline-none hover:scale-110 z-10"><i class="fas fa-chevron-left"></i></button>
                        <button onclick="changeImage(1)" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-md transition-all opacity-0 group-hover:opacity-100 focus:outline-none hover:scale-110 z-10"><i class="fas fa-chevron-right"></i></button>
                    @endif
                </div>

                @if(count($galleryImages) > 1)
                <div class="flex mt-4 gap-3 overflow-x-auto pb-2 justify-center">
                    @foreach($galleryImages as $index => $img)
                        <button onclick="setImage({{ $index }})" class="thumbnail-btn w-20 h-20 border-2 rounded-lg overflow-hidden flex-shrink-0 transition-all {{ $index === 0 ? 'border-cyan-500 ring-2 ring-cyan-200' : 'border-transparent hover:border-gray-300' }}">
                            <img src="{{ $img }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- CỘT PHẢI: THÔNG TIN --}}
            <div class="flex flex-col">
                <span class="text-cyan-500 font-medium text-sm mb-2 uppercase tracking-wider">{{ $product['categoryName'] ?? 'Technology' }}</span>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $name }}</h1>
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400 text-sm">
                        @for($i = 1; $i <= 5; $i++) <i class="{{ $i <= $rating ? 'fas' : 'far' }} fa-star"></i> @endfor
                    </div>
                    <span class="text-gray-500 text-sm ml-2">({{ $rating }} / 5)</span>
                </div>

                {{-- HIỂN THỊ GIÁ --}}
                <div class="flex items-end space-x-3 mb-6 bg-gray-50 p-4 rounded-lg">
                    {{-- Giá bán (To) --}}
                    <span id="display-price" class="text-3xl font-bold {{ $promo['has_discount'] ? 'text-red-600' : 'text-cyan-600' }}">
                        ${{ number_format($currentPrice, 2) }}
                    </span>

                    {{-- Giá gốc (Gạch ngang) --}}
                    <span id="original-price" class="text-gray-400 text-lg line-through mb-1 {{ $promo['has_discount'] ? '' : 'hidden' }}">
                        ${{ number_format($originalPrice, 2) }}
                    </span>

                    {{-- Badge nhỏ --}}
                    <span id="discount-tag" class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded uppercase mb-2 {{ $promo['has_discount'] ? '' : 'hidden' }}">
                        -{{ $discountPercent }}%
                    </span>

                    {{-- Kho --}}
                    <span id="stock-badge" class="bg-{{ $currentStock > 0 ? 'green' : 'red' }}-100 text-{{ $currentStock > 0 ? 'green' : 'red' }}-700 px-3 py-1 rounded-full text-xs font-semibold flex items-center ml-auto">
                        <i class="fas fa-{{ $currentStock > 0 ? 'check-circle' : 'times-circle' }} mr-1"></i>
                        <span id="display-stock-text">{{ $currentStock > 0 ? "Còn Hàng ($currentStock)" : 'Hết Hàng' }}</span>
                    </span>
                </div>

                {{-- BIẾN THỂ --}}
                @if($hasVariants)
                <div class="mb-6 border-t border-b border-gray-100 py-4">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">{{ $variants[0]['name'] ?? 'Tùy chọn' }}:</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($variants as $index => $variant)
                            @php
                                $vp = $variant['price'] ?? 0;
                                $label = $vp > 0 ? " (+$" . number_format($vp) . ")" : "";
                            @endphp
                            <button type="button" class="variant-btn px-4 py-2 border rounded-lg text-sm font-medium transition-all duration-200 {{ $index === 0 ? 'border-cyan-500 bg-cyan-50 text-cyan-700 ring-1 ring-cyan-500' : 'border-gray-200 text-gray-600 hover:border-cyan-300 hover:text-cyan-600' }}"
                                    onclick="selectVariant(this, {{ json_encode($variant) }})">
                                {{ $variant['value'] }} <span class="text-xs opacity-70">{{ $label }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <p class="text-gray-600 mb-6 leading-relaxed text-sm">{{ $description }}</p>

                {{-- BẢO HÀNH --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 flex items-start space-x-3">
                    <div class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-shield-alt text-xs"></i></div>
                    <div>
                        <h3 class="font-semibold text-gray-800 text-sm">Bảo Hành Sản Phẩm</h3>
                        <p class="text-blue-600 text-xs font-bold mt-0.5">{{ $product['warrantyPolicy']['name'] ?? 'Tiêu chuẩn' }}</p>
                        <p class="text-gray-500 text-xs mt-1 leading-tight">{{ $product['warrantyPolicy']['coverage'] ?? 'Liên hệ để biết thêm chi tiết.' }}</p>
                    </div>
                </div>

                {{-- NÚT THÊM GIỎ --}}
                <div class="mt-auto">
                    <button id="add-to-cart-btn"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg shadow-blue-500/30 transition-all duration-300 flex items-center justify-center gap-2 add-to-cart-btn {{ $currentStock == 0 ? 'bg-gray-400 cursor-not-allowed' : '' }}"
                            data-product-json="{{ json_encode($productDataForJson) }}"
                            {{ $currentStock == 0 ? 'disabled' : '' }}>
                        <i class="fas fa-shopping-cart"></i>
                        {{ $currentStock > 0 ? 'Thêm Vào Giỏ' : 'Tạm Hết Hàng' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const galleryImages = @json($galleryImages);
    let currentImageIndex = 0;
    const mainImage = document.getElementById('main-image');
    const thumbnails = document.querySelectorAll('.thumbnail-btn');

    function updateGalleryUI() {
        mainImage.src = galleryImages[currentImageIndex];
        thumbnails.forEach((thumb, idx) => {
            if (idx === currentImageIndex) {
                thumb.classList.add('border-cyan-500', 'ring-2', 'ring-cyan-200');
                thumb.classList.remove('border-transparent');
            } else {
                thumb.classList.remove('border-cyan-500', 'ring-2', 'ring-cyan-200');
                thumb.classList.add('border-transparent');
            }
        });
    }

    function changeImage(direction) {
        currentImageIndex += direction;
        if (currentImageIndex >= galleryImages.length) currentImageIndex = 0;
        else if (currentImageIndex < 0) currentImageIndex = galleryImages.length - 1;
        updateGalleryUI();
    }
    function setImage(index) { currentImageIndex = index; updateGalleryUI(); }

    const basePrice = {{ $basePrice }};
    const discountPercent = {{ $discountPercent }}; 
    let selectedVariant = @json(!empty($variants) ? $variants[0] : null);

    function selectVariant(btn, variantData) {
        document.querySelectorAll('.variant-btn').forEach(b => {
            b.className = 'variant-btn px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:border-cyan-300 hover:text-cyan-600 transition-all duration-200';
        });
        btn.className = 'variant-btn px-4 py-2 border rounded-lg text-sm font-medium transition-all duration-200 border-cyan-500 bg-cyan-50 text-cyan-700 ring-1 ring-cyan-500';

        selectedVariant = variantData;
        
        // Tính toán lại giá
        const variantPrice = variantData.price || 0;
        const originalTotal = basePrice + variantPrice;
        const finalTotal = originalTotal * ((100 - discountPercent) / 100);

        const formatter = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 2 });
        
        // Update UI
        document.getElementById('display-price').innerText = formatter.format(finalTotal);
        
        const originalEl = document.getElementById('original-price');
        if (discountPercent > 0) {
            originalEl.innerText = formatter.format(originalTotal);
            originalEl.classList.remove('hidden');
        } else {
            originalEl.classList.add('hidden');
        }

        // Update Stock
        const stockText = variantData.stock > 0 ? `Còn Hàng (${variantData.stock})` : 'Hết Hàng';
        document.getElementById('display-stock-text').innerText = stockText;

        const badge = document.getElementById('stock-badge');
        const cartBtn = document.getElementById('add-to-cart-btn');

        if(variantData.stock > 0) {
            badge.className = "bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold flex items-center ml-auto";
            cartBtn.disabled = false;
            cartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Thêm Vào Giỏ';
            cartBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            cartBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');
        } else {
            badge.className = "bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold flex items-center ml-auto";
            cartBtn.disabled = true;
            cartBtn.innerHTML = "Tạm Hết Hàng";
            cartBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            cartBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
        }

        updateAddToCartData();
    }

    function updateAddToCartData() {
        const btn = document.getElementById('add-to-cart-btn');
        let productData = JSON.parse(btn.dataset.productJson);
        
        if (selectedVariant) {
            productData.selected_variant = selectedVariant;
            productData.stock = selectedVariant.stock;
        }

        // 🟢 QUAN TRỌNG: Gửi giá GỐC (chưa giảm) khi đổi biến thể
        const vPrice = selectedVariant ? (selectedVariant.price || 0) : 0;
        const originalP = basePrice + vPrice; 
        productData.price = originalP;

        btn.dataset.productJson = JSON.stringify(productData);
    }

    document.addEventListener('DOMContentLoaded', () => {
        if(selectedVariant) updateAddToCartData();
    });
</script>
@endsection