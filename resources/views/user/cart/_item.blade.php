{{-- resources/views/user/cart/_item.blade.php --}}
@php
    // Dùng quantity từ API (không phải qty)
    $itemTotal = (isset($item['price']) && isset($item['quantity'])) 
        ? ($item['price'] * $item['quantity']) 
        : 0;
@endphp

<div class="d-flex align-items-center justify-content-between p-3 border-bottom cart-item"
     data-index="{{ $index }}">

    {{-- 1. CỘT TRÁI: ẢNH + THÔNG TIN --}}
    <div class="d-flex align-items-center flex-grow-1">
        
        {{-- Wrapper cố định khung ảnh: Đảm bảo ảnh luôn vuông, không bị méo --}}
        <div class="flex-shrink-0 rounded-3 overflow-hidden border bg-white d-flex align-items-center justify-content-center" 
             style="width: 80px; height: 80px;">
            <img src="{{ $item['image'] ?? '' }}" 
                 style="width: 100%; height: 100%; object-fit: cover;" 
                 alt="{{ $item['name'] ?? '' }}">
        </div>

        {{-- Thông tin sản phẩm --}}
        <div class="ms-3">
            <h6 class="mb-1 text-truncate" style="max-width: 250px;">
                {{ $item['name'] ?? 'Tên sản phẩm' }}
            </h6>
            
            {{-- Variant --}}
            @if(!empty($item['variant']))
                <div class="mb-1">
                    <span class="badge bg-light text-dark border fw-normal">
                        {{ $item['variant'] }}
                    </span>
                </div>
            @endif

            {{-- Đơn giá --}}
            <p class="text-primary mb-0 small">
                ${{ number_format($item['price'] ?? 0, 2) }}
            </p>
        </div>
    </div>

    {{-- 2. CỘT PHẢI: SỐ LƯỢNG + TỔNG TIỀN + NÚT XÓA --}}
    <div class="d-flex align-items-center">
        
        {{-- Input số lượng --}}
        <div class="input-group input-group-sm me-4" style="width: 110px;">
            <button type="button" class="btn btn-outline-secondary btn-minus">-</button>
            <input type="text" class="form-control text-center quantity-input"
                   value="{{ $item['quantity'] ?? 1 }}" readonly>
            <button type="button" class="btn btn-outline-secondary btn-plus">+</button>
        </div>

        {{-- Tổng tiền: Set width cố định để không bị nhảy layout khi số to lên --}}
        <div class="text-end me-3" style="width: 100px;">
            <strong class="text-dark item-total d-block">
                ${{ number_format($itemTotal, 2) }}
            </strong>
        </div>

        {{-- Nút xóa --}}
        <button type="button" class="btn btn-link text-danger trash-btn p-0" title="Xóa">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>

</div>