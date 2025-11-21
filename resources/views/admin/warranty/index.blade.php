@extends('layouts.admin')

@section('title', 'Quản lý bảo hành')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Bảo hành</h1>
            <p class="text-muted-foreground">Xem và quản lý các yêu cầu bảo hành</p>
        </div>

        @php
            $claims = $claims ?? [];
            $statusConfig = [
                'PENDING' => ['label' => 'Pending', 'color' => 'yellow', 'lucide' => 'clock'],
                'APPROVED' => ['label' => 'Approved', 'color' => 'emerald', 'lucide' => 'check-circle-big'],
                'REJECTED' => ['label' => 'Rejected', 'color' => 'red', 'lucide' => 'x-circle-big'],
                'SCHEDULED' => ['label' => 'Scheduled', 'color' => 'blue', 'lucide' => 'calendar'],
                'IN_REPAIR' => ['label' => 'In Repair', 'color' => 'sky', 'lucide' => 'refresh-cw'],
                'REPAIRED' => ['label' => 'Repaired', 'color' => 'cyan', 'lucide' => 'check'],
                'COMPLETED' => ['label' => 'Completed', 'color' => 'emerald', 'lucide' => 'award'],
                'CANCELLED' => ['label' => 'Cancelled', 'color' => 'gray', 'lucide' => 'x'],
            ];


            $lucideIcons = [
                'search' => '<svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"/></svg>',
                'eye' => '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>',
                'check-circle' => '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'x-circle' => '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            ];
        @endphp
        <!-- Stats Cards -->
        <div class="grid gap-4 md:grid-cols-4">
            @foreach($statusConfig as $status => $config)
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">{{ $config['label'] }}</p>
                            <p class="text-2xl font-bold mt-1 text-{{ $config['color'] }}-600 stats-count"
                                data-status="{{ $status }}">
                                {{ $statusCounts[$status] ?? 0 }}
                            </p>
                        </div>
                        {!! $lucideIcons[$config['lucide']] ?? '' !!}
                    </div>
                </div>
            @endforeach
        </div>


        <!-- Search & Filter -->
        <div class="p-6 border rounded-lg bg-card mt-4 flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1">
                {!! $lucideIcons['search'] !!}
                <input type="text" id="searchInput" placeholder="Tìm kiếm bảo hành..."
                    class="w-full h-10 pl-10 pr-3 rounded-md border border-input bg-background text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 transition-colors" />
            </div>
            <select id="statusFilter"
                class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 transition-colors">
                <option value="all">Tất cả trạng thái</option>
                @foreach($statusConfig as $status => $config)
                    <option value="{{ $status }}">{{ $config['label'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Table -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm overflow-x-auto">
            <table class="w-full">
                <thead class="border-b bg-gray-50">
                    <tr>
                        <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            ID</th>
                        <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Sản phẩm</th>
                        <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Khách hàng</th>
                        <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Vấn đề</th>
                        <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Ngày nộp</th>
                        <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Trạng thái</th>
                        <th class="h-12 px-4 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($claims as $claim)
                        @php
                            $claim = (array) $claim;
                            $config = $statusConfig[$claim['status']] ?? $statusConfig['PENDING'];
                        @endphp
                        <tr class="hover:bg-muted/50 transition-colors action-row">
                            <td class="p-4 text-sm">{{ $claim['id'] }}</td>
                            <td class="p-4 text-sm">{{ $claim['productName'] }}</td>
                            <td class="p-4 text-sm">{{ $claim['userName'] }}</td>
                            <td class="p-4 text-sm max-w-xs truncate">{{ $claim['issueDesc'] }}</td>
                            <td class="p-4 text-sm">{{ \Carbon\Carbon::parse($claim['createdAt'])->format('d/m/Y H:i') }}</td>

                            <td class="p-4">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-700 status-badge">
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex gap-1.5 action-icons opacity-0 transition-opacity duration-200">
                                    <span
                                        class="inline-flex items-center justify-center rounded-md h-8 w-8 hover:bg-accent hover:text-accent-foreground cursor-pointer">
                                        {!! $lucideIcons['eye'] !!}
                                    </span>
                                    @if($claim['status'] === 'PENDING')
                                        <form action="{{ route('admin.warranty.update', $claim['id']) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="APPROVED">
                                            <button type="submit"
                                                class="inline-flex items-center justify-center rounded-md h-8 w-8 text-emerald-600 hover:bg-emerald-50">
                                                {!! $lucideIcons['check-circle'] !!}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.warranty.update', $claim['id']) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="REJECTED">
                                            <button type="submit"
                                                class="inline-flex items-center justify-center rounded-md h-8 w-8 text-red-600 hover:bg-red-50">
                                                {!! $lucideIcons['x-circle'] !!}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">Không có yêu cầu bảo hành nào</td>
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

                function filterRows() {
                    const term = searchInput.value.toLowerCase();
                    const status = statusFilter.value;

                    document.querySelectorAll('table tbody tr').forEach(row => {
                        const cells = row.querySelectorAll('td');
                        const statusText = cells[5]?.innerText.trim() || '';
                        let matchText = Array.from(cells).slice(0, 5).some(cell => cell.innerText.toLowerCase().includes(term));
                        let matchStatus = status === 'all' || statusText === statusFilter.options[statusFilter.selectedIndex].text;
                        row.style.display = (matchText && matchStatus) ? '' : 'none';
                    });
                }

                searchInput.addEventListener('input', filterRows);
                statusFilter.addEventListener('change', filterRows);

                // Hover hiệu ứng hiện icon
                document.querySelectorAll('.action-row').forEach(row => {
                    row.addEventListener('mouseenter', () => {
                        row.querySelector('.action-icons').style.opacity = 1;
                    });
                    row.addEventListener('mouseleave', () => {
                        row.querySelector('.action-icons').style.opacity = 0;
                    });
                });
            });
        </script>
    @endpush

@endsection