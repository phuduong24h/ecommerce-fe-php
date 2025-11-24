@extends('layouts.admin')

@section('title', 'Quản Lý Bổ Sung')

@section('content')
<div class="space-y-6" x-data>
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Quản Lý Bổ Sung</h1>
        <p class="text-muted-foreground">Quản lý danh mục, khuyến mãi, trung tâm bảo hành, serial</p>
    </div>

    <!-- Flash Messages -->
    <div x-transition>
        {{-- Categories --}}
        <template x-if="$store.tab === 'categories' && '{{ session('activeTab') }}' === 'categories'">
            <div x-data="{ open: true }" x-show="open" x-transition
                 x-init="setTimeout(() => open = false, 4000)"
                 class="fixed top-4 right-4 bg-emerald-100 text-emerald-700 p-4 rounded shadow-lg z-50 flex items-center justify-between gap-4">
                <span>{{ session('success') }}</span>
                <button @click="open = false" class="font-bold text-emerald-800">&times;</button>
            </div>
        </template>

        {{-- Promotions --}}
        <template x-if="$store.tab === 'promotions' && '{{ session('activeTab') }}' === 'promotions'">
            <div x-data="{ open: true }" x-show="open" x-transition
                 x-init="setTimeout(() => open = false, 4000)"
                 class="fixed top-4 right-4 bg-purple-100 text-purple-700 p-4 rounded shadow-lg z-50 flex items-center justify-between gap-4">
                <span>{{ session('success') }}</span>
                <button @click="open = false" class="font-bold text-purple-800">&times;</button>
            </div>
        </template>

        {{-- Centers --}}
        <template x-if="$store.tab === 'centers' && '{{ session('activeTab') }}' === 'centers'">
            <div x-data="{ open: true }" x-show="open" x-transition
                 x-init="setTimeout(() => open = false, 4000)"
                 class="fixed top-4 right-4 bg-blue-100 text-blue-700 p-4 rounded shadow-lg z-50 flex items-center justify-between gap-4">
                <span>{{ session('success') }}</span>
                <button @click="open = false" class="font-bold text-blue-800">&times;</button>
            </div>
        </template>

        {{-- Serials --}}
        <template x-if="$store.tab === 'serials' && '{{ session('activeTab') }}' === 'serials'">
            <div x-data="{ open: true }" x-show="open" x-transition
                 x-init="setTimeout(() => open = false, 4000)"
                 class="fixed top-4 right-4 bg-cyan-100 text-cyan-700 p-4 rounded shadow-lg z-50 flex items-center justify-between gap-4">
                <span>{{ session('success') }}</span>
                <button @click="open = false" class="font-bold text-cyan-800">&times;</button>
            </div>
        </template>

        <!-- {{-- Logs --}}
        <template x-if="$store.tab === 'logs' && '{{ session('activeTab') }}' === 'logs'">
            <div x-data="{ open: true }" x-show="open" x-transition
                 x-init="setTimeout(() => open = false, 4000)"
                 class="fixed top-4 right-4 bg-indigo-100 text-indigo-700 p-4 rounded shadow-lg z-50 flex items-center justify-between gap-4">
                <span>{{ session('success') }}</span>
                <button @click="open = false" class="font-bold text-indigo-800">&times;</button>
            </div>
        </template> -->
    </div>

    <!-- Tabs -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="border-b">
            <div class="grid grid-cols-5">
                <button @click="$store.tab = 'categories'"
                        :class="{ 'border-b-2 border-cyan-500 text-foreground font-medium': $store.tab === 'categories' }"
                        class="px-4 py-3 text-sm hover:text-foreground transition-colors text-center">Danh mục</button>
                <button @click="$store.tab = 'promotions'"
                        :class="{ 'border-b-2 border-purple-500 text-foreground font-medium': $store.tab === 'promotions' }"
                        class="px-4 py-3 text-sm hover:text-foreground transition-colors text-center">Khuyến mãi</button>
                <button @click="$store.tab = 'centers'"
                        :class="{ 'border-b-2 border-blue-500 text-foreground font-medium': $store.tab === 'centers' }"
                        class="px-4 py-3 text-sm hover:text-foreground transition-colors text-center">Trung tâm bảo hành</button>
                <button @click="$store.tab = 'serials'"
                        :class="{ 'border-b-2 border-cyan-500 text-foreground font-medium': $store.tab === 'serials' }"
                        class="px-4 py-3 text-sm hover:text-foreground transition-colors text-center">Serial</button>
                <!-- <button @click="$store.tab = 'logs'"
                        :class="{ 'border-b-2 border-indigo-500 text-foreground font-medium': $store.tab === 'logs' }"
                        class="px-4 py-3 text-sm hover:text-foreground transition-colors text-center">Nhật ký quản trị</button> -->
            </div>
        </div>

        <div class="p-6">
            {{-- ================= DANH MỤC ================= --}}
            <template x-if="$store.tab === 'categories'">
                <div x-transition>
                    @include('admin.settings.categories.index')
                </div>
            </template>

            {{-- ================= KHUYẾN MÃI ================= --}}
            <template x-if="$store.tab === 'promotions'">
                <div x-transition>
                    @include('admin.settings.promotions.index')
                </div>
            </template>

            {{-- ================= TRUNG TÂM ================= --}}
            <template x-if="$store.tab === 'centers'">
                <div x-transition>
                    @include('admin.settings.centers.index')
                </div>
            </template>

            {{-- ================= SERIAL ================= --}}
            <template x-if="$store.tab === 'serials'">
                <div x-transition>
                    @include('admin.settings.serials.index')
                </div>
            </template>

            {{-- ================= NHẬT KÝ ================= --}}
            <template x-if="$store.tab === 'logs'">
                <div x-transition>
                    @include('admin.settings.logs.index')
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('tab', '{{ session('activeTab', $activeTab ?? 'categories') }}');
});

// Confirm deletes
function confirmDelete() {
    return confirm('Bạn có chắc chắn muốn xóa danh mục này không?');
}
function confirmDeletePromo() {
    return confirm('Bạn có chắc chắn muốn xóa khuyến mãi này không?');
}

// Search filters
document.addEventListener('DOMContentLoaded', () => {
    // Categories search
    const searchCategories = document.getElementById('searchCategories');
    if (searchCategories) {
        searchCategories.addEventListener('input', () => {
            const term = searchCategories.value.toLowerCase();
            document.querySelectorAll('#categoryGrid > div').forEach(card => {
                const name = card.querySelector('h3')?.innerText.toLowerCase() || '';
                const desc = card.querySelector('p')?.innerText.toLowerCase() || '';
                card.style.display = (name.includes(term) || desc.includes(term)) ? '' : 'none';
            });
        });
    }

    // Promotions search
    const searchPromotions = document.getElementById('searchPromotions');
    if (searchPromotions) {
        searchPromotions.addEventListener('input', () => {
            const term = searchPromotions.value.toLowerCase();
            document.querySelectorAll('#promotionGrid > div').forEach(card => {
                const code = card.querySelector('h3')?.innerText.toLowerCase() || '';
                const discount = card.querySelector('p')?.innerText.toLowerCase() || '';
                card.style.display = (code.includes(term) || discount.includes(term)) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
@endsection
