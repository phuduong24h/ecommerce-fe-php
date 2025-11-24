@extends('layouts.admin')
@section('title', 'Danh sách Đơn Hàng')

@section('content')
    <div class="space-y-6">

        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold">Đơn Hàng</h1>
            <p class="text-muted-foreground">Quản lý đơn hàng và trạng thái giao hàng</p>
            {{-- Thông báo flash --}}
            @if(session('success'))
                <div class="mt-2 mb-4 px-4 py-2 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mt-2 mb-4 px-4 py-2 bg-red-100 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <!-- Card chứa search + filter + export -->
        <div class="bg-white rounded-lg shadow p-4 space-y-4">

            <!-- Search + Filter + Export -->
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <!-- Search -->
                <div class="relative flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="Tìm kiếm Mã đơn, Khách hàng hoặc Sản phẩm..."
                        class="pl-10 w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <!-- Filter (Status) -->
                <select id="statusFilter" class="border border-gray-300 rounded px-3 py-2 w-[180px]">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="PENDING">Chờ xử lý</option>
                    <option value="PROCESSING">Đang xử lý</option>
                    <option value="SHIPPED">Đã gửi hàng</option>
                    <option value="DELIVERED">Đã giao</option>
                    <option value="CANCELLED">Đã hủy</option>
                </select>


                <!-- Export -->
                <button id="exportBtn"
                    class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-4 py-2 rounded hover:from-cyan-600 hover:to-blue-600 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12v8m0 0l-3-3m3 3l3-3m-6-8h6m-6 0l3-3m-3 3l-3-3" />
                    </svg>
                    Xuất CSV
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã đơn hàng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Khách hàng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số sản phẩm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tổng tiền</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày đặt</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTable" class="bg-white divide-y divide-gray-200">
                        @forelse($orders as $o)
                            <tr>
                                <td class="px-6 py-4">{{ $o['id'] }}</td>
                                <td class="px-6 py-4">{{ $o['userName'] ?? $o['customer'] }}</td>
                                <td class="px-6 py-4">{{ count($o['items'] ?? []) }}</td>
                                <td class="px-6 py-4">${{ number_format($o['totalAmount'] ?? 0, 2) }}</td>
                                <td class="px-6 py-4">
                                    {{ isset($o['createdAt']) ? \Carbon\Carbon::parse($o['createdAt'])->format('Y-m-d') : '' }}
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.orders.updateStatus', $o['id']) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()"
                                            class="border border-gray-300 rounded px-2 py-1 text-sm cursor-pointer">
                                            <option value="PENDING" {{ $o['status'] == 'PENDING' ? 'selected' : '' }}
                                                style="background-color:#FEF3C7; color:#B45309;">Chờ xử lý
                                            </option>
                                            <option value="PROCESSING" {{ $o['status'] == 'PROCESSING' ? 'selected' : '' }}
                                                style="background-color:#DBEAFE; color:#1D4ED8;">Đang xử lý
                                            </option>
                                            <option value="SHIPPED" {{ $o['status'] == 'SHIPPED' ? 'selected' : '' }}
                                                style="background-color:#EDE9FE; color:#7C3AED;">Đã gửi hàng
                                            </option>
                                            <option value="DELIVERED" {{ $o['status'] == 'DELIVERED' ? 'selected' : '' }}
                                                style="background-color:#D1FAE5; color:#065F46;">Đã giao
                                            </option>
                                            <option value="CANCELLED" {{ $o['status'] == 'CANCELLED' ? 'selected' : '' }}
                                                style="background-color:#FEE2E2; color:#991B1B;">Đã hủy
                                            </option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 flex gap-2">
                                    <a href="{{ route('admin.orders.show', $o['id']) }}"
                                        class="text-blue-600 hover:text-blue-700 hover:bg-blue-50 px-2 py-1 rounded flex items-center gap-1">
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
            const filter = statusFilter.value; // PENDING, PROCESSING, ...

            document.querySelectorAll('#ordersTable tr').forEach(row => {
                const cells = row.querySelectorAll('td');
                if (!cells.length) return;

                const orderId = cells[0]?.innerText.toLowerCase() || '';
                const customer = cells[1]?.innerText.toLowerCase() || '';
                const items = cells[2]?.innerText.toLowerCase() || '';

                const select = cells[5]?.querySelector('select');
                const rowStatusValue = select?.value || '';
                const rowStatusLabel = select?.selectedOptions[0]?.text.toLowerCase() || '';

                const matchesSearch = orderId.includes(term)
                    || customer.includes(term)
                    || items.includes(term)
                    || rowStatusLabel.includes(term); // search theo label
                const matchesFilter = filter === 'all' || rowStatusValue === filter;

                row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
            });
        }


        searchInput.addEventListener('input', filterTable);
        statusFilter.addEventListener('change', filterTable);


        // Export CSV
        exportBtn.addEventListener('click', function () {
            const visibleRows = Array.from(tableRows).filter(r => r.style.display !== 'none');
            if (visibleRows.length === 0) return alert('Không có đơn hàng để xuất');

            const csv = [];
            const headers = ['Order ID', 'Customer', 'Items', 'Total Amount', 'Date', 'Status'];
            csv.push(headers.join(','));

            visibleRows.forEach(row => {
                const cols = Array.from(row.cells).slice(0, 6).map(td => `"${td.innerText.replace(/"/g, '""')}"`);
                csv.push(cols.join(','));
            });

            const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'orders.csv';
            link.click();
        });

        // Cập nhật màu dropdown status
        document.querySelectorAll('select[name="status"]').forEach(select => {
            function updateColor() {
                const colors = {
                    PENDING: { bg: '#FEF3C7', color: '#B45309' },
                    PROCESSING: { bg: '#DBEAFE', color: '#1D4ED8' },
                    SHIPPED: { bg: '#EDE9FE', color: '#7C3AED' },
                    DELIVERED: { bg: '#D1FAE5', color: '#065F46' },
                    CANCELLED: { bg: '#FEE2E2', color: '#991B1B' }
                };
                const val = select.value;
                select.style.backgroundColor = colors[val].bg;
                select.style.color = colors[val].color;
            }
            updateColor();
            select.addEventListener('change', updateColor);
        });
    </script>
@endpush