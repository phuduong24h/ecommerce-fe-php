@extends('layouts.admin')
@section('title', 'Chi tiết Đơn Hàng')

@section('content')
<div class="container mx-auto p-6 max-w-lg">
    <h1 class="text-2xl font-bold mb-6">Chi tiết Đơn Hàng</h1>

    <div class="bg-white p-6 rounded shadow space-y-4">
        <!-- Mã đơn hàng -->
        <div>
            <label class="block font-medium">Mã đơn hàng</label>
            <input type="text" value="{{ $order['id'] }}" class="w-full border rounded p-2 bg-gray-100" readonly>
        </div>

        <!-- Khách hàng -->
        <div>
            <label class="block font-medium">Khách hàng</label>
            <input type="text" value="{{ $order['userName'] ?? $order['customer'] }}" class="w-full border rounded p-2 bg-gray-100" readonly>
        </div>

        <!-- Ngày đặt -->
        <div>
            <label class="block font-medium">Ngày đặt</label>
            <input type="text" value="{{ isset($order['createdAt']) ? \Carbon\Carbon::parse($order['createdAt'])->format('Y-m-d H:i') : '' }}" class="w-full border rounded p-2 bg-gray-100" readonly>
        </div>

        <!-- Tổng tiền -->
        <div>
            <label class="block font-medium">Tổng tiền</label>
            <input type="text" value="${{ number_format($order['totalAmount'] ?? 0, 2) }}" class="w-full border rounded p-2 bg-gray-100" readonly>
        </div>

        <!-- Trạng thái -->
        <div>
            <label class="block font-medium">Trạng thái</label>
            <input type="text" value="{{ ucfirst(strtolower($order['status'] ?? 'pending')) }}" class="w-full border rounded p-2 bg-gray-100" readonly>
        </div>

        <!-- Sản phẩm -->
        <div>
            <label class="block font-medium">Sản phẩm</label>
            <ul class="border rounded p-2 bg-gray-50">
                @foreach($order['items'] ?? [] as $item)
                    <li>{{ $item['name'] }} - SL: {{ $item['quantity'] }} - Giá: ${{ $item['price'] }}</li>
                @endforeach
                @if(empty($order['items']))
                    <li class="text-gray-500">Chưa có sản phẩm</li>
                @endif
            </ul>
        </div>

        <!-- Back button -->
        <div class="flex justify-end">
            <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:underline">Quay lại</a>
        </div>
    </div>
</div>
@endsection
