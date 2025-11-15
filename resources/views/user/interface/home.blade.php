{{-- 1. Báo cho Laravel dùng file Khung (Layout) của nhóm --}}
@extends('layouts.app')

{{-- 2. Bắt đầu nhét nội dung vào chỗ @yield('content') --}}
@section('content')

{{-- 3. Đây là toàn bộ nội dung trang chủ của bạn (lấy từ trangchu.blade.php) --}}
<div class="grid">
    <div class="grid__row app__content">
        <div class="grid__column-12">
            <div class="app__banner">
                <p class="app__banner-heading">Chào Mừng Đến Cửa Hàng</p>
                <p class="app__banner-text">Khám phá các sản phẩm công nghệ cao cấp với bảo hành đầy đủ</p>
                <p class="app__banner-promo">Miễn phí vận chuyển cho đơn hàng trên $50</p>
            </div>

            <form class="app__search-container" action="{{ route('home') }}" method="GET">
                <div class="header__search-input-wrap">
                    <i class="header__search-input-icon fa-solid fa-magnifying-glass"></i>
                    <input type="text"
                            class="header__search-input"
                            placeholder="Tìm kiếm sản phẩm..."
                            name="search"
                            value="{{ $searchTerm ?? '' }}">
                </div>
                <div class="header__search-select">
                    <span class="header__search-select-label">Tất cả danh mục</span>
                    <i class="header__search-select-icon fa-solid fa-angle-down"></i>
                    <ul class="header__search-option">
                        <li class="header__search-option-item header__search-option-item--active">
                            <span>Tất cả danh mục</span>
                            <i class="fa-solid fa-check"></i>
                        </li>
                        <li class="header__search-option-item">
                            <span>Điện tử</span>
                            <i class="fa-solid fa-check"></i>
                        </li>
                        <li class="header__search-option-item">
                            <span>Phụ kiện</span>
                            <i class="fa-solid fa-check"></i>
                        </li>
                    </ul>
                </div>
                <button type="submit" style="display: none;"></button>
            </form>

            {{-- Nơi để hiển thị sản phẩm --}}
            <div class="home-product">
                <div class="grid__row">
                    {{-- Kiểm tra nếu có lỗi từ controller --}}
                    @if(isset($error) && $error)
                        <h3 style="text-align: center; color: red; width: 100%;">{{ $error }}</h3>

                    {{-- Kiểm tra nếu không có sản phẩm --}}
                    @elseif(empty($products))
                        <h3 style="text-align: center; color: #888; width: 100%;">Không tìm thấy sản phẩm nào.</h3>

                    {{-- Nếu có sản phẩm, lặp và hiển thị --}}
                    @else
                        @foreach ($products as $product)
                            @php
                                $imageUrl = 'https://via.placeholder.com/300?text=No+Image'; // Ảnh mặc định

                                // Thử lấy 'HinhAnh' (key cũ) hoặc 'image' (key trong app.js)
                                if (!empty($product['HinhAnh'])) {
                                    $imageUrl = $product['HinhAnh'];
                                } elseif (!empty($product['image'])) {
                                    $imageUrl = $product['image'];
                                }
                                // Thử lấy mảng 'images' (key trong app.js)
                                elseif (!empty($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
                                    $firstImage = $product['images'][0];
                                    if (is_array($firstImage) && !empty($firstImage['url'])) {
                                        $imageUrl = $firstImage['url'];
                                    } elseif (is_string($firstImage)) {
                                        $imageUrl = $firstImage;
                                    }
                                }
                            @endphp
                            {{-- Ghi chú: Cấu trúc HTML bên dưới là ví dụ
                                Bạn cần thay thế bằng cấu trúc HTML của thẻ sản phẩm (product card) mà bạn muốn --}}
                            {{-- Đây là cấu trúc HTML đầy đủ từ app.js --}}
                            <div class="grid__column-4">
                                <div class="home-product-item">
                                    <div class="home-product-item__img" style="background-image: url({{ $imageUrl }});"></div>
                                    <div class="home-product-item__body">
                                        <div class="home-product-name__wrap">
                                            {{-- Dùng TenSP (key cũ) hoặc name (key app.js) --}}
                                            <h4 class="home-product-item__name">{{ $product['TenSP'] ?? $product['name'] ?? 'N/A' }}</h4>
                                            <span class="home-product-item__tag">{{ $product['categoryName'] ?? 'New' }}</span>
                                        </div>
                                        <div class="home-product-item__rating">
                                            <i class="fa-solid fa-star"></i>
                                            <span>({{ $product['rating'] ?? '0' }})</span>
                                        </div>
                                        <p class="home-product-item__category">{{ $product['categoryId'] ?? 'N/A' }}</p>
                                        <div class="home-product-item__footer">
                                            {{-- Dùng GiaBan (key cũ) hoặc price (key app.js) --}}
                                            <span class="home-product-item__price">${{ number_format($product['GiaBan'] ?? $product['price'] ?? 0) }}</span>
                                            <span class="home-product-item__stock">{{ ($product['stock'] ?? $product['SoLuongTon'] ?? 0) > 0 ? 'Còn Hàng' : 'Hết Hàng' }}</span>
                                        </div>
                                        <button class="home-product-item__button btn_css btn--primary_css add-to-cart-btn"
                                                data-product-json="{{ json_encode($product) }}">
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

        </div>
    </div>
</div>

@endsection
{{-- 4. Kết thúc nhét nội dung --}}
