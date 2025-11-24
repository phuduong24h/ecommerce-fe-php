<div x-transition class="space-y-4">

    <!-- Header: Search + Add Service Center -->
    <div class="flex justify-between items-center mb-4">
        <div class="relative w-1/2">
            <input type="text" id="searchCenters" placeholder="Tìm kiếm tên, địa chỉ hoặc số điện thoại..."
                   class="pl-10 w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
        </div>
        <a href="{{ route('admin.settings.centers.create') }}"
           class="inline-flex items-center justify-center rounded-md text-sm font-medium h-10 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm Trung Tâm Bảo Hành
        </a>
    </div>

    <!-- Grid of Service Centers -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3" id="centerGrid">
        @forelse($centers as $center)
        <div class="rounded-lg border-2 hover:border-blue-200 transition-colors bg-card text-card-foreground shadow-sm">
            <div class="p-6">
                <h3 class="font-semibold text-lg flex items-start gap-2 mb-2">
                    <svg class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $center['name'] }}
                </h3>
                <p class="text-sm text-muted-foreground mb-1">{{ $center['address'] }}</p>
                <p class="text-sm text-blue-600 mb-4">{{ $center['phone'] ?? '-' }}</p>

                <!-- Action buttons -->
                <div class="flex gap-2">
                    <a href="{{ route('admin.settings.centers.edit', $center['id']) }}"
                       class="flex-1 h-9 rounded-md border border-blue-200 text-blue-600 hover:bg-blue-50 text-sm font-medium flex items-center justify-center gap-1">
                        Sửa
                    </a>
                    <form action="{{ route('admin.settings.centers.destroy', $center['id']) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Service Center này không?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="h-9 w-9 rounded-md border border-red-200 text-red-600 hover:bg-red-50 flex items-center justify-center">
                            Xóa
                        </button>
                    </form>
                </div>

            </div>
        </div>
        @empty
            <div class="col-span-full text-center text-gray-500 py-8">Chưa có Service Center</div>
        @endforelse
    </div>

    <!-- Pagination giữ tab -->
    <div class="mt-4">
        {{ $centers->appends([
            'activeTab' => 'centers',
            'page_categories' => request()->get('page_categories'),
            'page_promotions' => request()->get('page_promotions'),
            'page_serials' => request()->get('page_serials'),
            'page_logs' => request()->get('page_logs'),
        ])->links() }}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchCenters');

    searchInput.addEventListener('input', () => {
        const term = searchInput.value.toLowerCase();
        document.querySelectorAll('#centerGrid > div').forEach(card => {
            const name = card.querySelector('h3')?.innerText.toLowerCase() || '';
            const address = card.querySelector('p:nth-child(2)')?.innerText.toLowerCase() || '';
            const phone = card.querySelector('p:nth-child(3)')?.innerText.toLowerCase() || '';
            card.style.display = (name.includes(term) || address.includes(term) || phone.includes(term)) ? '' : 'none';
        });
    });
});
</script>
@endpush
