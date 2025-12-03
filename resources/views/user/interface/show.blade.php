@extends('layouts.app')

@section('content')

{{-- CSS bổ sung --}}
<style>
    /* ======================== */
    /* BADGE GIẢM GIÁ */
    /* ======================== */
    .product-badge {
        position: absolute;
        top: 0;
        left: 0;
        background-color: #dc2626;
        color: white;
        padding: 4px 8px;
        font-size: 1.2rem;
        font-weight: bold;
        border-bottom-right-radius: 8px;
        z-index: 2;
        box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
    }
    .product-badge::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background-color: #b91c1c;
        border-top-left-radius: 2px;
    }

    /* ======================== */
    /* GIÁ GẠCH NGANG */
    /* ======================== */
    .price-original {
        color: #9ca3af;
        text-decoration: line-through;
        font-size: 1.3rem;
        margin-right: 8px;
    }

    /* ======================== */
    /* FOOTER FLEX */
    /* ======================== */
    .home-product-item__footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
    }

    /* ======================== */
    /* NÚT THÊM GIỎ */
    /* ======================== */
    .home-product-item__button {
        margin-top: 10px;
        padding: 8px 12px;
        width: 100%;
        background-color: #0891b2;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .home-product-item__button:hover:not(:disabled) {
        background-color: #0e7490;
    }
    .home-product-item__button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background-color: #9ca3af;
    }

    /* ======================== */
    /* STOCK */
    /* ======================== */
    .home-product-item__stock {
        font-size: 0.75rem;
        font-weight: 500;
    }
    .home-product-item__stock.text-green-600 { color: #16a34a; }
    .home-product-item__stock.text-red-600 { color: #dc2626; }
</style>

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
            // 2. GIÁ, KHUYẾN MÃI & BIẾN THỂ
            // ========================
            $basePrice = $product['price'] ?? 0;
            $hasVariant = false;
            $displayPrice = $basePrice;
            $originalPrice = $basePrice;
            $productForCart = $product;

            if (!empty($product['variants']) && count($product['variants']) > 0) {
                $hasVariant = true;
                $firstVariant = $product['variants'][0];
                $productForCart['selected_variant'] = $firstVariant;
                $displayPrice = $basePrice + ($firstVariant['price'] ?? 0);
                $originalPrice = $displayPrice;
                $productForCart['price'] = $originalPrice;
                $stock = $firstVariant['stock'] ?? 0;
            } else {
                $stock = $product['stock'] ?? 0;
            }

            // ========================
            // 3. XỬ LÝ KHUYẾN MÃI
            // ========================
            $hasDiscount = $product['has_discount'] ?? false;
            $discountPercent = $product['discount_percent'] ?? 0;

            if ($hasDiscount && $discountPercent > 0) {
                $displayPrice = $originalPrice * (100 - $discountPercent) / 100;
            }

            // ========================
            // 4. TAG BẢO HÀNH
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
            <div class="home-product-item relative">

                {{-- Badge giảm giá --}}
                @if($hasDiscount && $discountPercent > 0)
                    <div class="product-badge">Giảm {{ $discountPercent }}%</div>
                @endif

                {{-- Link ảnh --}}
                <a href="{{ route('product.detail', ['id' => $prodId]) }}" class="block">
                    <div class="home-product-item__img" style="background-image: url({{ $imageUrl }}); height: 250px; background-size: cover; background-position: center; border-radius: 8px;"></div>
                </a>

                <div class="home-product-item__body mt-2">
                    <div class="home-product-name__wrap mb-2">
                        {{-- Link tên sản phẩm --}}
                        <a href="{{ route('product.detail', ['id' => $prodId]) }}">
                            <h4 class="home-product-item__name font-semibold text-md" title="{{ $product['name'] ?? 'Sản phẩm' }}">
                                {{ $product['name'] ?? 'Sản phẩm' }}
                            </h4>
                        </a>

                        {{-- Tag Bảo hành --}}
                        <span class="home-product-item__tag text-xs text-gray-500">{{ $warrantyTag }}</span>
                    </div>

                    {{-- Rating --}}
                    <div class="home-product-item__rating mb-2">
                        <i class="fa-solid fa-star text-yellow-400"></i>
                        <span>({{ $product['rating'] ?? 0 }})</span>
                    </div>

                    {{-- Danh mục --}}
                    <p class="home-product-item__category text-xs text-gray-400 mb-2">
                        {{ $product['categoryName'] ?? $categoryName ?? 'General' }}
                    </p>

                    {{-- Footer: Giá + Tình trạng --}}
                    <div class="home-product-item__footer">
                        <div class="flex items-center gap-2">
                            @if($hasDiscount && $discountPercent > 0)
                                <span class="home-product-item__price text-red-600 font-bold">
                                    ${{ number_format($displayPrice, 2) }}
                                </span>
                                <span class="price-original">
                                    ${{ number_format($originalPrice, 2) }}
                                </span>
                            @else
                                <span class="home-product-item__price text-cyan-600 font-bold">
                                    ${{ number_format($displayPrice, 2) }}
                                </span>
                            @endif
                        </div>

                        <span class="home-product-item__stock {{ $stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $stock > 0 ? 'Còn Hàng' : 'Hết' }}
                        </span>
                    </div>

                    {{-- Nút thêm giỏ --}}
                    <button class="home-product-item__button add-to-cart-btn"
                        data-product-json="{{ json_encode($productForCart) }}"
                        {{ $stock == 0 ? 'disabled' : '' }}>
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
