@extends('layouts.admin')

@section('title', 'Thêm sản phẩm')

@section('content')
<div class="container mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold mb-4">Thêm sản phẩm</h1>

    {{-- Thông báo Success --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Thông báo Error (Tổng hợp từ cả 2 file) --}}
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Thông báo lỗi Validate --}}
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- === PHẦN 1: THÔNG TIN CƠ BẢN (Lấy từ File 2 vì đầy đủ hơn) === --}}
        <div class="space-y-4 border-b pb-6">
            <h2 class="text-lg font-semibold text-gray-800">Thông tin chung</h2>
            
            {{-- Tên sản phẩm --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nhập tên sản phẩm"
                    class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring-purple-500 focus:border-purple-500" required>
            </div>

            {{-- Mô tả --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                <textarea name="description" placeholder="Nhập mô tả sản phẩm" rows="3"
                    class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring-purple-500 focus:border-purple-500">{{ old('description') }}</textarea>
            </div>

            {{-- Giá & Tồn kho --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Giá bán</label>
                    <input type="number" name="price" value="{{ old('price') }}"
                        class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring-purple-500 focus:border-purple-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tồn kho tổng</label>
                    <input type="number" name="stock" value="{{ old('stock') }}"
                        class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:ring-purple-500 focus:border-purple-500" required>
                </div>
            </div>

            {{-- Trạng thái & Danh mục & Bảo hành --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Trạng thái (File 2) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                    <select name="isActive" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                        <option value="1" {{ old('isActive', 1) == 1 ? 'selected' : '' }}>Đang bán</option>
                        <option value="0" {{ old('isActive') == 0 ? 'selected' : '' }}>Ngưng bán</option>
                    </select>
                </div>

                {{-- Danh mục --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Danh mục</label>
                    <select name="categoryId" class="mt-1 w-full border border-gray-300 rounded px-3 py-2" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['id'] }}" {{ old('categoryId') == $category['id'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Chính sách bảo hành (File 2) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Chính sách bảo hành</label>
                    <select name="warrantyPolicyId" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">-- Chọn chính sách --</option>
                        @foreach($policies as $policy)
                            <option value="{{ $policy['id'] }}" {{ old('warrantyPolicyId') == $policy['id'] ? 'selected' : '' }}>
                                {{ $policy['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- === PHẦN 2: BIẾN THỂ SẢN PHẨM (Lấy từ File 2) === --}}
        <div class="space-y-4 border-b pb-6">
            <h2 class="text-lg font-semibold text-gray-800">Biến thể sản phẩm (Option)</h2>
            
            <div id="variant-list" class="space-y-3">
                @if(old('variants'))
                    @foreach(old('variants') as $i => $variant)
                        <div class="border p-3 rounded space-y-2 variant-item bg-gray-50">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                <input type="text" name="variants[{{ $i }}][name]" value="{{ $variant['name'] ?? '' }}" placeholder="Tên (VD: Màu)" class="w-full border border-gray-300 rounded px-2 py-1">
                                <input type="text" name="variants[{{ $i }}][value]" value="{{ $variant['value'] ?? '' }}" placeholder="Giá trị (VD: Đỏ)" class="w-full border border-gray-300 rounded px-2 py-1">
                                <input type="number" name="variants[{{ $i }}][price]" value="{{ $variant['price'] ?? 0 }}" placeholder="Giá thêm" class="w-full border border-gray-300 rounded px-2 py-1">
                                <input type="number" name="variants[{{ $i }}][stock]" value="{{ $variant['stock'] ?? 0 }}" placeholder="Kho riêng" class="w-full border border-gray-300 rounded px-2 py-1">
                            </div>
                            <button type="button" class="removeVariantBtn text-red-500 text-sm hover:underline">Xóa biến thể này</button>
                        </div>
                    @endforeach
                @endif
            </div>

            <button type="button" id="addVariantBtn" class="text-purple-600 text-sm font-medium hover:underline">+ Thêm biến thể mới</button>
        </div>

        {{-- === PHẦN 3: HÌNH ẢNH (Lấy từ File 1 - Hỗ trợ Base64 & URL) === --}}
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-800">Hình ảnh sản phẩm</h2>
            
            <div id="image-list" class="space-y-2">
                {{-- Input mặc định cho URL (giữ lại 1 cái đầu tiên) --}}
                <div class="flex gap-2 items-center">
                    <input type="text" name="images[]" value="{{ old('images.0') }}" 
                           class="block w-full border border-gray-300 rounded px-3 py-2 focus:ring-purple-500 focus:border-purple-500" 
                           placeholder="https://example.com/image.jpg">
                </div>
            </div>

            <div class="mt-2 flex gap-4 items-center">
                {{-- Nút thêm URL --}}
                <button type="button" id="addUrlBtn" class="text-blue-600 text-sm hover:underline font-medium">
                    + Thêm URL ảnh
                </button>
                
                <span class="text-gray-300">|</span>

                {{-- Nút Upload file (Tính năng xịn của File 1) --}}
                <label class="text-green-600 text-sm hover:underline cursor-pointer font-medium flex items-center gap-1">
                    <span>+ Upload ảnh từ máy</span>
                    <input type="file" id="fileUpload" class="hidden" accept="image/*">
                </label>
            </div>
            @error('images') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- SUBMIT --}}
        <div class="pt-4">
            <button type="submit"
                class="w-full md:w-auto bg-gradient-to-r from-purple-500 to-pink-500 text-white px-6 py-2 rounded shadow hover:from-purple-600 hover:to-pink-600 font-medium transition duration-150">
                Lưu sản phẩm
            </button>
        </div>
    </form>
</div>

{{-- === SCRIPTS (Gộp Logic của cả 2 file) === --}}
<script>
    // --- PHẦN 1: LOGIC BIẾN THỂ (Từ File 2) ---
    const variantList = document.getElementById('variant-list');
    const addVariantBtn = document.getElementById('addVariantBtn');
    
    // Đếm số lượng item hiện tại để tạo index mảng
    let variantIndex = document.querySelectorAll('.variant-item').length;

    addVariantBtn.addEventListener('click', () => {
        const div = document.createElement('div');
        div.classList.add('border', 'p-3', 'rounded', 'space-y-2', 'variant-item', 'bg-gray-50', 'mt-2');

        div.innerHTML = `
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                <input type="text" name="variants[${variantIndex}][name]" placeholder="Tên (VD: Size)" class="w-full border border-gray-300 rounded px-2 py-1">
                <input type="text" name="variants[${variantIndex}][value]" placeholder="Giá trị (VD: XL)" class="w-full border border-gray-300 rounded px-2 py-1">
                <input type="number" name="variants[${variantIndex}][price]" placeholder="Giá cộng thêm" class="w-full border border-gray-300 rounded px-2 py-1">
                <input type="number" name="variants[${variantIndex}][stock]" placeholder="Kho riêng" class="w-full border border-gray-300 rounded px-2 py-1">
            </div>
            <button type="button" class="removeVariantBtn text-red-500 text-sm hover:underline">Xóa biến thể này</button>
        `;
        variantList.appendChild(div);
        variantIndex++;
    });

    // Ủy quyền sự kiện xóa (Event Delegation) cho biến thể
    variantList.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeVariantBtn')) {
            e.target.closest('.variant-item').remove();
        }
    });


    // --- PHẦN 2: LOGIC HÌNH ẢNH (Từ File 1 - Xịn hơn) ---
    const imageList = document.getElementById('image-list');

    // Logic: Thêm ô nhập URL
    document.getElementById('addUrlBtn').addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'flex gap-2 items-center mt-2';
        div.innerHTML = `
            <input type="text" name="images[]" class="block w-full border border-gray-300 rounded px-3 py-2" placeholder="https://example.com/image.jpg">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm font-medium whitespace-nowrap">Xóa</button>
        `;
        imageList.appendChild(div);
    });

    // Logic: Upload file -> Convert Base64 -> Input Hidden
    document.getElementById('fileUpload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const base64String = e.target.result;
            const div = document.createElement('div');
            div.className = 'flex gap-2 items-center border p-2 rounded bg-gray-50 mt-2';
            
            // Input hidden: chứa dữ liệu thật gửi đi lên server
            // Img: hiển thị preview cho đẹp
            div.innerHTML = `
                <img src="${base64String}" class="w-10 h-10 object-cover rounded border">
                <input type="hidden" name="images[]" value="${base64String}"> 
                <span class="text-sm text-gray-600 truncate flex-1 ml-2">Ảnh tải lên: ${file.name}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm font-medium ml-auto">Xóa</button>
            `;
            imageList.appendChild(div);
        };
        reader.readAsDataURL(file);
        
        // Reset input để chọn lại file trùng tên vẫn kích hoạt sự kiện change
        event.target.value = ''; 
    });
</script>
@endsection