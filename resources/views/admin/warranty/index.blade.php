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
        $claims = [
            ['id'=>'WC-001','productName'=>'Wireless Mouse','customerName'=>'John Doe','status'=>'approved','issueDesc'=>'Left click button not responding','dateSubmitted'=>'2025-10-25'],
            ['id'=>'WC-002','productName'=>'Mechanical Keyboard','customerName'=>'Jane Smith','status'=>'in-progress','issueDesc'=>'Keys not registering','dateSubmitted'=>'2025-10-27'],
            ['id'=>'WC-003','productName'=>'Monitor 27"','customerName'=>'Bob Johnson','status'=>'pending','issueDesc'=>'Screen flickering issue','dateSubmitted'=>'2025-10-28'],
            ['id'=>'WC-004','productName'=>'USB-C Cable','customerName'=>'Alice Williams','status'=>'rejected','issueDesc'=>'Physical damage - not covered','dateSubmitted'=>'2025-10-26'],
        ];

        $statusConfig = [
            'pending' => ['label' => 'Pending', 'color' => 'yellow', 'lucide' => 'clock'],
            'in-progress' => ['label' => 'In Progress', 'color' => 'blue', 'lucide' => 'refresh-cw'],
            'approved' => ['label' => 'Approved', 'color' => 'emerald', 'lucide' => 'check-circle'],
            'rejected' => ['label' => 'Rejected', 'color' => 'red', 'lucide' => 'x-circle'],
        ];

        // ĐƯA ICONS LÊN TRÊN ĐÂY ĐỂ TRÁNH LỖI
        $lucideIcons = [
            'search' => '<svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"/></svg>',
            'eye' => '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>',
            'check-circle' => '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'x-circle' => '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'clock' => '<svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'refresh-cw' => '<svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>',
            'check-circle-big' => '<svg class="h-8 w-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'x-circle-big' => '<svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        ];
    @endphp

    <!-- Stats Cards -->
    <div class="grid gap-4 md:grid-cols-4">
        @foreach($statusConfig as $status => $config)
        @php $count = collect($claims)->where('status', $status)->count(); @endphp
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">{{ $config['label'] }}</p>
                        <p class="text-2xl font-bold mt-1 text-{{ $config['color'] }}-600">
                            {{ $count }}
                        </p>
                    </div>
                    @if($status === 'pending')
                        {!! $lucideIcons['clock'] !!}
                    @elseif($status === 'in-progress')
                        {!! $lucideIcons['refresh-cw'] !!}
                    @elseif($status === 'approved')
                        {!! $lucideIcons['check-circle-big'] !!}
                    @elseif($status === 'rejected')
                        {!! $lucideIcons['x-circle-big'] !!}
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Search + Filter + Table Card -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <!-- Header -->
        <div class="p-6 border-b">
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Search -->
                <div class="relative flex-1">
                    {!! $lucideIcons['search'] !!}
                    <input 
                        type="text" 
                        placeholder="Tìm kiếm bảo hành..." 
                        class="w-full h-10 pl-10 pr-3 rounded-md border border-input bg-background text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 transition-colors"
                    />
                </div>

                <!-- Filter -->
                <select class="h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 transition-colors">
                    <option value="all">Tất cả trạng thái</option>
                    @foreach($statusConfig as $status => $config)
                    <option value="{{ $status }}">{{ $config['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">ID</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Sản phẩm</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Khách hàng</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Vấn đề</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Ngày nộp</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Trạng thái</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($claims as $claim)
                    @php $config = $statusConfig[$claim['status']] @endphp
                    <tr class="hover:bg-muted/50 transition-colors">
                        <td class="p-4 align-middle text-sm font-medium">{{ $claim['id'] }}</td>
                        <td class="p-4 align-middle text-sm">{{ $claim['productName'] }}</td>
                        <td class="p-4 align-middle text-sm">{{ $claim['customerName'] }}</td>
                        <td class="p-4 align-middle text-sm max-w-xs truncate">{{ $claim['issueDesc'] }}</td>
                        <td class="p-4 align-middle text-sm">{{ $claim['dateSubmitted'] }}</td>
                        <td class="p-4 align-middle">
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-700">
                                {{ ucfirst(str_replace('-', ' ', $claim['status'])) }}
                            </span>
                        </td>
                        <td class="p-4 align-middle">
                            <div class="flex gap-1.5">
                                <!-- View -->
                                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-8 w-8 hover:bg-accent hover:text-accent-foreground">
                                    {!! $lucideIcons['eye'] !!}
                                </button>

                                @if($claim['status'] === 'pending')
                                <!-- Approve -->
                                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-8 w-8 text-emerald-600 hover:bg-emerald-50">
                                    {!! $lucideIcons['check-circle'] !!}
                                </button>
                                <!-- Reject -->
                                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-8 w-8 text-red-600 hover:bg-red-50">
                                    {!! $lucideIcons['x-circle'] !!}
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection