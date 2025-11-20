{{-- 1. Báo cho Laravel dùng file Khung (Layout) của nhóm --}}
@extends('layouts.app')

{{-- 2. Bắt đầu nhét nội dung vào chỗ @yield('content') --}}
@section('content')

    {{-- 3. Đây là toàn bộ nội dung trang chủ của bạn (lấy từ trangchu.blade.php) --}}
    <div class="grid">
        <div class="grid__row app__content">
            <div class="grid__column-12">

                {{-- Banner --}}
                <div class="app__banner">
                    <p class="app__banner-heading">Chào Mừng Đến Cửa Hàng</p>
                    <p class="app__banner-text">
                        Khám phá các sản phẩm công nghệ cao cấp với bảo hành đầy đủ
                    </p>
                    <p class="app__banner-promo">Miễn phí vận chuyển cho đơn hàng trên $50</p>
                </div>

                {{-- Search --}}
                <form class="app__search-container" action="{{ route('home') }}" method="GET">
                    <div class="header__search-input-wrap">
                        <i class="header__search-input-icon fa-solid fa-magnifying-glass"></i>
                        <input type="text" class="header__search-input" placeholder="Tìm kiếm sản phẩm..." name="search"
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

                {{-- Danh sách sản phẩm --}}
                <div class="home-product">
                    <div class="grid__row">
                        {{-- Kiểm tra nếu có lỗi từ controller --}}
                        @if (isset($error) && $error)
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
                                    elseif (
                                        !empty($product['images']) &&
                                        is_array($product['images']) &&
                                        count($product['images']) > 0
                                    ) {
                                        $firstImage = $product['images'][0];
                                        if (is_array($firstImage) && !empty($firstImage['url'])) {
                                            $imageUrl = $firstImage['url'];
                                        } elseif (is_string($firstImage)) {
                                            $imageUrl = $firstImage;
                                        }
                                    }
                                    $prodId = $product['id'] ?? ($product['_id'] ?? 0);
                                @endphp
                                {{-- Ghi chú: Cấu trúc HTML bên dưới là ví dụ
                                Bạn cần thay thế bằng cấu trúc HTML của thẻ sản phẩm (product card) mà bạn muốn --}}
                                {{-- Đây là cấu trúc HTML đầy đủ từ app.js --}}
                                <div class="grid__column-4">
                                    <div class="home-product-item">
                                        <a href="{{ route('product.detail', ['id' => $prodId]) }}" class="block">
                                            <div class="home-product-item__img"
                                                style="background-image: url({{ $imageUrl }});"></div>
                                        </a>
                                        <div class="home-product-item__body">
                                            <div class="home-product-name__wrap">
                                                {{-- Dùng TenSP (key cũ) hoặc name (key app.js) --}}
                                                <a href="{{ route('product.detail', ['id' => $prodId]) }}">
                                                    <h4 class="home-product-item__name">
                                                        {{ $product['TenSP'] ?? ($product['name'] ?? 'N/A') }}</h4>
                                                </a>
                                                {{-- TAG BẢO HÀNH / NEW --}}
                                                <span class="home-product-item__tag">
                                                    {{-- Hiển thị nhãn đã được tính toán ở Controller --}}
                                                    {{ $product['warranty_label'] ?? 'New' }}
                                                </span>
                                            </div>
                                            <div class="home-product-item__rating">
                                                <i class="fa-solid fa-star"></i>
                                                <span>({{ $product['rating'] ?? '0' }})</span>
                                            </div>
                                            {{-- Hiện tên danh mục (VD: Electronics) --}}
                                            <p class="home-product-item__category">
                                                {{ $product['categoryName'] ?? 'Phụ kiện' }}
                                            </p>
                                            <div class="home-product-item__footer">
                                                {{-- Dùng GiaBan (key cũ) hoặc price (key app.js) --}}
                                                <span
                                                    class="home-product-item__price">${{ number_format($product['GiaBan'] ?? ($product['price'] ?? 0)) }}</span>
                                                <span
                                                    class="home-product-item__stock">{{ ($product['stock'] ?? ($product['SoLuongTon'] ?? 0)) > 0 ? 'Còn Hàng' : 'Hết Hàng' }}</span>
                                            </div>
                                            <button
                                                class="home-product-item__button btn_css btn--primary_css add-to-cart-btn"
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

                {{-- =========================================== --}}
                {{-- PHẦN PHÂN TRANG (PAGINATION) --}}
                {{-- =========================================== --}}
                @if (isset($totalPages) && $totalPages > 1)
                    <div class="pagination">
                        <ul class="pagination-list">

                            {{-- Nút PREV --}}
                            @if ($currentPage > 1)
                                <li class="pagination-item">
                                    <a href="{{ route('home', array_merge(request()->all(), ['page' => $currentPage - 1])) }}"
                                        class="pagination-link">
                                        <i class="fa-solid fa-angle-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Các số trang --}}
                            @for ($i = 1; $i <= $totalPages; $i++)
                                <li class="pagination-item {{ $i == $currentPage ? 'pagination-item--active' : '' }}">
                                    <a href="{{ route('home', array_merge(request()->all(), ['page' => $i])) }}"
                                        class="pagination-link">
                                        {{ $i }}
                                    </a>
                                </li>
                            @endfor

                            {{-- Nút NEXT --}}
                            @if ($currentPage < $totalPages)
                                <li class="pagination-item">
                                    <a href="{{ route('home', array_merge(request()->all(), ['page' => $currentPage + 1])) }}"
                                        class="pagination-link">
                                        <i class="fa-solid fa-angle-right"></i>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                @endif

                {{-- Thêm CSS trực tiếp vào đây hoặc vào file CSS riêng --}}
                <style>
                    .pagination {
                        display: flex;
                        justify-content: center;
                        margin-top: 40px;
                        margin-bottom: 20px;
                    }

                    .pagination-list {
                        display: flex;
                        list-style: none;
                        padding: 0;
                        gap: 8px;
                    }

                    .pagination-link {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        min-width: 40px;
                        height: 40px;
                        text-decoration: none;
                        font-size: 1.4rem;
                        color: #999;
                        border-radius: 4px;
                        background-color: #fff;
                        /* Màu nền trắng */
                        border: 1px solid #eee;
                        transition: all 0.2s ease;
                    }

                    .pagination-link:hover {
                        background-color: #fafafa;
                        color: var(--primary-color, #ee4d2d);
                        /* Màu cam Shopee hoặc màu chủ đạo của bạn */
                    }

                    .pagination-item--active .pagination-link {
                        background-color: var(--primary-color, #ee4d2d);
                        /* Màu chủ đạo */
                        color: white;
                        border-color: var(--primary-color, #ee4d2d);
                    }

                    .pagination-item--active .pagination-link:hover {
                        background-color: #d73211;
                        /* Màu đậm hơn khi hover active */
                        color: white;
                    }
                </style>

            </div>
        </div>
    </div>

@endsection
