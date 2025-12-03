@extends('layouts.admin')

@section('title', 'Sửa Service Center')

@section('content')
<div class="container mx-auto p-6 max-w-lg">

    <h1 class="text-2xl font-bold mb-6">Sửa Service Center</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.centers.update', $center['id']) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div class="mb-4">
            <label class="block font-medium">Tên Service Center *</label>
            <input type="text" name="name" value="{{ old('name', $center['name']) }}" required
                   class="w-full border rounded p-2 @error('name') border-red-500 @enderror">
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Address -->
        <div class="mb-4">
            <label class="block font-medium">Địa chỉ *</label>
            <input type="text" name="address" value="{{ old('address', $center['address']) }}" required
                   class="w-full border rounded p-2 @error('address') border-red-500 @enderror">
            @error('address') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Phone -->
        <div class="mb-4">
            <label class="block font-medium">Số điện thoại</label>
            <input type="text" name="phone" value="{{ old('phone', $center['phone']) }}"
                   class="w-full border rounded p-2 @error('phone') border-red-500 @enderror">
            @error('phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label class="block font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $center['email']) }}"
                   class="w-full border rounded p-2 @error('email') border-red-500 @enderror">
            @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>



        <!-- Buttons -->
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.settings.centers.index') }}" 
               class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Hủy</a>
            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700">Cập nhật</button>
        </div>

    </form>
</div>
@endsection
