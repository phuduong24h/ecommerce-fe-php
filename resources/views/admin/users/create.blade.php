@extends('layouts.admin')
@section('title', 'Thêm người dùng')

@section('content')
    <div class="container mx-auto p-6 space-y-6">
        <h1 class="text-2xl font-bold mb-4">Thêm người dùng</h1>

        {{-- Thông báo success --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Thông báo lỗi --}}
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Tên --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Tên</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" placeholder="Nhập tên người dùng"
                    required>
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" placeholder="Nhập email" required>
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                <input type="password" name="password" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2"
                    placeholder="Nhập mật khẩu" required>
            </div>
            <!-- Phone -->
            <div class="mb-4">
                <label class="block font-medium">Số điện thoại</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="w-full border rounded p-2 @error('phone') border-red-500 @enderror"
                    placeholder="Nhập số điện thoại">
                @error('phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Vai trò</label>
                <select name="role" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">-- Chọn vai trò --</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="support" {{ old('role') == 'support' ? 'selected' : '' }}>Support</option>
                </select>
            </div>

            {{-- Nút lưu --}}
            <button type="submit"
                class="bg-gradient-to-r from-purple-500 to-pink-500 text-white py-2 px-4 rounded hover:from-purple-600 hover:to-pink-600">
                Lưu người dùng
            </button>
        </form>
    </div>
@endsection