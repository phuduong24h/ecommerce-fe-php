@extends('layouts.admin')

@section('title', 'Sửa khuyến mãi')

@section('content')
<div class="container mx-auto p-6 max-w-lg">

    <h1 class="text-2xl font-bold mb-6">Sửa khuyến mãi</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.promotions.update', $promotion['id']) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        <!-- Code -->
        <div class="mb-4">
            <label class="block font-medium">Mã khuyến mãi *</label>
            <input type="text" name="code" value="{{ old('code', $promotion['code']) }}" required
                   class="w-full border rounded p-2 @error('code') border-red-500 @enderror">
            @error('code') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label class="block font-medium">Mô tả</label>
            <textarea name="description" rows="3" class="w-full border rounded p-2 @error('description') border-red-500 @enderror">{{ old('description', $promotion['description']) }}</textarea>
            @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Discount -->
        <div class="mb-4">
            <label class="block font-medium">Giảm giá (%) *</label>
            <input type="number" name="discount" step="0.01" value="{{ old('discount', $promotion['discount']) }}" required
                   class="w-full border rounded p-2 @error('discount') border-red-500 @enderror">
            @error('discount') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Start Date -->
        <div class="mb-4">
            <label class="block font-medium">Ngày bắt đầu *</label>
            <input type="date" name="startDate" value="{{ old('startDate', substr($promotion['startDate'], 0, 10)) }}" required
                   class="w-full border rounded p-2 @error('startDate') border-red-500 @enderror">
            @error('startDate') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- End Date -->
        <div class="mb-4">
            <label class="block font-medium">Ngày kết thúc *</label>
            <input type="date" name="endDate" value="{{ old('endDate', substr($promotion['endDate'], 0, 10)) }}" required
                   class="w-full border rounded p-2 @error('endDate') border-red-500 @enderror">
            @error('endDate') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Active -->
        <div class="flex items-center gap-2 mb-4">
            <input type="checkbox" name="isActive" id="isActive" value="1" {{ old('isActive', $promotion['isActive']) ? 'checked' : '' }}>
            <label for="isActive" class="text-sm font-medium text-gray-700">Kích hoạt</label>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.settings.index', ['tab' => 'promotions']) }}" 
               class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Hủy</a>
            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700">Cập nhật</button>
        </div>

    </form>
</div>
@endsection
