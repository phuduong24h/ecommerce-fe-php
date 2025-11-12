@extends('layouts.admin')
@section('title', 'Sửa danh mục')

@section('content')
<div class="container mx-auto p-6 max-w-lg">
    <h1 class="text-2xl font-bold mb-6">Sửa danh mục</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.categories.update', $category['id']) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf @method('PUT')

        <!-- Tên danh mục -->
        <div class="mb-4">
            <label class="block font-medium">Tên danh mục *</label>
            <input type="text" name="name" value="{{ old('name', $category['name']) }}"
                   class="w-full border rounded p-2 @error('name') border-red-500 @enderror" required>
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Danh mục cha -->
        <div class="mb-4">
            <label class="block font-medium">Danh mục cha</label>
            <select name="parentId" class="w-full border rounded p-2">
                <option value="">-- Không có danh mục cha --</option>
                @if(!empty($categories))
                    @foreach($categories as $cat)
                        @if($cat['id'] != $category['id']) {{-- tránh chọn chính nó --}}
                            <option value="{{ $cat['id'] }}" {{ old('parentId', $category['parentId']) == $cat['id'] ? 'selected' : '' }}>
                                {{ $cat['name'] }}
                            </option>
                        @endif
                    @endforeach
                @endif
            </select>
            @error('parentId') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Cập nhật
            </button>
            <a href="{{ route('admin.settings.index') }}" class="ml-2 text-gray-600 hover:underline">Hủy</a>
        </div>
    </form>
</div>
@endsection
