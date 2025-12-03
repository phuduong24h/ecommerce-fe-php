@extends('layouts.admin')

@section('title', 'Thêm khuyến mãi')

@section('content')
    <div class="container mx-auto p-6 max-w-lg">
        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800 border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800 border border-red-300">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800 border border-red-300">
                <strong>Lỗi xảy ra:</strong>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Thêm khuyến mãi mới</h1>
            <p class="text-gray-500 text-sm">Điền thông tin để tạo khuyến mãi mới</p>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.settings.promotions.store') }}" method="POST"
            class="space-y-4 bg-white p-6 rounded-lg shadow">
            @csrf

            <!-- Code -->
            <div>
                <label class="block text-sm font-medium mb-1">Mã khuyến mãi *</label>
                <input type="text" name="code" value="{{ old('code') }}" required class="w-full border rounded px-3 py-2">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium mb-1">Mô tả</label>
                <textarea name="description" rows="3"
                    class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
            </div>

            <!-- Discount -->
            <div>
                <label class="block text-sm font-medium mb-1">Giảm giá (%) *</label>
                <input type="number" name="discount" step="1" min="0" max="100" value="{{ old('discount') }}" required
                    class="w-full border rounded px-3 py-2">
            </div>

            <!-- Start Date -->
            <div>
                <label class="block text-sm font-medium mb-1">Ngày bắt đầu *</label>
                <input type="date" name="startDate" value="{{ old('startDate') }}" required
                    class="w-full border rounded px-3 py-2">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-sm font-medium mb-1">Ngày kết thúc *</label>
                <input type="date" name="endDate" value="{{ old('endDate') }}" required
                    class="w-full border rounded px-3 py-2">
            </div>

            <!-- Active -->
            <div class="flex items-center gap-2">
                <input type="checkbox" name="isActive" value="1" {{ old('isActive', true) ? 'checked' : '' }}>
                <span class="text-sm font-medium">Kích hoạt</span>
            </div>

            <!-- === Chọn sản phẩm === -->
            <div class="border-t pt-4">
                <h2 class="text-lg font-semibold mb-2">Áp dụng cho sản phẩm</h2>
                <div id="product-select-list" class="space-y-3">
                    @if(old('productIds'))
                        @foreach(old('productIds') as $i => $oldProductId)
                            <div class="flex gap-2 items-center">
                                <select name="productIds[]" class="w-full border rounded px-3 py-2">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p['id'] }}" {{ $oldProductId == $p['id'] ? 'selected' : '' }}>
                                            {{ $p['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="remove-prod text-red-500 text-sm">X</button>
                            </div>
                        @endforeach
                    @else
                        <div class="flex gap-2 items-center">
                            <select name="productIds[]" class="w-full border rounded px-3 py-2">
                                <option value="">-- Chọn sản phẩm --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="remove-prod text-red-500 text-sm">X</button>

                        </div>
                    @endif
                </div>

                <button type="button" id="addProductBtn" class="text-purple-600 text-sm mt-2 hover:underline">
                    + Thêm sản phẩm
                </button>

                @error('productIds')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 pt-4">
                <a href="{{ route('admin.settings.index', ['tab' => 'promotions']) }}"
                    class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Hủy</a>
                <button type="submit" class="px-4 py-2 rounded bg-purple-500 text-white hover:bg-purple-600">Tạo khuyến
                    mãi</button>
            </div>
        </form>
    </div>

    <script>
        const productList = document.getElementById('product-select-list');
        const btnAdd = document.getElementById('addProductBtn');

        btnAdd.addEventListener('click', () => {
            const div = document.createElement('div');
            div.className = "flex gap-2 items-center mt-2";
            div.innerHTML = `
                    <select name="productIds[]" class="w-full border rounded px-3 py-2">
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach($products as $p)
                            <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="remove-prod text-red-500 text-sm">X</button>
                `;
            productList.appendChild(div);
        });

        // Xóa select
        productList.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-prod')) {
                e.target.closest('div').remove();
            }
        });
    </script>
@endsection