{{-- file _warranty.blade.php --}}
<div class="grid grid-cols-1 gap-4"> {{-- Thêm Grid container để quản lý các thẻ --}}
    @forelse($warranties as $warranty)
        {{-- Thêm class 'bg-white' và 'shadow-sm' để tạo khung rõ ràng --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
            
            <div class="flex items-start gap-4"> {{-- Giảm gap từ 6 xuống 4 --}}

                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center"> {{-- Giảm size icon xuống 12 --}}
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>

                <div class="flex-1 min-w-0"> {{-- flex-1 và min-w-0 giúp text truncate hoạt động --}}
                    
                    <div class="flex justify-between items-start mb-2"> {{-- Giảm mb xuống 2 --}}
                        <div class="mr-2">
                            <h3 class="text-base font-semibold text-gray-900 truncate"> {{-- Thêm truncate --}}
                                {{ $warranty['productName'] ?? 'Sản phẩm không xác định' }}
                            </h3>
                            <p class="text-xs text-gray-500"> {{-- Giảm text size xuống xs --}}
                                Serial: <span class="font-mono">{{ $warranty['productSerial'] ?? '---' }}</span>
                            </p>
                        </div>

                        <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                            {{ strtoupper($warranty['status'] ?? 'PENDING') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 mt-3 border-t pt-3 border-gray-50">
                        <div>
                            <p class="text-xs text-gray-500">Ngày Mua</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{-- Format ngày tháng cho gọn --}}
                                {{ isset($warranty['purchasedAt']) ? date('d/m/Y', strtotime($warranty['purchasedAt'])) : '---' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Hết Hạn Bảo Hành</p>
                            <p class="text-sm font-medium text-gray-900">
                                @php
                                    $duration = $warranty['warrantyPolicy']['durationDays'] ?? 0;
                                    $start = $warranty['purchasedAt'] ?? null;
                                    echo $start ? date('d/m/Y', strtotime("+$duration days", strtotime($start))) : '---';
                                @endphp
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-10 bg-white rounded-lg border border-dashed border-gray-300">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <p class="text-gray-500 text-sm">Bạn chưa có sản phẩm bảo hành nào</p>
        </div>
    @endforelse
</div>