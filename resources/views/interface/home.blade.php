{{-- 1. Báo cho Laravel dùng file Khung (Layout) của nhóm --}}
@extends('layouts.app')

{{-- 2. Bắt đầu nhét nội dung vào chỗ @yield('content') --}}
@section('content')

{{-- 3. Đây là toàn bộ nội dung trang chủ của bạn (lấy từ trangchu.blade.php) --}}
<div class="app__container">
    <div class="grid">
        <div class="grid__row app__content">
            <div class="grid__column-12">
                <div class="app__banner">
                    <p class="app__banner-heading">Chào Mừng Đến Cửa Hàng</p>
                    <p class="app__banner-text">Khám phá các sản phẩm công nghệ cao cấp với bảo hành đầy đủ</p>
                    <p class="app__banner-promo">Miễn phí vận chuyển cho đơn hàng trên $50</p>
                </div>

                <div class="app__search-container">
                    <div class="header__search-input-wrap">
                        <i class="header__search-input-icon fa-solid fa-magnifying-glass"></i>
                        <input type="text" class="header__search-input" placeholder="Tìm kiếm sản phẩm..." onkeyup="searchProducts()">
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
                </div>

                {{-- Nơi để hiển thị sản phẩm, giống hệt file cũ của bạn --}}
                <div class="home-product">
                    <div class="grid__row">
                        </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
async function searchProducts() {
    const keyword = document.getElementById("search-input").value.trim();
    if (!keyword) return;

    const res = await fetch(`http://127.0.0.1:3000/api/products/search?keyword=${keyword}`);
    const { data } = await res.json();

    console.log(data); // TODO: render ra giao diện
}
</script>


@endsection
{{-- 4. Kết thúc nhét nội dung --}}
