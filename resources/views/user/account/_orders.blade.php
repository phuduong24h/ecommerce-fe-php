@forelse($orders as $order)
<div class="border rounded-lg p-6 mb-4 hover:shadow-md transition-shadow">

    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Đơn Hàng {{ $order['id'] }}</h3>
            <p class="text-sm text-gray-500">
                {{-- SỬA ĐOẠN NÀY: Chuyển sang múi giờ Asia/Ho_Chi_Minh --}}
                Đặt vào {{ \Carbon\Carbon::parse($order['createdAt'])->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="text-right">

            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                {{-- Chỉnh Logic chọn màu: Nếu DELIVERED thì màu xanh, còn lại là màu tím --}}
                {{ ($order['status'] ?? '') === 'DELIVERED' 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-purple-100 text-purple-800' }}">
                
                {{-- Logic hiển thị chữ: Kiểm tra trạng thái để in ra tiếng Việt --}}
                {{ ($order['status'] ?? '') === 'DELIVERED' ? 'Đã Giao' : 'Đã Gửi' }}
            </span>

            <p class="text-lg font-bold text-cyan-600 mt-2">
                ${{ number_format($order['totalAmount'] ?? 0, 2) }}
            </p>
        </div>
    </div>

    <div class="border-t pt-4">
    @foreach($order['items'] as $item)
    <div class="flex justify-between py-2">

        <div class="flex flex-col">
            <span class="text-gray-700 font-medium">
                {{ $item['name'] ?? 'Sản phẩm' }}
            </span>

            {{-- 🟢 HIỂN THỊ VARIANT Ở LỊCH SỬ ĐƠN HÀNG --}}
            @if(!empty($item['variant']))
                <span class="text-xs text-gray-500">
                    Phân loại: {{ $item['variant'] }}
                </span>
            @endif

            <span class="text-xs text-gray-400 mt-0.5">
                Số lượng đã mua: {{ $item['quantity'] ?? 1 }}
            </span>
        </div>

        <span class="text-gray-900 font-medium">
            ${{ number_format($item['price'] ?? 0, 2) }}
        </span>

    </div>
    @endforeach
</div>

</div>

@empty

<div class="text-center py-12">
    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
    </svg>
    <p class="text-gray-500">Bạn chưa có đơn hàng nào</p>
</div>

@endforelse