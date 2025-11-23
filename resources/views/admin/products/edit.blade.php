@extends('layouts.admin')
@section('title', 'Sửa sản phẩm')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Sửa sản phẩm</h1>

    {{-- Thông báo Success --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Thông báo Error --}}
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
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

    <form action="{{ route('admin.products.update', $product['id']) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf @method('PUT')

        <div class="mb-4">
            <label class="block font-medium">Tên sản phẩm *</label>
            <input type="text" name="name" value="{{ old('name', $product['name']) }}"
                   class="w-full border rounded p-2 @error('name') border-red-500 @enderror" required>
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block font-medium">Giá *</label>
            <input type="number" name="price" value="{{ old('price', $product['price']) }}"
                   step="0.01" class="w-full border rounded p-2 @error('price') border-red-500 @enderror" required>
            @error('price') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block font-medium">Tồn kho *</label>
            <input type="number" name="stock" value="{{ old('stock', $product['stock']) }}"
                   class="w-full border rounded p-2 @error('stock') border-red-500 @enderror" required>
            @error('stock') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block font-medium">Mô tả</label>
            <textarea name="description" rows="4"
                      class="w-full border rounded p-2 @error('description') border-red-500 @enderror">{{ old('description', $product['description']) }}</textarea>
        </div>

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

        <div class="mb-4">
            <label class="block font-medium">Hình ảnh</label>
            
            <div id="image-list" class="space-y-3">
                @php
                    $currentImages = old('images', $product['images'] ?? []);
                    if(is_string($currentImages)) $currentImages = [$currentImages];
                @endphp

                @foreach($currentImages as $img)
                    @if(!empty($img))
                    <div class="flex gap-2 items-center">
                        <div class="flex-1 flex items-center gap-2">
                             {{-- Preview ảnh --}}
                             <img src="{{ $img }}" class="w-10 h-10 object-cover rounded border bg-gray-100" onerror="this.style.display='none'">
                             
                             {{-- Logic kiểm tra: Nếu là Base64 dài dòng thì ẩn đi, nếu là URL ngắn thì hiện text --}}
                             @if(strlen($img) > 500)
                                <input type="hidden" name="images[]" value="{{ $img }}">
                                <span class="text-xs text-gray-500 truncate">Dữ liệu ảnh (Base64)</span>
                             @else
                                <input type="text" name="images[]" value="{{ $img }}" class="block w-full border rounded px-3 py-2">
                             @endif
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm ml-2">Xóa</button>
                    </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-2 flex gap-4">
                <button type="button" id="addUrlBtn" class="text-blue-500 text-sm hover:underline">+ Thêm URL ảnh</button>
                <label class="text-green-500 text-sm hover:underline cursor-pointer">
                    + Upload ảnh từ máy
                    <input type="file" id="fileUpload" class="hidden" accept="image/*">
                </label>
            </div>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Cập nhật
        </button>
        <a href="{{ route('admin.products.index') }}" class="ml-2 text-gray-600 hover:underline">Hủy</a>
    </form>
</div>

<script>
    const imageList = document.getElementById('image-list');

    // Thêm input URL
    document.getElementById('addUrlBtn').addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'flex gap-2 items-center mt-2';
        div.innerHTML = `
            <div class="flex-1">
                <input type="text" name="images[]" class="block w-full border rounded px-3 py-2" placeholder="https://example.com/image.jpg">
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm ml-2">Xóa</button>
        `;
        imageList.appendChild(div);
    });

    // Upload File -> Base64 -> Input Hidden
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
                <span class="text-xs text-gray-500 truncate flex-1 ml-2">Ảnh mới (Base64)</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm ml-auto">Xóa</button>
            `;
            imageList.appendChild(div);
        };
        reader.readAsDataURL(file);
        event.target.value = '';
    });
</script>
@endsection