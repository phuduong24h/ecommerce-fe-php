@extends('layouts.admin')
@section('title', 'Danh sách Đơn Hàng')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold">Đơn Hàng</h1>
        <p class="text-muted-foreground">Quản lý đơn hàng và trạng thái giao hàng</p>
    </div>

    <!-- Card chứa search + filter + export -->
    <div class="bg-white rounded-lg shadow p-4 space-y-4">

        <!-- Search + Filter + Export -->
        <div class="flex flex-col md:flex-row md:items-center gap-4">
            <!-- Search -->
            <div class="relative flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"/>
                </svg>
                <input type="text" id="searchInput" placeholder="Tìm kiếm Customer hoặc Items..." class="pl-10 w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <!-- Filter (Status) -->
            <select id="statusFilter" class="border border-gray-300 rounded px-3 py-2 w-[180px]">
                <option value="all">Tất cả trạng thái</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>

            <!-- Export -->
            <button id="exportBtn" class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-4 py-2 rounded hover:from-cyan-600 hover:to-blue-600 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12v8m0 0l-3-3m3 3l3-3m-6-8h6m-6 0l3-3m-3 3l-3-3"/>
                </svg>
                Export CSV
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTable" class="bg-white divide-y divide-gray-200">
                    @php
                        $statusClasses = [
                            'PENDING'=>'bg-yellow-100 text-yellow-700',
                            'PROCESSING'=>'bg-blue-100 text-blue-700',
                            'SHIPPED'=>'bg-purple-100 text-purple-700',
                            'DELIVERED'=>'bg-emerald-100 text-emerald-700',
                            'CANCELLED'=>'bg-red-100 text-red-700',
                        ];
                    @endphp

                    @forelse($orders as $o)
                        <tr>
                            <td class="px-6 py-4">{{ $o['id'] }}</td>
                            <td class="px-6 py-4">{{ $o['userName'] ?? $o['customer'] }}</td>
                            <td class="px-6 py-4">{{ count($o['items'] ?? []) }}</td>
                            <td class="px-6 py-4">${{ number_format($o['totalAmount'] ?? 0, 2) }}</td>
                            <td class="px-6 py-4">{{ isset($o['createdAt']) ? \Carbon\Carbon::parse($o['createdAt'])->format('Y-m-d') : '' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded {{ $statusClasses[$o['status'] ?? 'PENDING'] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst(strtolower($o['status'] ?? 'PENDING')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 flex gap-2">
                                <a href="{{ route('admin.orders.show', $o['id']) }}" class="text-blue-600 hover:text-blue-700 hover:bg-blue-50 px-2 py-1 rounded flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">Chưa có đơn hàng</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
    {{ $orders->links() }}
</div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const tableRows = document.querySelectorAll('#ordersTable tr');
const exportBtn = document.getElementById('exportBtn');

function filterTable() {
    const term = searchInput.value.toLowerCase();
    const filter = statusFilter.value.toLowerCase();
    tableRows.forEach(row => {
        const customer = row.cells[1]?.innerText.toLowerCase() || '';
        const items = row.cells[2]?.innerText.toLowerCase() || '';
        const status = row.cells[5]?.innerText.toLowerCase() || '';
        const matchesSearch = customer.includes(term) || items.includes(term);
        const matchesFilter = filter === 'all' || status === filter;
        row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
    });
}

searchInput.addEventListener('input', filterTable);
statusFilter.addEventListener('change', filterTable);

// Export CSV
exportBtn.addEventListener('click', function() {
    const visibleRows = Array.from(tableRows).filter(r => r.style.display !== 'none');
    if (visibleRows.length === 0) return alert('Không có đơn hàng để xuất');

    const csv = [];
    const headers = ['Order ID','Customer','Items','Total Amount','Date','Status'];
    csv.push(headers.join(','));

    visibleRows.forEach(row => {
        const cols = Array.from(row.cells).slice(0,6).map(td => `"${td.innerText.replace(/"/g,'""')}"`);
        csv.push(cols.join(','));
    });

    const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'orders.csv';
    link.click();
});
</script>
@endpush
