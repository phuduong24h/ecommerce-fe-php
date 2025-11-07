@extends('layouts.app')

@section('content')
<div class="row g-4">
    <!-- Danh sách sản phẩm -->
    <div class="col-lg-8">
        <h4 class="mb-4 text-center text-lg-start">Giỏ Hàng</h4>

        @if(empty($cart))
            <div class="text-center py-5">
                <p>Giỏ hàng trống</p>
                <a href="/" class="btn btn-primary">Tiếp tục mua sắm</a>
            </div>
        @else
            <div class="bg-white rounded-3 shadow-sm p-4">
                @foreach($cart as $index => $item)
                    @include('cart._item', compact('item', 'index'))
                @endforeach
            </div>
        @endif
    </div>

    <!-- Tổng tiền -->
    <div class="col-lg-4">
        <div class="bg-white rounded-3 shadow-sm p-4 sticky-top" style="top: 20px;">
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
                Tiến Hành Thanh Toán →
            </a>
        </div>
    </div>
</div>
@endsection