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



        <!-- Header: Add Serial button -->
        <div class="flex justify-end">
            <a href="{{ route('admin.settings.serials.create') }}"
               class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Serial
            </a>
        </div>
