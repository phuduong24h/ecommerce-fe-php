@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">

    <h2 class="text-2xl font-bold mb-6">Thanh Toán</h2>

    {{-- =======================
         1. THÔNG TIN NGƯỜI MUA
        ======================= --}}
    <h4 class="font-semibold mb-3">Thông Tin Giao Hàng</h4>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <label class="text-sm text-gray-500">Họ và Tên</label>
            <p class="text-lg font-medium">{{ $user['name'] ?? '' }}</p>
        </div>
        <div>
            <label class="text-sm text-gray-500">Email</label>
            <p class="text-lg font-medium">{{ $user['email'] ?? '' }}</p>
        </div>
        <div>
            <label class="text-sm text-gray-500">Số điện thoại</label>
            <p class="text-lg font-medium">{{ $user['phone'] ?? '' }}</p>
        </div>
        <div>
            <label class="text-sm text-gray-500">Địa chỉ</label>
            <p class="text-lg font-medium">{{ $user['address'] ?? '' }}</p>
        </div>
    </div>

    <hr class="my-6">

    {{-- ======================
         2. DANH SÁCH SẢN PHẨM
       ====================== --}}
    <h4 class="font-semibold mb-3">Sản Phẩm Trong Giỏ</h4>
    {{-- sửa ở đây --}}
    <div class="border rounded-lg p-4 mb-6">
    @foreach($cart as $item)
        <div class="flex justify-between py-2 border-b last:border-0">
            {{-- CỘT TÊN SẢN PHẨM --}}
            <div class="flex flex-col">
                <span class="font-medium">
                    {{ $item['name'] }} 
                    <span class="text-gray-500 text-sm">(x{{ $item['quantity'] }})</span>
                </span>
                
                {{-- 🟢 THÊM ĐOẠN NÀY ĐỂ HIỆN VARIANT --}}
                @if(!empty($item['variant']))
                    <span class="text-xs text-gray-500 mt-1">
                        Phân loại: <span class="font-semibold text-cyan-600">{{ $item['variant'] }}</span>
                    </span>
                @endif
            </div>

            {{-- CỘT GIÁ --}}
            <span class="font-semibold text-gray-700">
                ${{ number_format($item['price'] * $item['quantity'], 2) }}
            </span>
        </div>
    @endforeach
</div>

    {{-- Subtotal --}}
    <div class="flex justify-between mb-2">
        <span>Tạm tính</span>
        <strong>${{ number_format($subtotal, 2) }}</strong>
    </div>

    {{-- Shipping --}}
    <div class="flex justify-between mb-2">
        <span>Vận chuyển</span>
        <strong class="text-green-600">FREE</strong>
    </div>

    {{-- Tax --}}
    <div class="flex justify-between mb-4">
        <span>Thuế</span>
        <strong>$9.60</strong>
    </div>

    {{-- Total --}}
    <div class="flex justify-between text-lg font-bold text-blue-600 mb-6">
        <span>Tổng cộng</span>
        <span>${{ number_format($subtotal + 9.6, 2) }}</span>
    </div>

    {{-- ======================
         3. PHƯƠNG THỨC THANH TOÁN
       ====================== --}}
    <h4 class="font-semibold mb-3">Phương Thức Thanh Toán</h4>

    <select id="payment_method" class="form-select mb-6">
        <option value="CASH">Tiền mặt</option>
        <option value="BANK">Chuyển khoản</option>
        <option value="MOMO">Momo</option>
        <option value="ZALO_PAY">ZaloPay</option>
    </select>

    {{-- ======================
         4. BUTTON XÁC NHẬN
       ====================== --}}
    <button id="btn-checkout"
            class="btn btn-primary w-100 py-3 fw-bold">
        Xác Nhận Thanh Toán
    </button>
    <a href="/cart" 
        class="btn btn-outline-secondary w-100 py-3 fw-bold mt-3">
            ← Quay Lại Giỏ Hàng
    </a>


</div>

<script>
document.getElementById("btn-checkout").addEventListener("click", function () {

    fetch("/checkout/submit", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            payment_method: document.getElementById("payment_method").value
        })
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {
            alert("Thanh toán thành công!");
            window.location.href = "/account/orders"; 
        } else {
            alert("Lỗi thanh toán!");
        }

    });
});
</script>
@endsection
