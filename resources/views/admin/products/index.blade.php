@extends('layouts.admin')
@section('title', 'Danh sách sản phẩm')

@section('content')
<div class="container mx-auto p-6 space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Sản phẩm</h1>
        <!-- Nút dẫn tới trang create -->
        <a href="{{ route('admin.products.create') }}" 
           class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-4 py-2 rounded hover:from-cyan-600 hover:to-blue-600 flex items-center gap-2">
            <span>+</span> Thêm sản phẩm
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-emerald-100 text-emerald-700 p-3 rounded mb-4 animate-fade-in">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 animate-fade-in">{{ session('error') }}</div>
    @endif

    <!-- Search -->
    <div class="mb-4">
        <div class="relative w-1/2">
            <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm..." class="pl-10 w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"/>
            </svg>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full table-auto" id="productsTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $p)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm flex items-center gap-2">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-cyan-100 to-blue-100 flex items-center justify-center text-xs font-bold">Box</div>
                        <span>{{ $p['name'] }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $p['categoryName'] ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm font-medium">{{ number_format($p['price']) }}</td>
                    <td class="px-6 py-4 text-sm">{{ $p['stock'] }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($p['stock'] > 30)
                            <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-700 text-xs font-medium">In Stock</span>
                        @elseif($p['stock'] > 10)
                            <span class="px-2 py-1 rounded bg-amber-100 text-amber-700 text-xs font-medium">Low Stock</span>
                        @else
                            <span class="px-2 py-1 rounded bg-red-100 text-red-700 text-xs font-medium">Critical</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm flex gap-2">
                        <a href="{{ route('admin.products.edit', $p['id']) }}" class="text-blue-600 hover:underline font-medium">Edit</a>
                        <form action="{{ route('admin.products.destroy', $p['id']) }}" method="POST" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">Chưa có sản phẩm</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
    {{ $products->links() }}

</div>

    </div>
</div>

@push('scripts')
<script>
    // Search đa cột (Product, Category, Price, Stock, Status)
    document.getElementById('searchInput').addEventListener('input', () => {
        const term = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('#productsTable tbody tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            let match = false;

            cells.forEach((cell, index) => {
                if(index <= 4 && cell.innerText.toLowerCase().includes(term)){
                    match = true;
                }
            });

            row.style.display = match ? '' : 'none';
        });
    });

    // Xác nhận xóa
    function confirmDelete() {
        return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');
    }
</script>
@endpush
@endsection  