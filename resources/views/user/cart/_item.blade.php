{{-- resources/views/user/cart/_item.blade.php --}}
@php
    // Dùng quantity từ API (không phải qty)
    $itemTotal = (isset($item['price']) && isset($item['quantity'])) 
        ? ($item['price'] * $item['quantity']) 
        : 0;
@endphp

<div class="d-flex align-items-center justify-content-between p-3 border-bottom cart-item"
     data-index="{{ $index }}">

    <div class="d-flex align-items-center gap-3 flex-grow-1">
        <img src="{{ $item['image'] ?? '' }}" class="rounded-3" width="80" height="80" style="object-fit: cover;" alt="{{ $item['name'] ?? '' }}">
        <div>
            <h6 class="mb-1">{{ $item['name'] ?? 'Tên sản phẩm' }}</h6>

            {{-- GIÁ THEO USD --}}
            <p class="text-primary mb-0">
                ${{ number_format($item['price'] ?? 0, 2) }}
            </p>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="input-group input-group-sm" style="width: 110px;">
            <button type="button" class="btn btn-outline-secondary btn-minus">-</button>
            <input type="text" class="form-control text-center quantity-input"
                   value="{{ $item['quantity'] ?? 1 }}" readonly>
            <button type="button" class="btn btn-outline-secondary btn-plus">+</button>
        </div>

        {{-- ITEM TOTAL THEO USD --}}
        <strong class="text-dark item-total">
            ${{ number_format($itemTotal, 2) }}
        </strong>

        <button type="button" class="btn btn-link text-danger trash-btn p-0 ms-2">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>

</div>
