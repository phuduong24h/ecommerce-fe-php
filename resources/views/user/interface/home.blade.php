@extends('layouts.app')

@section('content')

{{-- CSS --}}
<style>
    .header__search-option { display: none; position: absolute; right: 0; top: 100%; width: 160px; list-style: none; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1); padding-left: 0; border-radius: 3px; background-color: white; z-index: 10; margin-top: 10px; animation: fadeIn ease-in 0.2s; }
    .header__search-select:hover .header__search-option { display: block; }
    .header__search-option-item { background-color: white; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; transition: background-color 0.1s; }
    .header__search-option-item:hover { background-color: #fafafa; cursor: pointer; color: #0891b2; }
    .header__search-option-item span { font-size: 1.4rem; color: #333; flex: 1; }
    
    /* BADGE GIẢM GIÁ */
    .product-badge { position: absolute; top: 0; left: 0; background-color: #dc2626; color: white; padding: 4px 8px; font-size: 1.2rem; font-weight: bold; border-bottom-right-radius: 8px; z-index: 2; box-shadow: 2px 2px 5px rgba(0,0,0,0.2); }
    .product-badge::after { content: ""; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background-color: #b91c1c; border-top-left-radius: 2px; }
    
    /* GIÁ GẠCH NGANG */
    .price-original { color: #9ca3af; text-decoration: line-through; font-size: 1.3rem; margin-right: 8px; }
    
    /* FLEX FOOTER */
    .home-product-item__footer { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; }
</style>

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
            <form class="app__search-container" action="{{ route('home') }}" method="GET" id="search-form">
                <input type="hidden" name="categoryId" id="search-category-input" value="{{ $categoryId ?? '' }}">
                <div class="header__search-input-wrap">
                    <i class="header__search-input-icon fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="header__search-input" placeholder="Tìm kiếm sản phẩm..." name="search" value="{{ $searchTerm ?? '' }}">
                </div>
                <div class="header__search-select">
                    <span class="header__search-select-label">{{ $currentCategoryName ?? 'Tất cả danh mục' }}</span>
                    <i class="header__search-select-icon fa-solid fa-angle-down"></i>
                    <ul class="header__search-option">
                        <li class="header__search-option-item" data-value=""><span>Tất cả danh mục</span></li>
                        @if(isset($categories) && count($categories) > 0)
                            @foreach($categories as $cat)
                                <li class="header__search-option-item" data-value="{{ $cat['id'] }}"><span>{{ $cat['name'] }}</span></li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <button type="submit" class="header__search-btn btn_css btn--primary_css" style="width: 60px; height: 34px; display: flex; align-items: center; justify-content: center;">
                    <i class="header__search-btn-icon fa-solid fa-magnifying-glass" style="color: white;"></i>
                </button>
            </form>

            {{-- LIST SẢN PHẨM --}}
            <div class="home-product">
                <div class="grid__row">
                    @if(isset($error) && $error)
                        <div class="col-12 text-center p-5"><h3 class="text-red-500">{{ $error }}</h3></div>
                    @elseif(empty($products))
                        <div class="col-12 text-center p-5"><h3 class="text-gray-500">Không tìm thấy sản phẩm nào.</h3></div>
                    @else
                        @foreach ($products as $product)
                            @php
                                // 1. Xử lý ảnh
                                $imageUrl = 'https://via.placeholder.com/300?text=No+Image';
                                if (!empty($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
                                    $firstImage = $product['images'][0];
                                    $imageUrl = is_array($firstImage) ? ($firstImage['url'] ?? $firstImage) : $firstImage;
                                } elseif (!empty($product['image'])) {
                                    $imageUrl = $product['image'];
                                }

                                $prodId = $product['id'] ?? $product['_id'] ?? 0;
                                
                                // 2. Logic Giá & Stock
                                $basePrice = $product['price'] ?? 0;
                                $productForCart = $product;
                                $currentStock = $product['stock'] ?? 0;

                                // Nếu có biến thể, lấy thông tin biến thể đầu tiên để hiển thị
                                if (!empty($product['variants']) && count($product['variants']) > 0) {
                                    $firstVariant = $product['variants'][0];
                                    $productForCart['selected_variant'] = $firstVariant;
                                    $currentStock = $firstVariant['stock'] ?? 0;
                                    // Lưu ý: Ở đây ta không cộng giá variant vào basePrice gửi đi để tránh lỗi logic
                                }
                                
                                // 3. CHUẨN BỊ JSON CHO NÚT BẤM
                                // - Gán stock để Controller kiểm tra
                                $productForCart['stock'] = $currentStock;
                                // - 🟢 QUAN TRỌNG: Gán lại giá GỐC. AddCartController sẽ tự tính toán khuyến mãi.
                                // Nếu gửi giá đã giảm lên, Controller sẽ giảm thêm lần nữa -> Sai.
                                $productForCart['price'] = $basePrice; 
                            @endphp

                            <div class="grid__column-4">
                                <div class="home-product-item relative">
                                    
                                    {{-- Badge giảm giá --}}
                                    @if($product['has_discount'])
                                        <div class="product-badge">Giảm {{ $product['discount_percent'] }}%</div>
                                    @endif

                                    <a href="{{ route('product.detail', ['id' => $prodId]) }}" class="block">
                                        <div class="home-product-item__img" style="background-image: url({{ $imageUrl }});"></div>
                                    </a>

                                    <div class="home-product-item__body">
                                        <div class="home-product-name__wrap">
                                            <a href="{{ route('product.detail', ['id' => $prodId]) }}">
                                                <h4 class="home-product-item__name" title="{{ $product['name'] }}">{{ $product['name'] }}</h4>
                                            </a>
                                            <span class="home-product-item__tag">{{ $product['warranty_label'] ?? 'New' }}</span>
                                        </div>

                                        <div class="home-product-item__rating">
                                            <i class="fa-solid fa-star"></i><span>({{ $product['rating'] ?? 0 }})</span>
                                        </div>

                                        {{-- FOOTER: GIÁ VÀ KHO --}}
                                        <div class="home-product-item__footer">
                                            {{-- Cột Trái: Giá --}}
                                            <div class="flex flex-col">
                                                @if($product['has_discount'])
                                                    <div class="flex items-center gap-2">
                                                        <span class="home-product-item__price text-red-600 font-bold text-lg">
                                                            ${{ number_format($product['final_price'], 2) }}
                                                        </span>
                                                        <span class="price-original text-gray-400 text-sm line-through">
                                                            ${{ number_format($product['original_price'], 2) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="home-product-item__price text-cyan-600 font-bold text-lg">
                                                        ${{ number_format($product['final_price'], 2) }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Cột Phải: Kho --}}
                                            <span class="home-product-item__stock text-xs font-medium {{ $currentStock > 0 ? 'text-green-600 bg-green-50 px-2 py-1 rounded' : 'text-red-600 bg-red-50 px-2 py-1 rounded' }}">
                                                {{ $currentStock > 0 ? 'Còn Hàng' : 'Hết Hàng' }}
                                            </span>
                                        </div>

                                        {{-- BUTTON THÊM GIỎ --}}
                                        <div class="mt-2">
                                            <button class="home-product-item__button btn_css btn--primary_css add-to-cart-btn w-full flex justify-center items-center gap-2 {{ $currentStock <= 0 ? 'opacity-50 cursor-not-allowed bg-gray-400' : '' }}"
                                                    data-product-json="{{ json_encode($productForCart) }}"
                                                    {{ $currentStock <= 0 ? 'disabled' : '' }}>
                                                @if($currentStock > 0)
                                                    <i class="home-product-item__cart fa-solid fa-cart-shopping"></i> Thêm vào Giỏ
                                                @else
                                                    Tạm Hết
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- PHÂN TRANG --}}
            @if(isset($totalPages) && $totalPages > 1)
            <div class="pagination">
                <ul class="pagination-list">
                    @if($currentPage > 1)
                        <li class="pagination-item"><a href="{{ route('home', array_merge(request()->all(), ['page' => $currentPage - 1])) }}" class="pagination-link"><i class="fa-solid fa-angle-left"></i></a></li>
                    @endif
                    @for($i = 1; $i <= $totalPages; $i++)
                        <li class="pagination-item {{ $i == $currentPage ? 'pagination-item--active' : '' }}"><a href="{{ route('home', array_merge(request()->all(), ['page' => $i])) }}" class="pagination-link">{{ $i }}</a></li>
                    @endfor
                    @if($currentPage < $totalPages)
                        <li class="pagination-item"><a href="{{ route('home', array_merge(request()->all(), ['page' => $currentPage + 1])) }}" class="pagination-link"><i class="fa-solid fa-angle-right"></i></a></li>
                    @endif
                </ul>
            </div>
            @endif

            <style>
                .pagination { display: flex; justify-content: center; margin: 40px 0 20px 0; }
                .pagination-list { display: flex; list-style: none; padding: 0; gap: 8px; }
                .pagination-link { display: flex; align-items: center; justify-content: center; min-width: 40px; height: 40px; text-decoration: none; font-size: 1.4rem; color: #999; border-radius: 4px; background-color: #fff; border: 1px solid #eee; transition: all 0.2s ease; }
                .pagination-link:hover { background-color: #fafafa; color: #0891b2; border-color: #0891b2; }
                .pagination-item--active .pagination-link { background-color: #0891b2; color: white; border-color: #0891b2; }
                .pagination-item--active .pagination-link:hover { background-color: #0e7490; }
            </style>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hiddenInput = document.getElementById('search-category-input');
        const searchForm = document.getElementById('search-form');
        const items = document.querySelectorAll('.header__search-option-item');
        items.forEach(item => {
            item.addEventListener('click', function (e) {
                const value = this.getAttribute('data-value');
                if(hiddenInput) hiddenInput.value = value;
                if(searchForm) searchForm.submit();
            });
        });
    });
</script>
@endsection