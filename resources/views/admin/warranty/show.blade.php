@extends('layouts.admin')
@section('title', 'Chi tiết Yêu cầu Bảo hành')

@section('content')
    <div class="container mx-auto p-6 max-w-lg">
        <h1 class="text-2xl font-bold mb-6">Chi tiết Yêu cầu Bảo hành</h1>

        <div class="bg-white p-6 rounded shadow space-y-4">
            <!-- Mã yêu cầu -->
            <div>
                <label class="block font-medium">ID Yêu cầu</label>
                <input type="text" value="{{ $claim['id'] ?? $claim['_id'] ?? '' }}"
                    class="w-full border rounded p-2 bg-gray-100" readonly>
            </div>

            <!-- Sản phẩm -->
            <div>
                <label class="block font-medium">Sản phẩm</label>
                <input type="text" value="{{ $claim['productName'] ?? '' }}" class="w-full border rounded p-2 bg-gray-100"
                    readonly>
            </div>

            <!-- Serial -->
            <div>
                <label class="block font-medium">Serial</label>
                <input type="text" value="{{ $claim['productSerial'] ?? '' }}" class="w-full border rounded p-2 bg-gray-100"
                    readonly>
            </div>

            <!-- Khách hàng -->
            <div>
                <label class="block font-medium">Khách hàng</label>
                <input type="text" value="{{ $claim['userName'] ?? '' }}" class="w-full border rounded p-2 bg-gray-100"
                    readonly>
            </div>

            <!-- Ngày nộp -->
            <div>
                <label class="block font-medium">Ngày nộp</label>
                <input type="text"
                    value="{{ isset($claim['createdAt']) ? \Carbon\Carbon::parse($claim['createdAt'])->format('d/m/Y H:i') : '' }}"
                    class="w-full border rounded p-2 bg-gray-100" readonly>
            </div>

            <!-- Vấn đề -->
            <div>
                <label class="block font-medium">Mô tả vấn đề</label>
                <textarea class="w-full border rounded p-2 bg-gray-50" rows="3"
                    readonly>{{ $claim['issueDesc'] ?? '' }}</textarea>
            </div>

            <!-- Trạng thái -->
            <div>
                <label class="block font-medium">Trạng thái</label>
                <input type="text" value="{{ $claim['status'] ?? '' }}" class="w-full border rounded p-2 bg-gray-100"
                    readonly>
            </div>

            <!-- Hình ảnh -->
            <div>
                <label class="block font-medium mb-2">Hình ảnh</label>
                <div class="flex flex-wrap gap-4">
                    @forelse($claim['images'] ?? [] as $img)
                        <div class="border rounded p-1">
                            @if(str_starts_with($img, 'data:image'))
                                <img src="{{ $img }}" alt="Ảnh bảo hành" class="w-48 h-48 object-contain border rounded bg-gray-50">

                            @else
                                <img src="data:image/jpeg;base64,{{ $img }}" alt="Ảnh bảo hành"
                                    class="max-w-xs max-h-48 object-contain">
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500">Chưa có hình ảnh</p>
                    @endforelse
                </div>
            </div>

            <!-- Back button -->
            <div class="flex justify-end mt-4">
                <a href="{{ route('admin.warranty.index') }}" class="text-gray-600 hover:underline">Quay lại</a>
            </div>
        </div>
    </div>
@endsection