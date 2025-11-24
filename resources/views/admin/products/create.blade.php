
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

    <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- THÔNG TIN CƠ BẢN --}}
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nhập tên sản phẩm"
                    class="mt-1 w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Mô tả</label>
                <textarea name="description" placeholder="Nhập mô tả sản phẩm"
                    class="mt-1 w-full border border-gray-300 rounded px-3 py-2">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Giá</label>
                    <input type="number" name="price" value="{{ old('price') }}"
                        class="mt-1 w-full border border-gray-300 rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tồn kho</label>
                    <input type="number" name="stock" value="{{ old('stock') }}"
                        class="mt-1 w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select name="isActive" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                    <option value="1" {{ old('isActive', 1) == 1 ? 'selected' : '' }}>Đang bán</option>
                    <option value="0" {{ old('isActive') == 0 ? 'selected' : '' }}>Ngưng bán</option>
                </select>
            </div>

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

        {{-- BIẾN THỂ --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Biến thể sản phẩm</label>

            <div id="variant-list" class="space-y-3">
                @if(old('variants'))
                    @foreach(old('variants') as $i => $variant)
                        <div class="border p-3 rounded space-y-2 variant-item">
                            <input type="text" name="variants[{{ $i }}][name]" value="{{ $variant['name'] ?? '' }}"
                                placeholder="Tên biến thể" class="w-full border border-gray-300 rounded px-3 py-2">

                            <input type="text" name="variants[{{ $i }}][value]" value="{{ $variant['value'] ?? '' }}"
                                placeholder="Giá trị" class="w-full border border-gray-300 rounded px-3 py-2">

                            <input type="number" name="variants[{{ $i }}][price]" value="{{ $variant['price'] ?? 0 }}"
                                placeholder="Giá biến thể" class="w-full border border-gray-300 rounded px-3 py-2">

                            <input type="number" name="variants[{{ $i }}][stock]" value="{{ $variant['stock'] ?? 0 }}"
                                placeholder="Tồn kho" class="w-full border border-gray-300 rounded px-3 py-2">

                            <button type="button" class="removeVariantBtn text-red-500 text-sm">Xóa</button>
                        </div>
                    @endforeach
                @endif
            </div>

            <button type="button" id="addVariantBtn" class="text-green-600 text-sm">+ Thêm biến thể</button>
        </div>

        {{-- HÌNH ẢNH --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Hình ảnh (URL)</label>

            <div id="image-list" class="space-y-2">
                <input type="text" name="images[]" value="{{ old('images.0') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2"
                    placeholder="https://example.com/image.jpg">
            </div>

            <button type="button" id="addImageBtn" class="text-blue-600 text-sm mt-1">+ Thêm hình ảnh</button>
        </div>

        {{-- SUBMIT --}}
        <button type="submit"
            class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded shadow hover:from-purple-600 hover:to-pink-600">
            Lưu sản phẩm
        </button>
    </form>
</div>

{{-- SCRIPT --}}
<script>
    let variantIndex = document.querySelectorAll('.variant-item').length;

    const variantList = document.getElementById('variant-list');
    const addVariantBtn = document.getElementById('addVariantBtn');

    addVariantBtn.addEventListener('click', () => {
        const div = document.createElement('div');
        div.classList.add('border', 'p-3', 'rounded', 'space-y-2', 'variant-item');

        div.innerHTML = `
            <input type="text" name="variants[${variantIndex}][name]" placeholder="Tên biến thể" class="w-full border border-gray-300 rounded px-3 py-2">
            <input type="text" name="variants[${variantIndex}][value]" placeholder="Giá trị" class="w-full border border-gray-300 rounded px-3 py-2">
            <input type="number" name="variants[${variantIndex}][price]" placeholder="Giá biến thể" class="w-full border border-gray-300 rounded px-3 py-2">
            <input type="number" name="variants[${variantIndex}][stock]" placeholder="Tồn kho" class="w-full border border-gray-300 rounded px-3 py-2">
            <button type="button" class="removeVariantBtn text-red-500 text-sm">Xóa</button>
        `;
        variantList.appendChild(div);

        variantIndex++;
    });

    variantList.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeVariantBtn')) {
            e.target.closest('.variant-item').remove();
        }
    });

    document.getElementById('addImageBtn').addEventListener('click', () => {
        const div = document.createElement('div');
        div.innerHTML = `
            <input type="text" name="images[]" class="w-full border border-gray-300 rounded px-3 py-2"
            placeholder="https://example.com/image.jpg">
        `;
        document.getElementById('image-list').appendChild(div);
    });
</script>
@endsection
