@extends('layouts.app')

@section('content')

<h2 class="text-2xl font-bold my-5">{{ $categoryName ?? 'Danh mục' }}</h2>

<div class="grid__row">
    @forelse($products as $product)
        @php
            // ========================
            // 1. XỬ LÝ ẢNH
            // ========================
            $imageUrl = 'https://via.placeholder.com/300?text=No+Image';
            if (!empty($product['HinhAnh'])) {
                $imageUrl = $product['HinhAnh'];
            } elseif (!empty($product['image'])) {
                $imageUrl = $product['image'];
            } elseif (!empty($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
                $firstImage = $product['images'][0];
                $imageUrl = is_array($firstImage) ? ($firstImage['url'] ?? $firstImage) : $firstImage;
            }

            $prodId = $product['id'] ?? $product['_id'] ?? 0;

            // ========================
            // 2. GIÁ & BIẾN THỂ
            // ========================
            $basePrice = $product['price'] ?? 0;
            $displayPrice = $basePrice;
            $productForCart = $product;
            $hasVariant = false;

            if (!empty($product['variants']) && count($product['variants']) > 0) {
                $hasVariant = true;
                $firstVariant = $product['variants'][0];
                $productForCart['selected_variant'] = $firstVariant;
                $displayPrice = $basePrice + ($firstVariant['price'] ?? 0);
                $productForCart['price'] = $displayPrice;
            }

            $stock = $hasVariant ? ($firstVariant['stock'] ?? 0) : ($product['stock'] ?? 0);

            // ========================
            // 3. TAG BẢO HÀNH
            // ========================
            if (!empty($product['warranty_label'])) {
                $warrantyTag = $product['warranty_label'];
            } elseif (!empty($product['warranty_period'])) {
                $warrantyTag = $product['warranty_period'] . ' tháng';
            } elseif ($hasVariant && !empty($firstVariant['warranty'])) {
                $warrantyTag = $firstVariant['warranty'];
            } else {
                $warrantyTag = 'New';
            }
        @endphp

        <div class="grid__column-4">
            <div class="home-product-item">
                {{-- Link ảnh --}}
                <a href="{{ route('product.detail', ['id' => $prodId]) }}" class="block">
                    <div class="home-product-item__img" style="background-image: url({{ $imageUrl }});"></div>
                </a>

                <div class="home-product-item__body">
                    <div class="home-product-name__wrap">
                        {{-- Link tên sản phẩm --}}
                        <a href="{{ route('product.detail', ['id' => $prodId]) }}">
                            <h4 class="home-product-item__name" title="{{ $product['name'] ?? 'Sản phẩm' }}">
                                {{ $product['name'] ?? 'Sản phẩm' }}
                            </h4>
                        </a>

                        {{-- Tag Bảo hành --}}
                        <span class="home-product-item__tag">{{ $warrantyTag }}</span>
                    </div>

                    {{-- Rating --}}
                    <div class="home-product-item__rating">
                        <i class="fa-solid fa-star"></i>
                        <span>({{ $product['rating'] ?? 0 }})</span>
                    </div>

                    {{-- Danh mục --}}
                    <p class="home-product-item__category">
                        {{ $product['categoryName'] ?? $categoryName ?? 'General' }}
                    </p>

                    {{-- Footer: Giá + Tình trạng --}}
                    <div class="home-product-item__footer">
                        <span class="home-product-item__price text-cyan-600 font-bold">
                            ${{ number_format($displayPrice, 2) }}
                        </span>
                        <span class="home-product-item__stock text-xs">
                            {{ $stock > 0 ? 'Còn Hàng' : 'Hết' }}
                        </span>
                    </div>

                    {{-- Nút thêm giỏ --}}
                    <button class="home-product-item__button btn_css btn--primary_css add-to-cart-btn"
                        data-product-json="{{ json_encode($productForCart) }}" {{ $stock == 0 ? 'disabled' : '' }}>
                        <i class="home-product-item__cart fa-solid fa-cart-shopping"></i>
                        {{ $stock > 0 ? 'Thêm vào Giỏ' : 'Tạm Hết Hàng' }}
                    </button>
                </div>
            </div>
        </div>

    @empty
        <div class="col-12 text-center p-5">
            <h3 class="text-gray-500">Không có sản phẩm nào trong danh mục này.</h3>
        </div>
    @endforelse
</div>

@endsection
