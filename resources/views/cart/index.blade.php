{{-- resources/views/cart/index.blade.php --}}
@extends('layouts.app')

@section('content')
<!-- Tiêu đề nằm ngoài, không ảnh hưởng layout -->
<h4 class="mb-4">Giỏ Hàng</h4>

<div class="row g-4" style="align-items: stretch;">
    <!-- Danh sách sản phẩm -->
    <div class="col-lg-8 d-flex flex-column">
        <!-- Container giữ layout (luôn tồn tại) -->
        <div class="bg-white rounded-3 shadow-sm p-4 flex-grow-1" id="cart-items-container">
            @if(empty($cart))
                <!-- Giỏ hàng trống -->
                <div class="text-center d-flex flex-column justify-content-center" style="min-height: 400px;">
                    <div style="margin-bottom: 30px;">
                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="60" cy="60" r="60" fill="#dce4f0"/>
                            <path d="M50 35H70C72.21 35 74 36.79 74 39V85C74 88.31 71.31 91 68 91H52C48.69 91 46 88.31 46 85V39C46 36.79 47.79 35 50 35Z" stroke="#8fa3c4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            <path d="M54 35C54 32.24 56.24 30 59 30C61.76 30 64 32.24 64 35" stroke="#8fa3c4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h5 class="mb-2">Giỏ hàng trống</h5>
                    <p class="text-muted mb-4">Thêm sản phẩm để bắt đầu mua sắm!</p>
                    <a href="/" class="btn btn-primary">Tiếp tục mua sắm</a>
                </div>
            @else
                <!-- Danh sách sản phẩm -->
                @foreach($cart as $index => $item)
                    @include('cart._item', compact('item', 'index'))
                @endforeach
            @endif
        </div>
    </div>

    <!-- Tổng tiền: chỉ hiện khi có sản phẩm -->
    @if(!empty($cart))
    <div class="col-lg-4 d-flex">
        <div class="bg-white rounded-3 shadow-sm p-4 w-100" style="position: sticky; top: 20px; align-self: flex-start;">
            <h5 class="mb-3">Tổng Đơn Hàng</h5>
            <div class="d-flex justify-content-between mb-2">
                <span>Tạm Tính</span>
                <strong id="subtotal">${{ number_format($subtotal, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Vận Chuyển</span>
                <strong class="text-success">MIỄN PHÍ</strong>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span>Thuế</span>
                <strong>$9.60</strong>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-4">
                <strong>Tổng Cộng</strong>
                <strong class="text-primary fs-5" id="total">${{ number_format($subtotal + 9.60, 2) }}</strong>
            </div>
            <a href="#" class="btn btn-info text-white w-100 fw-bold">
                Tiến Hành Thanh Toán
            </a>
        </div>
    </div>
    @endif
</div>
@endsection