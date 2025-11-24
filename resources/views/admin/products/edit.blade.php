@extends('layouts.admin')

@section('title', 'Sửa sản phẩm')

@section('content')
<div class="container mx-auto p-6 space-y-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Sửa sản phẩm: {{ $product['name'] }}</h1>
        <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-800">&larr; Quay lại</a>
    </div>

    {{-- Thông báo Success --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Thông báo Error (Tổng hợp) --}}
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product['id']) }}" method="POST" class="bg-white p-6 rounded shadow space-y-6">
        @csrf
        @method('PUT')

        {{-- === PHẦN 1: THÔNG TIN CƠ BẢN (File 2) === --}}
        <div class="space-y-4 border-b pb-6">
            <h2 class="text-lg font-semibold text-gray-800">Thông tin chung</h2>

            {{-- Tên --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Tên sản phẩm *</label>
                <input type="text" name="name" value="{{ old('name', $product['name']) }}"
                       class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:ring-purple-500 focus:border-purple-500" required>
            </div>

            {{-- Mô tả --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                <textarea name="description" rows="3"
                          class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:ring-purple-500 focus:border-purple-500">{{ old('description', $product['description']) }}</textarea>
            </div>

            {{-- Giá & Tồn kho --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Giá bán *</label>
                    <input type="number" name="price" value="{{ old('price', $product['price']) }}" step="0.01"
                           class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tồn kho tổng *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product['stock']) }}"
                           class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
            </div>

            {{-- Danh mục, Trạng thái, Bảo hành (File 2) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Trạng thái --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                    <select name="isActive" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                        <option value="1" {{ old('isActive', $product['isActive'] ?? 1) == 1 ? 'selected' : '' }}>Đang bán</option>
                        <option value="0" {{ old('isActive', $product['isActive'] ?? 1) == 0 ? 'selected' : '' }}>Ngưng bán</option>
                    </select>
                </div>

                {{-- Danh mục --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Danh mục *</label>
                    <select name="categoryId" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['id'] }}" {{ old('categoryId', $product['categoryId']) == $category['id'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Bảo hành (File 2) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Chính sách bảo hành</label>
                    <select name="warrantyPolicyId" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">-- Chọn chính sách --</option>
                        @foreach($policies as $policy)
                            <option value="{{ $policy['id'] }}" {{ old('warrantyPolicyId', $product['warrantyPolicyId'] ?? '') == $policy['id'] ? 'selected' : '' }}>
                                {{ $policy['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- === PHẦN 2: BIẾN THỂ (Lấy Logic từ File 2) === --}}
        <div class="space-y-4 border-b pb-6">
            <h2 class="text-lg font-semibold text-gray-800">Biến thể sản phẩm</h2>
            
            <div id="variant-list" class="space-y-3">
                @foreach(old('variants', $product['variants'] ?? []) as $i => $variant)
                    <div class="border p-3 rounded space-y-2 variant-item bg-gray-50">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            <div>
                                <label class="text-xs text-gray-500">Tên biến thể</label>
                                <input type="text" name="variants[{{ $i }}][name]" value="{{ $variant['name'] ?? '' }}"
                                       placeholder="VD: Màu sắc" class="w-full border rounded px-2 py-1">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Giá trị</label>
                                <input type="text" name="variants[{{ $i }}][value]" value="{{ $variant['value'] ?? '' }}"
                                       placeholder="VD: Đỏ" class="w-full border rounded px-2 py-1">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Giá cộng thêm</label>
                                <input type="number" name="variants[{{ $i }}][price]" value="{{ $variant['price'] ?? 0 }}"
                                       class="w-full border rounded px-2 py-1">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Kho riêng</label>
                                <input type="number" name="variants[{{ $i }}][stock]" value="{{ $variant['stock'] ?? 0 }}"
                                       class="w-full border rounded px-2 py-1">
                            </div>
                        </div>
                        <button type="button" class="removeVariantBtn text-red-500 text-sm hover:underline">Xóa biến thể này</button>
                    </div>
                @endforeach
            </div>
            
            <button type="button" id="addVariantBtn" class="text-purple-600 text-sm font-medium hover:underline">+ Thêm biến thể</button>
        </div>

        {{-- === PHẦN 3: HÌNH ẢNH (Lấy Logic từ File 1 - Xịn hơn) === --}}
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-800">Hình ảnh sản phẩm</h2>
            
            <div id="image-list" class="space-y-2">
                @php
                    // Logic xử lý dữ liệu ảnh cũ (từ File 1)
                    $currentImages = old('images', $product['images'] ?? []);
                    if(is_string($currentImages)) $currentImages = [$currentImages];
                @endphp

                @foreach($currentImages as $img)
                    @if(!empty($img))
                    <div class="flex gap-2 items-center border p-2 rounded bg-gray-50">
                        {{-- Preview ảnh --}}
                        <img src="{{ $img }}" class="w-10 h-10 object-cover rounded border bg-white" onerror="this.style.display='none'">
                        
                        <div class="flex-1">
                             {{-- Nếu là Base64 dài quá thì ẩn đi cho gọn --}}
                             @if(strlen($img) > 200)
                                <input type="hidden" name="images[]" value="{{ $img }}">
                                <span class="text-xs text-gray-500 truncate block">Ảnh Base64 (Đã lưu)</span>
                             @else
                                <input type="text" name="images[]" value="{{ $img }}" class="block w-full border rounded px-2 py-1 text-sm text-gray-600">
                             @endif
                        </div>
                        
                        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm font-medium">Xóa</button>
                    </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-2 flex gap-4 items-center">
                <button type="button" id="addUrlBtn" class="text-blue-600 text-sm hover:underline font-medium">+ Thêm URL ảnh</button>
                <span class="text-gray-300">|</span>
                <label class="text-green-600 text-sm hover:underline cursor-pointer font-medium flex items-center gap-1">
                    <span>+ Upload ảnh từ máy</span>
                    <input type="file" id="fileUpload" class="hidden" accept="image/*">
                </label>
            </div>
        </div>

        {{-- Nút Submit --}}
        <div class="pt-4 border-t">
            <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-2 rounded shadow hover:from-blue-600 hover:to-blue-700 font-medium">
                Cập nhật sản phẩm
            </button>
        </div>
    </form>
</div>

{{-- === SCRIPTS (Gộp Logic của cả 2 file) === --}}
<script>
    // --- PHẦN 1: LOGIC BIẾN THỂ (Từ File 2) ---
    const variantList = document.getElementById('variant-list');
    let variantIndex = {{ count(old('variants', $product['variants'] ?? [])) }};
    // Nếu index chưa có gì thì bắt đầu từ 0, nếu có rồi thì nối tiếp (tránh trùng name)
    if(variantIndex === 0 && document.querySelectorAll('.variant-item').length > 0) {
        variantIndex = document.querySelectorAll('.variant-item').length;
    }

    // Thêm biến thể mới
    document.getElementById('addVariantBtn').addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'border p-3 rounded space-y-2 variant-item bg-gray-50 mt-2';
        div.innerHTML = `
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                <input type="text" name="variants[${variantIndex}][name]" placeholder="Tên (VD: Size)" class="w-full border rounded px-2 py-1">
                <input type="text" name="variants[${variantIndex}][value]" placeholder="Giá trị (VD: XL)" class="w-full border rounded px-2 py-1">
                <input type="number" name="variants[${variantIndex}][price]" placeholder="Giá cộng thêm" class="w-full border rounded px-2 py-1">
                <input type="number" name="variants[${variantIndex}][stock]" placeholder="Kho riêng" class="w-full border rounded px-2 py-1">
            </div>
            <button type="button" class="removeVariantBtn text-red-500 text-sm hover:underline mt-1">Xóa biến thể này</button>
        `;
        variantList.appendChild(div);
        variantIndex++;
    });

    // Xóa biến thể (Event Delegation)
    variantList.addEventListener('click', function(e){
        if(e.target && e.target.classList.contains('removeVariantBtn')){
            e.target.closest('.variant-item').remove();
        }
    });

    // Clean up biến thể rỗng trước khi submit (Logic hay từ File 2)
    document.querySelector('form').addEventListener('submit', function() {
        const variantItems = document.querySelectorAll('.variant-item');
        variantItems.forEach(item => {
            const name = item.querySelector('input[name*="[name]"]').value.trim();
            const value = item.querySelector('input[name*="[value]"]').value.trim();
            // Nếu không có tên và giá trị -> Xóa để không gửi rác lên server
            if(!name && !value){
                item.remove();
            }
        });
    });

    // --- PHẦN 2: LOGIC HÌNH ẢNH (Từ File 1 - Xịn hơn) ---
    const imageList = document.getElementById('image-list');

    // Thêm input URL thủ công
    document.getElementById('addUrlBtn').addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'flex gap-2 items-center mt-2';
        div.innerHTML = `
            <input type="text" name="images[]" class="block w-full border border-gray-300 rounded px-3 py-2" placeholder="https://example.com/image.jpg">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm font-medium whitespace-nowrap">Xóa</button>
        `;
        imageList.appendChild(div);
    });

    // Upload File -> Convert Base64 -> Preview & Input Hidden
    document.getElementById('fileUpload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const base64String = e.target.result;
            const div = document.createElement('div');
            div.className = 'flex gap-2 items-center border p-2 rounded bg-gray-50 mt-2';
            div.innerHTML = `
                <img src="${base64String}" class="w-10 h-10 object-cover rounded border">
                <input type="hidden" name="images[]" value="${base64String}">
                <span class="text-xs text-gray-500 truncate flex-1 ml-2">Ảnh mới: ${file.name}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm font-medium ml-auto">Xóa</button>
            `;
            imageList.appendChild(div);
        };
        reader.readAsDataURL(file);
        event.target.value = '';
    });
</script>
@endsection