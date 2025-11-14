@extends('layouts.app')

@section('content')
<h4 class="mb-4">Giỏ Hàng</h4>

@if(empty($cart))
    {{-- GIỎ HÀNG TRỐNG --}}
    <div class="d-flex align-items-center justify-content-center bg-white rounded-3 shadow-sm" 
         style="min-height: 70vh; margin: 0 -12px; padding: 40px 20px;">
        <div class="text-center">
            <div class="d-flex justify-content-center mb-4">
                <svg width="120" height="120" viewBox="0 0 120 120" fill="none">
                    <circle cx="60" cy="60" r="60" fill="#eef3fb"/>
                    <path d="M40 45H80L75 85H45L40 45Z" stroke="#90a4c7" stroke-width="3" fill="none"/>
                    <circle cx="50" cy="95" r="5" fill="#90a4c7"/>
                    <circle cx="70" cy="95" r="5" fill="#90a4c7"/>
                </svg>
            </div>
            <h5 class="mb-2">Giỏ hàng trống</h5>
            <p class="text-muted mb-4">Thêm sản phẩm để bắt đầu mua sắm!</p>
            <a href="/" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    </div>
@else

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="bg-white rounded-3 shadow-sm p-4" id="cart-items-container">
                @foreach($cart as $index => $item)
                    @include('user.cart._item', compact('item', 'index'))
                @endforeach
            </div>
        </div>

        <div class="col-lg-4">
            <div class="bg-white rounded-3 shadow-sm p-4" id="cart-summary" style="position: sticky; top: 20px;">
                <h5 class="mb-3">Tổng Đơn Hàng</h5>

                {{-- TẠM TÍNH — USD --}}
                <div class="d-flex justify-content-between mb-2">
                    <span>Tạm Tính</span>
                    <strong id="subtotal">
                        ${{ number_format($subtotal, 2) }}
                    </strong>
                </div>

                {{-- VẬN CHUYỂN — Free --}}
                <div class="d-flex justify-content-between mb-2">
                    <span>Vận Chuyển</span>
                    <strong class="text-success">FREE</strong>
                </div>

                {{-- THUẾ — USD --}}
                <div class="d-flex justify-content-between mb-3">
                    <span>Thuế</span>
                    <strong>$9.60</strong>
                </div>

                <hr>

                {{-- TỔNG CỘNG — USD --}}
                <div class="d-flex justify-content-between mb-4">
                    <strong>Tổng Cộng</strong>
                    <strong id="total" class="text-primary fs-5">
                        ${{ number_format($subtotal + 9.6, 2) }}
                    </strong>
                </div>

                <a href="#" class="btn btn-info text-white w-100 fw-bold">
                    Tiến Hành Thanh Toán
                </a>
            </div>
        </div>
    </div>

@endif
@endsection

@push('scripts')
<script src="/js/cart.js"></script>
@endpush
