@extends('layouts.admin')
@section('title', 'Sửa chính sách bảo hành')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Sửa chính sách bảo hành</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.warranty_policies.update', $policy['id']) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf @method('PUT')

        <!-- Tên -->
        <div class="mb-4">
            <label class="block font-medium">Tên chính sách *</label>
            <input type="text" name="name" value="{{ old('name', $policy['name']) }}"
                   class="w-full border rounded p-2 @error('name') border-red-500 @enderror" required>
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Thời hạn -->
        <div class="mb-4">
            <label class="block font-medium">Thời hạn (ngày) *</label>
            <input type="number" name="durationDays" value="{{ old('durationDays', $policy['durationDays']) }}"
                   class="w-full border rounded p-2 @error('durationDays') border-red-500 @enderror" required>
            @error('durationDays') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Phạm vi bảo hành -->
        <div class="mb-4">
            <label class="block font-medium">Phạm vi bảo hành *</label>
            <textarea name="coverage" rows="4"
                      class="w-full border rounded p-2 @error('coverage') border-red-500 @enderror" required>{{ old('coverage', $policy['coverage']) }}</textarea>
            @error('coverage') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Yêu cầu số serial -->
        <div class="mb-4 flex items-center gap-2">
            <input type="checkbox" name="requiresSerial" id="requiresSerial" value="1" {{ old('requiresSerial', $policy['requiresSerial']) ? 'checked' : '' }}>
            <label for="requiresSerial" class="text-sm text-gray-700">Yêu cầu số serial</label>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Cập nhật
        </button>
        <a href="{{ route('admin.warranty_policies.index') }}" class="ml-2 text-gray-600 hover:underline">Hủy</a>
    </form>
</div>
@endsection
