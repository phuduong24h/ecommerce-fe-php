@extends('layouts.admin')

@section('title', 'Thêm khuyến mãi')

@section('content')
<div class="container mx-auto p-6 max-w-lg">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Thêm khuyến mãi mới</h1>
        <p class="text-gray-500 text-sm">Điền thông tin để tạo khuyến mãi mới</p>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.settings.promotions.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf

        <!-- Code -->
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Mã khuyến mãi <span class="text-red-500">*</span></label>
            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            @error('code')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
            <textarea name="description" id="description" rows="3"
                      class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Discount -->
        <div>
            <label for="discount" class="block text-sm font-medium text-gray-700 mb-1">Giảm giá (%) <span class="text-red-500">*</span></label>
            <input type="number" name="discount" id="discount" value="{{ old('discount') }}" step="0.01" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            @error('discount')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Start Date -->
        <div>
            <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu <span class="text-red-500">*</span></label>
            <input type="date" name="startDate" id="startDate" value="{{ old('startDate') }}" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            @error('startDate')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- End Date -->
        <div>
            <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc <span class="text-red-500">*</span></label>
            <input type="date" name="endDate" id="endDate" value="{{ old('endDate') }}" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            @error('endDate')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Active -->
        <div class="flex items-center gap-2">
            <input type="checkbox" name="isActive" id="isActive" value="1" {{ old('isActive', true) ? 'checked' : '' }}>
            <label for="isActive" class="text-sm font-medium text-gray-700">Kích hoạt</label>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.settings.index', ['tab' => 'promotions']) }}" 
               class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Hủy</a>
            <button type="submit" 
                    class="px-4 py-2 rounded bg-purple-500 text-white hover:bg-purple-600">Tạo khuyến mãi</button>
        </div>
    </form>
</div>
@endsection
