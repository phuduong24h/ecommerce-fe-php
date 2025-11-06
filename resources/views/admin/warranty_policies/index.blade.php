@extends('layouts.admin')

@section('title', 'Chính sách bảo hành')

@section('content')
<div class="space-y-6">
    <!-- Header + Add Button -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Chính sách bảo hành</h1>
            <p class="text-muted-foreground">Quản lý các mẫu chính sách bảo hành</p>
        </div>

        <button 
            x-data 
            x-on:click="$dispatch('open-modal', 'add-policy-modal')"
            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white"
        >
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm chính sách
        </button>
    </div>

    <!-- Policies Grid -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach($policies as $policy)
        @php
            $years = floor($policy['duration_days'] / 365);
            $yearText = $years == 1 ? 'năm' : 'năm';
        @endphp
        <div class="rounded-lg border-2 hover:border-cyan-200 transition-colors bg-card text-card-foreground shadow-sm">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-cyan-100 to-blue-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg">{{ $policy['name'] }}</h3>
                            <p class="text-sm text-muted-foreground mt-1">
                                {{ $years }} {{ $yearText }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 pb-6 space-y-4">
                <p class="text-sm text-muted-foreground">{{ $policy['description'] }}</p>
                <div class="flex gap-2">
                    <button class="flex-1 inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-9 px-3 border border-blue-200 text-blue-600 hover:bg-blue-50">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Sửa
                    </button>
                    <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-9 w-9 border border-red-200 text-red-600 hover:bg-red-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal: Add Policy -->
<div 
    x-data="{ open: false }" 
    x-on:open-modal.window="if ($event.detail === 'add-policy-modal') open = true"
    x-on:close-modal.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    <div x-show="open" x-transition class="fixed inset-0 bg-black/50" x-on:click="open = false"></div>
    <div x-show="open" x-transition class="relative w-full max-w-md rounded-lg bg-background p-6 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Thêm chính sách bảo hành mới</h2>
            <button x-on:click="open = false" class="text-muted-foreground hover:text-foreground">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form class="space-y-4">
            <div>
                <label for="policy-name" class="block text-sm font-medium text-foreground mb-1">Tên chính sách</label>
                <input id="policy-name" type="text" placeholder="VD: Standard Warranty" class="w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" />
            </div>
            <div>
                <label for="duration" class="block text-sm font-medium text-foreground mb-1">Thời hạn (ngày)</label>
                <input id="duration" type="number" placeholder="365" class="w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" />
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-foreground mb-1">Mô tả</label>
                <textarea id="description" rows="3" placeholder="Mô tả phạm vi bảo hành..." class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 resize-none"></textarea>
            </div>
            <button type="button" class="w-full h-10 rounded-md bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium transition-all">
                Thêm chính sách
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
@endsection