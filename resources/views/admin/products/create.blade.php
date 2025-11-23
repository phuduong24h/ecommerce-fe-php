@extends('layouts.admin')
@section('title', 'Thêm sản phẩm')

@section('content')
    <div class="container mx-auto p-6 space-y-6">
        <h1 class="text-2xl font-bold mb-4">Thêm sản phẩm</h1>

        {{-- Thông báo success --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Thông báo lỗi --}}
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Thông tin cơ bản --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nhập tên sản phẩm"
                        class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                    <textarea name="description" placeholder="Nhập mô tả sản phẩm"
                        class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Giá</label>
                        <input type="number" name="price" value="{{ old('price') }}"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tồn kho</label>
                        <input type="number" name="stock" value="{{ old('stock') }}"
                            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" required>
                    </div>
                </div>



                <div>
                    <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                    <select name="isActive" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                        <option value="1" {{ old('isActive', 1) == 1 ? 'selected' : '' }}>Đang bán</option>
                        <option value="0" {{ old('isActive') == 0 ? 'selected' : '' }}>Ngưng bán</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Danh mục</label>
                    <select name="categoryId" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['id'] }}" {{ old('categoryId') == $category['id'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Chính sách bảo hành</label>
                    <select name="warrantyPolicyId" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">-- Chọn chính sách --</option>
                        @foreach($policies as $policy)
                            <option value="{{ $policy['id'] }}" {{ old('warrantyPolicyId') == $policy['id'] ? 'selected' : '' }}>
                                {{ $policy['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- Biến thể sản phẩm --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Biến thể sản phẩm</label>
                <div id="variant-list" class="space-y-2">
                    {{-- Nếu có dữ liệu cũ (old) --}}
                    @if(old('variants'))
                        @foreach(old('variants', []) as $i => $variant)
                            <input type="text" name="variants[{{ $i }}][name]" value="{{ $variant['name'] ?? '' }}">
                            <input type="text" name="variants[{{ $i }}][value]" value="{{ $variant['value'] ?? '' }}">
                            <input type="number" name="variants[{{ $i }}][price]" value="{{ $variant['price'] ?? 0 }}">
                            <input type="number" name="variants[{{ $i }}][stock]" value="{{ $variant['stock'] ?? 0 }}">
                        @endforeach
                    @endif
                </div>
                <button type="button" id="addVariantBtn" class="text-green-500 text-sm mt-2">+ Thêm biến thể</button>
            </div>



            {{-- Hình ảnh --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Hình ảnh (URL)</label>
                <div id="image-list">
                    <input type="text" name="images[]" value="{{ old('images.0') }}"
                        class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 mb-2"
                        placeholder="https://example.com/image.jpg" required>
                </div>
                <button type="button" id="addImageBtn" class="text-blue-500 text-sm">+ Thêm hình ảnh</button>
            </div>

            {{-- Nút lưu --}}
            <button type="submit"
                class="bg-gradient-to-r from-purple-500 to-pink-500 text-white py-2 px-4 rounded hover:from-purple-600 hover:to-pink-600">
                Lưu sản phẩm
            </button>
        </form>
    </div>

    {{-- JS thêm input hình ảnh --}}
    <script>
        const variantList = document.getElementById('variant-list');
        const addVariantBtn = document.getElementById('addVariantBtn');

        let variantIndex = document.querySelectorAll('.variant-item').length;

        // Thêm biến thể mới
        addVariantBtn.addEventListener('click', function () {
            const div = document.createElement('div');
            div.classList.add('border', 'p-3', 'rounded', 'space-y-2', 'variant-item');
            div.innerHTML = `
            <input type="text" name="variants[${variantIndex}][name]" placeholder="Tên biến thể (VD: Màu đỏ)" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
            <input type="text" name="variants[${variantIndex}][value]" placeholder="Giá trị (VD: Đỏ)" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
            <input type="number" name="variants[${variantIndex}][price]" placeholder="Giá biến thể" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
            <input type="number" name="variants[${variantIndex}][stock]" placeholder="Tồn kho biến thể" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
            <button type="button" class="removeVariantBtn text-red-500 text-sm mt-1">Xóa</button>
        `;
            variantList.appendChild(div);
            variantIndex++;
        });


        // Xóa biến thể
        variantList.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('removeVariantBtn')) {
                e.target.closest('.variant-item').remove();
            }
        });

        // Lọc bỏ biến thể trống trước submit
        document.querySelector('form').addEventListener('submit', function (e) {
            const variantItems = document.querySelectorAll('.variant-item');
            variantItems.forEach(item => {
                const name = item.querySelector('input[name*="[name]"]').value.trim();
                const value = item.querySelector('input[name*="[value]"]').value.trim();
                const price = item.querySelector('input[name*="[price]"]').value.trim();
                const stock = item.querySelector('input[name*="[stock]"]').value.trim();

                // Nếu tất cả đều trống → xóa
                if (!name && !value && !price && !stock) {
                    item.remove();
                } else {
                    // Chuyển giá trị rỗng thành mặc định
                    item.querySelector('input[name*="[price]"]').value = price || 0;
                    item.querySelector('input[name*="[stock]"]').value = stock || 0;
                }
            });
        });

        document.getElementById('addImageBtn').addEventListener('click', function () {
            const div = document.createElement('div');
            div.innerHTML = `<input type="text" name="images[]" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 mb-2" placeholder="https://example.com/image.jpg">`;
            document.getElementById('image-list').appendChild(div);
        });
    </script>
@endsection