@extends('layouts.admin')

@section('title', 'Quản lý bảo hành')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Bảo hành</h1>
        <p class="text-muted-foreground">Xem và quản lý các yêu cầu bảo hành</p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mt-4 mb-4 px-4 py-2 bg-green-100 text-green-800 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mt-4 mb-4 px-4 py-2 bg-red-100 text-red-800 rounded shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @php
        $claims = $claims ?? [];
        $statusConfig = [
            'PENDING' => ['label' => 'Chờ xử lý', 'bg' => '#FEF3C7', 'text' => '#B45309'],
            'APPROVED' => ['label' => 'Đã duyệt', 'bg' => '#D1FAE5', 'text' => '#065F46'],
            'REJECTED' => ['label' => 'Từ chối', 'bg' => '#FEE2E2', 'text' => '#991B1B'],
            'SCHEDULED' => ['label' => 'Đã lên lịch', 'bg' => '#DBEAFE', 'text' => '#1D4ED8'],
            'IN_REPAIR' => ['label' => 'Đang sửa', 'bg' => '#E0F2FE', 'text' => '#0284C7'],
            'REPAIRED' => ['label' => 'Đã sửa xong', 'bg' => '#E0F7FA', 'text' => '#006064'],
            'COMPLETED' => ['label' => 'Hoàn tất', 'bg' => '#D1FAE5', 'text' => '#065F46'],
            'CANCELLED' => ['label' => 'Hủy', 'bg' => '#FEE2E2', 'text' => '#991B1B'],
        ];

        $statusCounts = [];
        foreach ($claims as $claim) {
            $statusCounts[$claim['status']] = ($statusCounts[$claim['status']] ?? 0) + 1;
        }
    @endphp

    <!-- Stats Cards -->
    <div class="grid gap-4 md:grid-cols-4">
        @foreach($statusConfig as $status => $config)
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">{{ $config['label'] }}</p>
                        <p class="text-2xl font-bold mt-1" style="color: {{ $config['text'] }}">
                            {{ $statusCounts[$status] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Search & Filter -->
    <div class="p-6 border rounded-lg bg-card mt-4 flex flex-col sm:flex-row gap-4">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
            </svg>
            <input type="text" id="searchInput" placeholder="Tìm kiếm theo ID, sản phẩm hoặc khách hàng..."
                class="w-full h-10 pl-10 pr-3 rounded-md border border-input bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors" />
        </div>
        <select id="statusFilter"
            class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring transition-colors">
            <option value="all">Tất cả trạng thái</option>
            @foreach($statusConfig as $status => $config)
                <option value="{{ $status }}">{{ $config['label'] }}</option>
            @endforeach
        </select>
    </div>

    <!-- Table -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm overflow-x-auto mt-4">
        <table class="w-full">
            <thead class="border-b bg-gray-50">
                <tr>
                    <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">ID</th>
                    <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Sản phẩm</th>
                    <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Khách hàng</th>
                    <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Vấn đề</th>
                    <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Ngày nộp</th>
                    <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($claims as $claim)
                    @php
                        $claim = (array) $claim;
                        $config = $statusConfig[$claim['status']] ?? $statusConfig['PENDING'];
                    @endphp
                    <tr class="hover:bg-muted/50 transition-colors">
                        <td class="p-4 text-sm">{{ $claim['id'] }}</td>
                        <td class="p-4 text-sm">{{ $claim['productName'] }}</td>
                        <td class="p-4 text-sm">{{ $claim['userName'] }}</td>
                        <td class="p-4 text-sm max-w-xs truncate">{{ $claim['issueDesc'] }}</td>
                        <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($claim['createdAt'])->format('d/m/Y H:i') }}</td>
                        <td class="p-4">
                            <form action="{{ route('admin.warranty.update', $claim['id']) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <select name="status" onchange="this.form.submit()"
                                    class="px-2 py-1 rounded text-sm font-semibold cursor-pointer"
                                    style="background-color: {{ $config['bg'] }}; color: {{ $config['text'] }}">
                                    @foreach($statusConfig as $status => $sConfig)
                                        <option value="{{ $status }}" {{ $claim['status'] == $status ? 'selected' : '' }}
                                            style="background-color: {{ $sConfig['bg'] }}; color: {{ $sConfig['text'] }}">
                                            {{ $sConfig['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">Không có yêu cầu bảo hành nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $claims->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');

        // Filter table
        function filterRows() {
            const term = searchInput.value.toLowerCase();
            const status = statusFilter.value.toLowerCase();

            document.querySelectorAll('table tbody tr').forEach(row => {
                const cells = row.querySelectorAll('td');
                if (!cells.length) return;

                const id = cells[0]?.innerText.toLowerCase() || '';
                const product = cells[1]?.innerText.toLowerCase() || '';
                const customer = cells[2]?.innerText.toLowerCase() || '';

                const select = cells[5]?.querySelector('select');
                const statusText = select?.selectedOptions[0]?.text.toLowerCase() || '';

                const matchText = id.includes(term) || product.includes(term) || customer.includes(term) || statusText.includes(term);
                const matchStatus = status === 'all' || select?.value.toLowerCase() === status;

                row.style.display = (matchText && matchStatus) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterRows);
        statusFilter.addEventListener('change', filterRows);
    });
</script>
@endpush
@endsection
