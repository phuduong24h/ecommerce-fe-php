@extends('layouts.admin')
@section('title', 'Danh sách sản phẩm')

@section('content')
<div class="container mx-auto p-6 space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Sản phẩm</h1>

        <!-- Button mở Modal -->
        <button id="openModalBtn" class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-4 py-2 rounded hover:from-cyan-600 hover:to-blue-600 flex items-center gap-2">
            <span>➕</span> Thêm sản phẩm
        </button>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <div class="relative w-1/2">
            <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm..." class="pl-10 w-full border border-gray-300 rounded px-3 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"/>
            </svg>
        </div>
    </div>

    <!-- Table sản phẩm -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full table-auto">
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
            <tbody id="productsTable" class="bg-white divide-y divide-gray-200">
                @php
                    $products = [
                        ['id'=>1,'name'=>'Wireless Mouse','price'=>29.99,'stock'=>150,'category'=>'Electronics'],
                        ['id'=>2,'name'=>'Mechanical Keyboard','price'=>89.99,'stock'=>75,'category'=>'Electronics'],
                        ['id'=>3,'name'=>'USB-C Cable','price'=>12.99,'stock'=>300,'category'=>'Accessories'],
                        ['id'=>4,'name'=>'Laptop Stand','price'=>45.99,'stock'=>25,'category'=>'Accessories'],
                        ['id'=>5,'name'=>'Monitor 27"','price'=>299.99,'stock'=>8,'category'=>'Electronics'],
                    ];
                @endphp
                @foreach($products as $p)
                <tr>
                    <td class="px-6 py-4 text-sm flex items-center gap-2">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-cyan-100 to-blue-100 flex items-center justify-center">
                            📦
                        </div>
                        <span>{{ $p['name'] }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $p['category'] }}</td>
                    <td class="px-6 py-4 text-sm">${{ number_format($p['price'], 2) }}</td>
                    <td class="px-6 py-4 text-sm">{{ $p['stock'] }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($p['stock'] > 50)
                            <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-700">In Stock</span>
                        @elseif($p['stock'] > 10)
                            <span class="px-2 py-1 rounded bg-amber-100 text-amber-700">Low Stock</span>
                        @else
                            <span class="px-2 py-1 rounded bg-red-100 text-red-700">Critical</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm flex gap-2">
                        <button class="text-blue-600 hover:underline">Edit</button>
                        <button class="text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal thêm sản phẩm -->
<div id="productModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6 relative">
        <button id="closeModalBtn" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-lg">&times;</button>
        <h2 class="text-xl font-bold mb-4">Thêm sản phẩm mới</h2>
        <form class="space-y-4" onsubmit="return false;">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
                <input type="text" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" placeholder="Nhập tên sản phẩm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Giá</label>
                    <input type="number" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tồn kho</label>
                    <input type="number" class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" placeholder="0">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Danh mục</label>
                <select class="mt-1 block w-full border border-gray-300 rounded px-3 py-2">
                    <option>Electronics</option>
                    <option>Accessories</option>
                    <option>Parts</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Hình ảnh</label>
                <input type="file" class="mt-1 block w-full text-sm text-gray-600">
            </div>
            <button type="button" class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white py-2 rounded hover:from-purple-600 hover:to-pink-600">Thêm sản phẩm</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Modal
    const modal = document.getElementById('productModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');

    openBtn.addEventListener('click', () => modal.classList.remove('hidden'));
    closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
    modal.addEventListener('click', (e) => {
        if(e.target === modal) modal.classList.add('hidden');
    });

    // Search filter
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('#productsTable tr');

    searchInput.addEventListener('input', () => {
        const term = searchInput.value.toLowerCase();
        tableRows.forEach(row => {
            const name = row.cells[0].innerText.toLowerCase();
            row.style.display = name.includes(term) ? '' : 'none';
        });
    });
</script>
@endpush
