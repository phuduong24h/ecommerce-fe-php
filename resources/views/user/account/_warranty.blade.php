@forelse($warranties as $warranty)
<div class="border rounded-lg p-6 mb-4 hover:shadow-md transition-shadow">
    <div class="flex items-start gap-6">

        <!-- Icon -->
        <div class="flex-shrink-0">
            <div class="w-16 h-16 bg-cyan-100 rounded-lg flex items-center justify-center">
                <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-grow">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $warranty['productName'] ?? 'Sản phẩm không xác định' }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        Số Serial: {{ $warranty['productSerial'] ?? '---' }}
                    </p>
                </div>

                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    {{ strtoupper($warranty['status'] ?? 'PENDING') }}
                </span>
            </div>

            <!-- Warranty Details -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Ngày Mua</p>
                    <p class="font-medium text-gray-900">
                        {{ $warranty['purchasedAt'] ?? '---' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Bảo Hành Hết Hạn</p>
                    <p class="font-medium text-gray-900">
                        @php
                            $duration = $warranty['warrantyPolicy']['durationDays'] ?? 0;
                            $start = $warranty['purchasedAt'] ?? null;
                            echo $start ? date('Y-m-d', strtotime("+$duration days", strtotime($start))) : '---';
                        @endphp
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@empty
<div class="text-center py-12">
    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
    </svg>
    <p class="text-gray-500">Bạn chưa có sản phẩm bảo hành nào</p>
</div>
@endforelse
