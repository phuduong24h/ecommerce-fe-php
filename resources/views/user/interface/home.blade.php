@extends('layouts.app')

@section('content')

<div class="grid">
    <div class="grid__row app__content">
        <div class="grid__column-12">

            {{-- BANNER --}}
            <div class="app__banner">
                <p class="app__banner-heading">Chào Mừng Đến Cửa Hàng</p>
                <p class="app__banner-text">Khám phá các sản phẩm công nghệ cao cấp với bảo hành đầy đủ</p>
                <p class="app__banner-promo">Miễn phí vận chuyển cho đơn hàng trên $50</p>
            </div>

            {{-- SEARCH BAR --}}
            <form class="app__search-container" action="{{ route('home') }}" method="GET">
                <div class="header__search-input-wrap">
                    <i class="header__search-input-icon fa-solid fa-magnifying-glass"></i>
                    <input type="text"
                            class="header__search-input"
                            placeholder="Tìm kiếm sản phẩm..."
                            name="search"
                            value="{{ $searchTerm ?? '' }}">
                </div>
                <!-- <div class="header__search-select">
                    <span class="header__search-select-label">Tất cả danh mục</span>
                    <i class="header__search-select-icon fa-solid fa-angle-down"></i>
                </div> -->
                <button type="submit" style="display: none;"></button>
            </form>

            {{-- LIST SẢN PHẨM --}}
            <div class="home-product">
                <div class="grid__row">

                    @if(isset($error) && $error)
                        <div class="col-12 text-center p-5">
                            <h3 class="text-red-500">{{ $error }}</h3>
                        </div>
                    @elseif(empty($products))
                        <div class="col-12 text-center p-5">
                            <h3 class="text-gray-500">Không tìm thấy sản phẩm nào.</h3>
                        </div>
                    @else
                        @foreach ($products as $product)
                            @php
                                // 1. Xử lý ảnh (Logic cũ của bạn - Giữ nguyên)
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

                                // 2. LOGIC GIÁ CỘNG DỒN (Sửa lại cho đúng)
                                $basePrice = $product['price'] ?? 0; // Giá gốc sản phẩm
                                $displayPrice = $basePrice;          // Mặc định hiển thị giá gốc
                                $productForCart = $product;          // Dữ liệu để gửi vào giỏ hàng
                                $hasVariant = false;

                                // Kiểm tra nếu có biến thể
                                if (!empty($product['variants']) && count($product['variants']) > 0) {
                                    $hasVariant = true;
                                    // Lấy biến thể đầu tiên làm mặc định cho trang chủ
                                    $firstVariant = $product['variants'][0];

                                    // Gán biến thể đã chọn vào dữ liệu giỏ hàng
                                    $productForCart['selected_variant'] = $firstVariant;

                                    // TÍNH TOÁN GIÁ HIỂN THỊ: Giá Gốc + Giá Biến Thể
                                    $displayPrice = $basePrice + ($firstVariant['price'] ?? 0);

                                    // Cập nhật giá vào object giỏ hàng để AddCartController xử lý đúng nếu cần
                                    // (Lưu ý: Controller nên tính toán lại để bảo mật, nhưng gửi lên để UI JS xử lý nhanh)
                                    $productForCart['price'] = $displayPrice;
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
                                            {{-- Link tên --}}
                                            <a href="{{ route('product.detail', ['id' => $prodId]) }}">
                                                <h4 class="home-product-item__name" title="{{ $product['name'] }}">
                                                    {{ $product['name'] }}
                                                </h4>
                                            </a>

                                            {{-- Tag Bảo hành --}}
                                            <span class="home-product-item__tag">
                                                {{ $product['warranty_label'] ?? 'New' }}
                                            </span>
                                        </div>

                                        <div class="home-product-item__rating">
                                            <i class="fa-solid fa-star"></i>
                                            <span>({{ $product['rating'] ?? 0 }})</span>
                                        </div>

                                        <p class="home-product-item__category">
                                            {{ $product['categoryName'] ?? 'General' }}
                                        </p>

                                        <div class="home-product-item__footer">
                                            {{-- Hiển thị giá đã tính toán (Cộng dồn) --}}
                                            <span class="home-product-item__price text-cyan-600 font-bold">
                                                ${{ number_format($displayPrice, 2) }}
                                            </span>

                                            <span class="home-product-item__stock text-xs">
                                                {{ ($product['stock'] ?? 0) > 0 ? 'Còn Hàng' : 'Hết' }}
                                            </span>
                                        </div>

                                        {{-- Nút thêm giỏ --}}
                                        <button class="home-product-item__button btn_css btn--primary_css add-to-cart-btn"
                                                data-product-json="{{ json_encode($productForCart) }}">
                                            <i class="home-product-item__cart fa-solid fa-cart-shopping"></i>
                                            Thêm vào Giỏ
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- PHÂN TRANG (PAGINATION) --}}
            @if(isset($totalPages) && $totalPages > 1)
            <div class="pagination">
                <ul class="pagination-list">
                    {{-- Prev --}}
                    @if($currentPage > 1)
                        <li class="pagination-item">
                            <a href="{{ route('home', array_merge(request()->all(), ['page' => $currentPage - 1])) }}" class="pagination-link">
                                <i class="fa-solid fa-angle-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Numbers --}}
                    @for($i = 1; $i <= $totalPages; $i++)
                        <li class="pagination-item {{ $i == $currentPage ? 'pagination-item--active' : '' }}">
                            <a href="{{ route('home', array_merge(request()->all(), ['page' => $i])) }}" class="pagination-link">
                                {{ $i }}
                            </a>
                        </li>
                    @endfor

                    {{-- Next --}}
                    @if($currentPage < $totalPages)
                        <li class="pagination-item">
                            <a href="{{ route('home', array_merge(request()->all(), ['page' => $currentPage + 1])) }}" class="pagination-link">
                                <i class="fa-solid fa-angle-right"></i>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            @endif

            {{-- CSS Phân trang --}}
            <style>
                .pagination { display: flex; justify-content: center; margin: 40px 0 20px 0; }
                .pagination-list { display: flex; list-style: none; padding: 0; gap: 8px; }
                .pagination-link {
                    display: flex; align-items: center; justify-content: center;
                    min-width: 40px; height: 40px; text-decoration: none;
                    font-size: 1.4rem; color: #999; border-radius: 4px;
                    background-color: #fff; border: 1px solid #eee; transition: all 0.2s ease;
                }
                .pagination-link:hover { background-color: #fafafa; color: #0891b2; border-color: #0891b2; }
                .pagination-item--active .pagination-link {
                    background-color: #0891b2; color: white; border-color: #0891b2;
                }
                .pagination-item--active .pagination-link:hover { background-color: #0e7490; }
            </style>

        </div>
    </div>
</div>
@endsection