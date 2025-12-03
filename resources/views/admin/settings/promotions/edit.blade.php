@extends('layouts.admin')

@section('title', 'Sửa khuyến mãi')

@section('content')
    <div class="container mx-auto p-6 max-w-lg">

        <h1 class="text-2xl font-bold mb-6">Sửa khuyến mãi</h1>
        <!-- Hiển thị flash messages -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.settings.promotions.update', $promotion['id']) }}" method="POST"
            class="space-y-4 bg-white p-6 rounded-lg shadow">
            @csrf
            @method('PUT')

            <!-- Code -->
            <div>
                <label class="block text-sm font-medium mb-1">Mã khuyến mãi *</label>
                <input type="text" name="code" value="{{ old('code', $promotion['code']) }}" required
                    class="w-full border rounded px-3 py-2">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium mb-1">Mô tả</label>
                <textarea name="description" rows="3"
                    class="w-full border rounded px-3 py-2">{{ old('description', $promotion['description']) }}</textarea>
            </div>

            <!-- Discount -->
            <div>
                <label class="block text-sm font-medium mb-1">Giảm giá (%) *</label>
                <input type="number" name="discount" step="1" min="0" max="100"
                    value="{{ old('discount', $promotion['discount']) }}" required class="w-full border rounded px-3 py-2">
            </div>

            <!-- Start Date -->
            <div>
                <label class="block text-sm font-medium mb-1">Ngày bắt đầu *</label>
                <input type="date" name="startDate" value="{{ old('startDate', substr($promotion['startDate'], 0, 10)) }}"
                    required class="w-full border rounded px-3 py-2">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-sm font-medium mb-1">Ngày kết thúc *</label>
                <input type="date" name="endDate" value="{{ old('endDate', substr($promotion['endDate'], 0, 10)) }}"
                    required class="w-full border rounded px-3 py-2">
            </div>

            <!-- Active -->
            <div class="flex items-center gap-2">
                <input type="checkbox" name="isActive" value="1" {{ old('isActive', $promotion['isActive']) ? 'checked' : '' }}>
                <span class="text-sm font-medium">Kích hoạt</span>
            </div>

            <!-- Chọn sản phẩm -->
            <div class="border-t pt-4">
                <h2 class="text-lg font-semibold mb-2">Áp dụng cho sản phẩm</h2>
                <div id="product-select-list" class="space-y-3">
                    @php
                        $promotion = $promotion ?? ['id' => null];

                        $selectedIds = old(
                            'productIds',
                            collect($products)
                                ->filter(fn($p) => ($p['promotionId'] ?? null) === $promotion['id'])
                                ->pluck('id')
                                ->all()
                        );

                        $selectedProducts = [];
                        foreach ($selectedIds as $id) {
                            $prod = collect($products)->firstWhere('id', $id);
                            if ($prod)
                                $selectedProducts[] = $prod;
                        }
                    @endphp
                    @if(count($selectedProducts) > 0)
                        @foreach($selectedProducts as $index => $prod)
                            <div class="flex gap-2 items-center">
                                <select name="productIds[]" class="w-full border rounded px-3 py-2">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p['id'] }}" {{ $p['id'] == $prod['id'] ? 'selected' : '' }}>
                                            {{ $p['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button"
                                    class="remove-prod text-red-500 text-sm {{ count($selectedProducts) == 1 ? 'hidden' : '' }}">X</button>
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
                            <button type="button" class="remove-prod text-red-500 text-sm hidden">X</button>
                        </div>
                    @endif
                </div>

                <button type="button" id="addProductBtn" class="text-purple-600 text-sm mt-2 hover:underline">+ Thêm sản
                    phẩm</button>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 pt-4">
                <a href="{{ route('admin.settings.index', ['tab' => 'promotions']) }}"
                    class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Hủy</a>
                <button type="submit" class="px-4 py-2 rounded bg-purple-500 text-white hover:bg-purple-600">Cập
                    nhật</button>
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

        productList.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-prod')) {
                e.target.closest('div').remove();
            }
        });

        // Loại bỏ select trống trước submit
        document.querySelector('form').addEventListener('submit', (e) => {
            document.querySelectorAll('select[name="productIds[]"]').forEach(s => {
                if (!s.value) s.remove();
            });
        });

    </script>
@endsection