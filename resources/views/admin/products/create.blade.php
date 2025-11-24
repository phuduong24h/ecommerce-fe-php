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

    {{-- Thông báo lỗi từ Controller trả về (Quan trọng) --}}
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Thông báo lỗi Validate --}}
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
            <label class="block text-sm font-medium text-gray-700">Hình ảnh</label>
            
            <div id="image-list" class="space-y-2">
                {{-- Input mặc định cho URL --}}
                <div class="flex gap-2 items-center">
                    <input type="text" name="images[]" value="{{ old('images.0') }}" 
                           class="block w-full border border-gray-300 rounded px-3 py-2" 
                           placeholder="https://example.com/image.jpg">
                </div>
            </div>

            <div class="mt-2 flex gap-4">
                <button type="button" id="addUrlBtn" class="text-blue-500 text-sm hover:underline">+ Thêm URL ảnh</button>
                
                {{-- Nút Upload file --}}
                <label class="text-green-500 text-sm hover:underline cursor-pointer">
                    + Upload ảnh từ máy
                    <input type="file" id="fileUpload" class="hidden" accept="image/*">
                </label>
            </div>
            @error('images') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="bg-gradient-to-r from-purple-500 to-pink-500 text-white py-2 px-4 rounded hover:from-purple-600 hover:to-pink-600">
            Lưu sản phẩm
        </button>
    </form>
</div>

<script>
    const imageList = document.getElementById('image-list');

    // Thêm ô nhập URL
    document.getElementById('addUrlBtn').addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'flex gap-2 items-center';
        div.innerHTML = `
            <input type="text" name="images[]" class="block w-full border border-gray-300 rounded px-3 py-2" placeholder="https://example.com/image.jpg">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm">Xóa</button>
        `;
        imageList.appendChild(div);
    });

    // Xử lý upload file -> Base64 -> Input Hidden
    document.getElementById('fileUpload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const base64String = e.target.result;
            const div = document.createElement('div');
            div.className = 'flex gap-2 items-center border p-2 rounded bg-gray-50';
            
            // Input hidden: chứa dữ liệu thật gửi đi
            // Img: hiển thị preview
            div.innerHTML = `
                <img src="${base64String}" class="w-10 h-10 object-cover rounded border">
                <input type="hidden" name="images[]" value="${base64String}"> 
                <span class="text-sm text-gray-600 truncate flex-1 ml-2">Ảnh tải lên (Base64)</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 text-sm ml-auto">Xóa</button>
            `;
            imageList.appendChild(div);
        };
        reader.readAsDataURL(file);
        event.target.value = ''; 
    });
</script>
@endsection