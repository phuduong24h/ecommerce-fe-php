@extends('layouts.admin')
@section('title', 'Thêm chính sách bảo hành')

@section('content')
<div class="container mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold mb-4">Thêm chính sách bảo hành</h1>

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

    <form action="{{ route('admin.warranty_policies.store') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Tên chính sách --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Tên chính sách</label>
            <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" placeholder="VD: Standard Warranty" required>
        </div>

        {{-- Thời hạn (ngày) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Thời hạn (ngày)</label>
            <input type="number" name="durationDays" value="{{ old('durationDays') }}" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" placeholder="365" required>
        </div>

        {{-- Phạm vi bảo hành --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Phạm vi bảo hành</label>
            <textarea name="coverage" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" placeholder="Mô tả phạm vi bảo hành..." required>{{ old('coverage') }}</textarea>
        </div>

        {{-- Yêu cầu số serial --}}
        <div class="flex items-center gap-2">
            <input type="checkbox" name="requiresSerial" id="requiresSerial" value="1" {{ old('requiresSerial') ? 'checked' : '' }}>
            <label for="requiresSerial" class="text-sm text-gray-700">Yêu cầu số serial</label>
        </div>

        {{-- Nút lưu --}}
        <button type="submit" class="bg-gradient-to-r from-purple-500 to-pink-500 text-white py-2 px-4 rounded hover:from-purple-600 hover:to-pink-600">
            Lưu chính sách
        </button>
    </form>
</div>
@endsection
