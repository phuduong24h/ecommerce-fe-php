@extends('layouts.admin')
@section('title', 'Sửa sản phẩm')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Sửa sản phẩm</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product['id']) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf @method('PUT')

        <!-- Tên -->
        <div class="mb-4">
            <label class="block font-medium">Tên sản phẩm *</label>
            <input type="text" name="name" value="{{ old('name', $product['name']) }}"
                   class="w-full border rounded p-2 @error('name') border-red-500 @enderror" required>
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Giá -->
        <div class="mb-4">
            <label class="block font-medium">Giá *</label>
            <input type="number" name="price" value="{{ old('price', $product['price']) }}"
                   step="0.01" class="w-full border rounded p-2 @error('price') border-red-500 @enderror" required>
            @error('price') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Tồn kho -->
        <div class="mb-4">
            <label class="block font-medium">Tồn kho *</label>
            <input type="number" name="stock" value="{{ old('stock', $product['stock']) }}"
                   class="w-full border rounded p-2 @error('stock') border-red-500 @enderror" required>
            @error('stock') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Mô tả -->
        <div class="mb-4">
            <label class="block font-medium">Mô tả</label>
            <textarea name="description" rows="4"
                      class="w-full border rounded p-2 @error('description') border-red-500 @enderror">{{ old('description', $product['description']) }}</textarea>
        </div>

        <!-- Danh mục -->
        <div class="mb-4">
            <label class="block font-medium">Danh mục</label>
            <select name="categoryId" class="w-full border rounded p-2">
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $category)
                    <option value="{{ $category['id'] }}" {{ old('categoryId', $product['categoryId']) == $category['id'] ? 'selected' : '' }}>
                        {{ $category['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Hình ảnh -->
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
            Cập nhật
        </button>
        <a href="{{ route('admin.products.index') }}" class="ml-2 text-gray-600 hover:underline">Hủy</a>
    </form>
</div>

{{-- JS thêm input hình ảnh --}}
<script>
document.getElementById('addImageBtn').addEventListener('click', function() {
    const div = document.createElement('div');
    div.innerHTML = `<input type="text" name="images[]" class="mt-1 block w-full border rounded px-3 py-2 mb-2" placeholder="https://example.com/image.jpg">`;
    document.getElementById('image-list').appendChild(div);
});
</script>
@endsection
