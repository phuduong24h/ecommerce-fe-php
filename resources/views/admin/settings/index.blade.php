@extends('layouts.admin')

@section('title', 'Additional Management')

@section('content')
<div class="space-y-6" x-data>
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Additional Management</h1>
        <p class="text-muted-foreground">Manage categories, promotions, service centers, and more</p>
    </div>

    @php
        // MOCK DATA
        $categories = [
            ['id' => 1, 'name' => 'Electronics', 'productCount' => 156],
            ['id' => 2, 'name' => 'Accessories', 'productCount' => 98],
            ['id' => 3, 'name' => 'Parts', 'productCount' => 72],
            ['id' => 4, 'name' => 'Software', 'productCount' => 14],
        ];

        $promotions = [
            ['id' => 1, 'code' => 'SAVE20', 'discount' => 20, 'isActive' => true],
            ['id' => 2, 'code' => 'WELCOME10', 'discount' => 10, 'isActive' => true],
            ['id' => 3, 'code' => 'SUMMER25', 'discount' => 25, 'isActive' => false],
        ];

        $centers = [
            ['id' => 1, 'name' => 'Tech Support Center - Downtown', 'address' => '123 Main St, City Center', 'phone' => '(555) 123-4567'],
            ['id' => 2, 'name' => 'Tech Support Center - North', 'address' => '456 North Ave, Northside', 'phone' => '(555) 234-5678'],
            ['id' => 3, 'name' => 'Tech Support Center - East', 'address' => '789 East Blvd, Eastside', 'phone' => '(555) 345-6789'],
        ];

        $serials = [
            ['id' => 1, 'serial' => 'SN-12345-ABCD', 'productName' => 'Wireless Mouse', 'status' => 'sold'],
            ['id' => 2, 'serial' => 'SN-67890-EFGH', 'productName' => 'Mechanical Keyboard', 'status' => 'warranty-claimed'],
            ['id' => 3, 'serial' => 'SN-11111-IJKL', 'productName' => 'Monitor 27"', 'status' => 'available'],
            ['id' => 4, 'serial' => 'SN-22222-MNOP', 'productName' => 'Wireless Mouse', 'status' => 'sold'],
        ];

        $logs = [
            ['id' => 1, 'adminName' => 'Admin User', 'action' => 'Approved warranty claim WC-001', 'timestamp' => '2025-10-29 10:30'],
            ['id' => 2, 'adminName' => 'Admin User', 'action' => 'Updated product stock for Wireless Mouse', 'timestamp' => '2025-10-29 09:15'],
            ['id' => 3, 'adminName' => 'Support Agent', 'action' => 'Created new warranty claim WC-003', 'timestamp' => '2025-10-28 16:45'],
            ['id' => 4, 'adminName' => 'Admin User', 'action' => 'Added new promotion code SAVE20', 'timestamp' => '2025-10-28 14:20'],
        ];
    @endphp

    <!-- Tabs -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="border-b">
            <div class="grid grid-cols-5">
                <button @click="$store.tab = 'categories'" :class="{ 'border-b-2 border-cyan-500 text-foreground font-medium': $store.tab === 'categories' }" class="px-4 py-3 text-sm hover:text-foreground transition-colors text-center">Categories</button>
                <button @click="$store.tab = 'promotions'" :class="{ 'border-b-2 border-purple-500 text-foreground font-medium': $store.tab === 'promotions' }" class="px-4 py-3 text-sm hover:text-foreground transition-colors text-center">Promotions</button>
                <button @click="$store.tab = 'centers'" :class="{ 'border-b-2 border-blue-500 text-foreground font-medium': $store.tab === 'centers' }" class="px-4 py-3 text-sm hover:text-foreground transition-colors text-center">Service Centers</button>
                <button @click="$store.tab = 'serials'" :class="{ 'border-b-2 border-cyan-500 text-foreground font-medium': $store.tab === 'serials' }" class="px-4 py-3 text-sm hover:text-foreground transition-colors text-center">Serials</button>
                <button @click="$store.tab = 'logs'" :class="{ 'border-b-2 border-indigo-500 text-foreground font-medium': $store.tab === 'logs' }" class="px-4 py-3 text-sm hover:text-foreground transition-colors text-center">Admin Logs</button>
            </div>
        </div>

        <div class="p-6">
            <!-- CHỈ RENDER KHI CLICK -->
            <template x-if="$store.tab === 'categories'">
                <div x-transition class="space-y-4">
                    <div class="flex justify-end">
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Category
                        </button>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        @foreach($categories as $cat)
                        <div class="rounded-lg border-2 hover:border-cyan-200 transition-colors bg-card text-card-foreground shadow-sm">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-cyan-100 to-blue-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    </div>
                                    <div class="flex gap-1">
                                        <button class="h-8 w-8 p-0 rounded-md text-blue-600 hover:bg-blue-50 flex items-center justify-center">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button class="h-8 w-8 p-0 rounded-md text-red-600 hover:bg-red-50 flex items-center justify-center">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2.5 2.5 0 0116.138 21H7.862a2.5 2.5 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <h3 class="font-semibold text-lg">{{ $cat['name'] }}</h3>
                                <p class="text-sm text-muted-foreground">{{ $cat['productCount'] }} products</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </template>

            <template x-if="$store.tab === 'promotions'">
                <div x-transition class="space-y-4">
                    <div class="flex justify-end">
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Promotion
                        </button>
                    </div>
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm overflow-hidden">
                        <table class="w-full">
                            <thead class="border-b bg-muted/50">
                                <tr>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Promotion Code</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Discount</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($promotions as $promo)
                                <tr class="hover:bg-muted/50 transition-colors">
                                    <td class="p-4 align-middle">
                                        <div class="flex items-center gap-2">
                                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center">
                                                <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                            </div>
                                            <span class="font-mono font-medium">{{ $promo['code'] }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle">{{ $promo['discount'] }}% off</td>
                                    <td class="p-4 align-middle">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $promo['isActive'] ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $promo['isActive'] ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="p-4 align-middle">
                                        <div class="flex gap-2">
                                            <button class="h-8 w-8 rounded-md text-blue-600 hover:bg-blue-50 flex items-center justify-center">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button class="h-8 w-8 rounded-md text-red-600 hover:bg-red-50 flex items-center justify-center">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2.5 2.5 0 0116.138 21H7.862a2.5 2.5 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <!-- Các tab khác tương tự... (giữ nguyên như trên) -->
            <!-- Centers, Serials, Logs – đã có trong file trước, copy nguyên -->

            <template x-if="$store.tab === 'centers'">
                <div x-transition class="space-y-4">
                    <div class="flex justify-end">
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Service Center
                        </button>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($centers as $center)
                        <div class="rounded-lg border-2 hover:border-blue-200 transition-colors bg-card text-card-foreground shadow-sm">
                            <div class="p-6">
                                <h3 class="font-semibold text-lg flex items-start gap-2 mb-2">
                                    <svg class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $center['name'] }}
                                </h3>
                                <p class="text-sm text-muted-foreground mb-1">{{ $center['address'] }}</p>
                                <p class="text-sm text-blue-600 mb-4">{{ $center['phone'] }}</p>
                                <div class="flex gap-2">
                                    <button class="flex-1 h-9 rounded-md border border-blue-200 text-blue-600 hover:bg-blue-50 text-sm font-medium flex items-center justify-center gap-1">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>
                                    <button class="h-9 w-9 rounded-md border border-red-200 text-red-600 hover:bg-red-50 flex items-center justify-center">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2.5 2.5 0 0116.138 21H7.862a2.5 2.5 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </template>

            <template x-if="$store.tab === 'serials'">
                <div x-transition class="space-y-4">
                    <div class="flex justify-end">
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Serial
                        </button>
                    </div>
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm overflow-hidden">
                        <table class="w-full">
                            <thead class="border-b bg-muted/50">
                                <tr>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Serial Number</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Product</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($serials as $serial)
                                @php
                                    $statusClass = $serial['status'] === 'available'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : ($serial['status'] === 'sold'
                                            ? 'bg-blue-100 text-blue-700'
                                            : 'bg-amber-100 text-amber-700');

                                    $statusText = $serial['status'] === 'available'
                                        ? 'Available'
                                        : ($serial['status'] === 'sold'
                                            ? 'Sold'
                                            : 'Warranty Claimed');
                                @endphp
                                <tr class="hover:bg-muted/50 transition-colors">
                                    <td class="p-4 align-middle">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                            <span class="font-mono text-sm">{{ $serial['serial'] }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle">{{ $serial['productName'] }}</td>
                                    <td class="p-4 align-middle">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="p-4 align-middle">
                                        <div class="flex gap-2">
                                            <button class="h-8 w-8 rounded-md text-blue-600 hover:bg-blue-50 flex items-center justify-center">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button class="h-8 w-8 rounded-md text-red-600 hover:bg-red-50 flex items-center justify-center">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2.5 2.5 0 0116.138 21H7.862a2.5 2.5 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <template x-if="$store.tab === 'logs'">
                <div x-transition class="space-y-4">
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
                        <h3 class="text-lg font-semibold mb-4">Activity Log</h3>
                        <div class="space-y-4">
                            @foreach($logs as $log)
                            <div class="flex items-start gap-4 p-4 border rounded-lg hover:bg-muted/50 transition-colors">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div class="flex-1 space-y-1">
                                    <p class="text-sm font-medium">{{ $log['action'] }}</p>
                                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                        <span>{{ $log['adminName'] }}</span>
                                        <span>•</span>
                                        <span>{{ $log['timestamp'] }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </template>

            <!-- Fallback nếu chưa chọn tab -->
            <div x-show="!$store.tab" class="text-center text-muted-foreground py-8">
                Please select a tab to view content.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('tab', 'categories'); // Mặc định mở Categories
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
@endsection