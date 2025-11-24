@extends('layouts.admin')

@section('title', 'Sửa sản phẩm')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Sửa sản phẩm</h1>

    {{-- Thông báo lỗi --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product['id']) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        {{-- Tên --}}
        <div class="mb-4">
            <label class="block font-medium">Tên sản phẩm *</label>
            <input type="text" name="name" value="{{ old('name', $product['name']) }}"
                   class="w-full border rounded p-2 @error('name') border-red-500 @enderror" required>
        </div>

        {{-- Giá --}}
        <div class="mb-4">
            <label class="block font-medium">Giá *</label>
            <input type="number" name="price" value="{{ old('price', $product['price']) }}" step="0.01"
                   class="w-full border rounded p-2 @error('price') border-red-500 @enderror" required>
        </div>

        {{-- Tồn kho --}}
        <div class="mb-4">
            <label class="block font-medium">Tồn kho *</label>
            <input type="number" name="stock" value="{{ old('stock', $product['stock']) }}"
                   class="w-full border rounded p-2 @error('stock') border-red-500 @enderror" required>
        </div>

        {{-- Mô tả --}}
        <div class="mb-4">
            <label class="block font-medium">Mô tả</label>
            <textarea name="description" rows="4"
                      class="w-full border rounded p-2 @error('description') border-red-500 @enderror">{{ old('description', $product['description']) }}</textarea>
        </div>

        {{-- Trạng thái --}}
        <div class="mb-4">
            <label class="block font-medium">Trạng thái</label>
            <select name="isActive" class="w-full border rounded p-2">
                <option value="1" {{ old('isActive', $product['isActive']) ? 'selected' : '' }}>Đang bán</option>
                <option value="0" {{ !old('isActive', $product['isActive']) ? 'selected' : '' }}>Ngưng bán</option>
            </select>
        </div>

        {{-- Danh mục --}}
        <div class="mb-4">
            <label class="block font-medium">Danh mục</label>
            <select name="categoryId" class="w-full border rounded p-2" required>
                @foreach($categories as $category)
                    <option value="{{ $category['id'] }}" {{ old('categoryId', $product['categoryId']) == $category['id'] ? 'selected' : '' }}>
                        {{ $category['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Chính sách bảo hành --}}
        <div class="mb-4">
            <label class="block font-medium">Chính sách bảo hành</label>
            <select name="warrantyPolicyId" class="w-full border rounded p-2">
                <option value="">-- Chọn chính sách --</option>
                @foreach($policies as $policy)
                    <option value="{{ $policy['id'] }}" {{ old('warrantyPolicyId', $product['warrantyPolicyId']) == $policy['id'] ? 'selected' : '' }}>
                        {{ $policy['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Biến thể sản phẩm --}}
        <div class="mb-4">
            <label class="block font-medium">Biến thể sản phẩm</label>
            <div id="variant-list" class="space-y-2">
                @foreach(old('variants', $product['variants'] ?? []) as $i => $variant)
                    <div class="border p-3 rounded space-y-2 variant-item">
                        <input type="text" name="variants[{{ $i }}][name]" value="{{ $variant['name'] ?? '' }}"
                               placeholder="Tên biến thể" class="mt-1 block w-full border rounded px-3 py-2">
                        <input type="text" name="variants[{{ $i }}][value]" value="{{ $variant['value'] ?? '' }}"
                               placeholder="Giá trị" class="mt-1 block w-full border rounded px-3 py-2">
                        <input type="number" name="variants[{{ $i }}][price]" value="{{ $variant['price'] ?? 0 }}"
                               placeholder="Giá biến thể" class="mt-1 block w-full border rounded px-3 py-2">
                        <input type="number" name="variants[{{ $i }}][stock]" value="{{ $variant['stock'] ?? 0 }}"
                               placeholder="Tồn kho biến thể" class="mt-1 block w-full border rounded px-3 py-2">
                        <button type="button" class="removeVariantBtn text-red-500 text-sm mt-1">Xóa</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="addVariantBtn" class="text-green-500 text-sm mt-2">+ Thêm biến thể</button>
        </div>

        {{-- Hình ảnh --}}
        <div class="mb-4">
            <label class="block font-medium">Hình ảnh (URL)</label>
            <div id="image-list">
                @foreach(old('images', $product['images'] ?? []) as $img)
                    <input type="text" name="images[]" value="{{ $img }}" class="mt-1 block w-full border rounded px-3 py-2 mb-2">
                @endforeach
            </div>
            <button type="button" id="addImageBtn" class="text-blue-500 text-sm">+ Thêm hình ảnh</button>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Cập nhật sản phẩm
        </button>
    </form>
</div>

{{-- JS thêm input hình ảnh & biến thể --}}
<script>
const variantList = document.getElementById('variant-list');
let variantIndex = {{ count(old('variants', $product['variants'] ?? [])) }};

// Thêm biến thể mới
document.getElementById('addVariantBtn').addEventListener('click', function() {
    const div = document.createElement('div');
    div.classList.add('border', 'p-3', 'rounded', 'space-y-2', 'variant-item');
    div.innerHTML = `
        <input type="text" name="variants[${variantIndex}][name]" placeholder="Tên biến thể" class="mt-1 block w-full border rounded px-3 py-2">
        <input type="text" name="variants[${variantIndex}][value]" placeholder="Giá trị" class="mt-1 block w-full border rounded px-3 py-2">
        <input type="number" name="variants[${variantIndex}][price]" placeholder="Giá biến thể" class="mt-1 block w-full border rounded px-3 py-2">
        <input type="number" name="variants[${variantIndex}][stock]" placeholder="Tồn kho biến thể" class="mt-1 block w-full border rounded px-3 py-2">
        <button type="button" class="removeVariantBtn text-red-500 text-sm mt-1">Xóa</button>
    `;
    variantList.appendChild(div);
    variantIndex++;
});

// Xóa biến thể
variantList.addEventListener('click', function(e){
    if(e.target && e.target.classList.contains('removeVariantBtn')){
        e.target.closest('.variant-item').remove();
    }
});

// Lọc bỏ biến thể trống trước submit
document.querySelector('form').addEventListener('submit', function() {
    const variantItems = document.querySelectorAll('.variant-item');
    variantItems.forEach(item => {
        const name = item.querySelector('input[name*="[name]"]').value.trim();
        const value = item.querySelector('input[name*="[value]"]').value.trim();
        const price = item.querySelector('input[name*="[price]"]').value.trim();
        const stock = item.querySelector('input[name*="[stock]"]').value.trim();

        if(!name && !value && !price && !stock){
            item.remove();
        } else {
            item.querySelector('input[name*="[price]"]').value = price || 0;
            item.querySelector('input[name*="[stock]"]').value = stock || 0;
        }
    });
});

// Thêm hình ảnh
document.getElementById('addImageBtn').addEventListener('click', function() {
    const div = document.createElement('div');
    div.innerHTML = `<input type="text" name="images[]" class="mt-1 block w-full border rounded px-3 py-2 mb-2" placeholder="https://example.com/image.jpg">`;
    document.getElementById('image-list').appendChild(div);
});
</script>
@endsection
