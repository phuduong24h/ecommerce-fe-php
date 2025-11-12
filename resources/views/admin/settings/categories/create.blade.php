@extends('layouts.admin')

@section('title', 'Thêm danh mục')

@section('content')
<div class="container mx-auto p-6 max-w-lg">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Thêm danh mục mới</h1>
        <p class="text-gray-500 text-sm">Điền thông tin để tạo danh mục sản phẩm mới</p>
    </div>

    <!-- Flash Messages -->

    <!-- Form -->
    <form action="{{ route('admin.settings.categories.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Tên danh mục <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Parent Category -->
        <div>
            <label for="parentId" class="block text-sm font-medium text-gray-700 mb-1">
                Danh mục cha
            </label>
            <select name="parentId" id="parentId" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <option value="">-- Không có danh mục cha --</option>
                @if(!empty($categories))
                    @foreach($categories as $cat)
                        <option value="{{ $cat['id'] }}" {{ old('parentId') == $cat['id'] ? 'selected' : '' }}>
                            {{ $cat['name'] }}
                        </option>
                    @endforeach
                @endif
            </select>
            @error('parentId')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.settings.categories.index') }}" 
               class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Hủy</a>
            <button type="submit" 
                    class="px-4 py-2 rounded bg-cyan-500 text-white hover:bg-cyan-600">Tạo danh mục</button>
        </div>
    </form>
</div>
@endsection
