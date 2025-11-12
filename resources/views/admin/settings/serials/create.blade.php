@extends('layouts.admin')

@section('title', 'Thêm Serial')

@section('content')
<div class="container mx-auto p-6 max-w-lg">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Thêm Serial mới</h1>
        <p class="text-gray-500 text-sm">Điền thông tin để tạo Serial mới</p>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.settings.serials.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf

        <!-- Serial -->
        <div>
            <label for="serial" class="block text-sm font-medium text-gray-700 mb-1">Serial Number <span class="text-red-500">*</span></label>
            <input type="text" name="serial" id="serial" value="{{ old('serial') }}" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            @error('serial')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Product ID -->
        <div>
            <label for="productId" class="block text-sm font-medium text-gray-700 mb-1">Product <span class="text-red-500">*</span></label>
            <select name="productId" id="productId" required
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                @foreach($products as $product)
                    <option value="{{ $product['id'] }}" {{ old('productId') == $product['id'] ? 'selected' : '' }}>
                        {{ $product['name'] }}
                    </option>
                @endforeach
            </select>
            @error('productId')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status -->
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="status" id="status"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                <option value="warranty" {{ old('status') == 'warranty' ? 'selected' : '' }}>Warranty Claimed</option>
            </select>
            @error('status')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Registered By -->
        <div>
            <label for="registeredBy" class="block text-sm font-medium text-gray-700 mb-1">Registered By</label>
            <input type="text" name="registeredBy" id="registeredBy" value="{{ old('registeredBy') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            @error('registeredBy')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.settings.index', ['tab' => 'serials']) }}" 
               class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Hủy</a>
            <button type="submit" 
                    class="px-4 py-2 rounded bg-cyan-500 text-white hover:bg-cyan-600">Tạo Serial</button>
        </div>
    </form>
</div>
@endsection
