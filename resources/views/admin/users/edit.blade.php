@extends('layouts.admin')
@section('title', 'Sửa người dùng')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Sửa người dùng</h1>

    {{-- Hiển thị lỗi validate --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Hiển thị flash message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form update --}}
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        {{-- Tên --}}
        <div class="mb-4">
            <label class="block font-medium">Tên *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                class="w-full border rounded p-2 @error('name') border-red-500 @enderror" required>
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label class="block font-medium">Email *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                class="w-full border rounded p-2 @error('email') border-red-500 @enderror" required>
        </div>

        {{-- Mật khẩu --}}
        <div class="mb-4">
            <label class="block font-medium">Mật khẩu <span class="text-gray-400 text-sm">(Để trống nếu không đổi)</span></label>
            <input type="password" name="password" class="w-full border rounded p-2">
        </div>

        {{-- Vai trò --}}
        <div class="mb-4">
            <label class="block font-medium">Vai trò *</label>
            <select name="role" class="w-full border rounded p-2" required>
                <option value="admin" {{ old('role', strtolower($user->role)) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="customer" {{ old('role', strtolower($user->role)) == 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="support" {{ old('role', strtolower($user->role)) == 'support' ? 'selected' : '' }}>Support</option>
            </select>
        </div>

        {{-- Số điện thoại --}}
        <div class="mb-4">
            <label class="block font-medium">Số điện thoại</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                class="w-full border rounded p-2" placeholder="Nhập số điện thoại">
        </div>

        {{-- Địa chỉ --}}
        <div class="mb-4">
            <label class="block font-medium">Địa chỉ</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}" 
                class="w-full border rounded p-2" placeholder="Nhập địa chỉ">
        </div>

        {{-- Buttons --}}
        <div class="flex items-center gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Cập nhật
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Hủy</a>
        </div>
    </form>
</div>
@endsection
