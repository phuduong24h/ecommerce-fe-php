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

        {{-- Tên sản phẩm --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
            <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" placeholder="Nhập tên sản phẩm" required>
        </div>

        {{-- Mô tả --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Mô tả</label>
            <textarea name="description" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" placeholder="Nhập mô tả sản phẩm">{{ old('description') }}</textarea>
        </div>

        {{-- Giá & Tồn kho --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Giá</label>
                <input type="number" name="price" value="{{ old('price') }}" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tồn kho</label>
                <input type="number" name="stock" value="{{ old('stock') }}" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
        </div>

        {{-- Danh mục --}}
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

        {{-- Hình ảnh --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Hình ảnh (URL)</label>
            <div id="image-list">
                <input type="text" name="images[]" value="{{ old('images.0') }}" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 mb-2" placeholder="https://example.com/image.jpg" required>
            </div>
            <button type="button" id="addImageBtn" class="text-blue-500 text-sm">+ Thêm hình ảnh</button>
        </div>

        {{-- Nút lưu --}}
        <button type="submit" class="bg-gradient-to-r from-purple-500 to-pink-500 text-white py-2 px-4 rounded hover:from-purple-600 hover:to-pink-600">
            Lưu sản phẩm
        </button>
    </form>
</div>

{{-- JS thêm input hình ảnh --}}
<script>
document.getElementById('addImageBtn').addEventListener('click', function() {
    const div = document.createElement('div');
    div.innerHTML = `<input type="text" name="images[]" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 mb-2" placeholder="https://example.com/image.jpg">`;
    document.getElementById('image-list').appendChild(div);
});
</script>
@endsection
