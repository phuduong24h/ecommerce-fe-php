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

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
        @csrf @method('PUT')

        <!-- Tên -->
        <div class="mb-4">
            <label class="block font-medium">Tên sản phẩm *</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}"
                   class="w-full border rounded p-2 @error('name') border-red-500 @enderror" required>
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Giá -->
        <div class="mb-4">
            <label class="block font-medium">Giá *</label>
            <input type="number" name="price" value="{{ old('price', $product->price) }}"
                   step="0.01" class="w-full border rounded p-2 @error('price') border-red-500 @enderror" required>
            @error('price') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Tồn kho -->
        <div class="mb-4">
            <label class="block font-medium">Tồn kho *</label>
            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                   class="w-full border rounded p-2 @error('stock') border-red-500 @enderror" required>
            @error('stock') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Mô tả -->
        <div class="mb-4">
            <label class="block font-medium">Mô tả</label>
            <textarea name="description" rows="4"
                      class="w-full border rounded p-2 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
        </div>

        <!-- Ảnh -->
        <div class="mb-4">
            <label class="block font-medium">Ảnh hiện tại</label>
            @if($product->image)
                <img src="{{ asset('storage/products/' . $product->image) }}" class="w-32 h-32 object-cover mb-2">
            @endif
            <input type="file" name="image" accept="image/*" class="block">
            @error('image') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Cập nhật
        </button>
        <a href="{{ route('admin.products.index') }}" class="ml-2 text-gray-600 hover:underline">Hủy</a>
    </form>
</div>
@endsection