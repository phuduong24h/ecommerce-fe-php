<div x-transition class="space-y-4">

    <!-- Header: Search + Add -->
    <div class="flex justify-between items-center mb-4">
        <div class="relative w-1/2">
            <input type="text" id="searchCategories" placeholder="Tìm kiếm danh mục..."
                class="pl-10 w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
        </div>
        <a href="{{ route('admin.settings.categories.create') }}"
            class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-4 py-2 rounded hover:from-cyan-600 hover:to-blue-600 flex items-center gap-2">
            <span>++</span> Thêm danh mục
        </a>
    </div>

    <!-- Category Grid -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4" id="categoryGrid">
        @forelse($categories as $cat)
            <div class="rounded-lg border-2 hover:border-cyan-200 transition-colors bg-white shadow-sm">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-cyan-100 to-blue-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-cyan-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <div class="flex gap-1">
                            <a href="{{ route('admin.settings.categories.edit', $cat['id']) }}"
                                class="h-8 w-8 p-0 rounded-md text-blue-600 hover:bg-blue-50 flex items-center justify-center">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.settings.categories.destroy', $cat['id']) }}"
                                method="POST" onsubmit="return confirmDelete()">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="h-8 w-8 p-0 rounded-md text-red-600 hover:bg-red-50 flex items-center justify-center">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2.5 2.5 0 0116.138 21H7.862a2.5 2.5 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <h3 class="font-semibold text-lg">{{ $cat['name'] }}</h3>
                    <p class="text-sm text-gray-500">{{ $cat['description'] ?? '' }}</p>
                    <p class="text-sm text-gray-400 mt-1">{{ $cat['product_count'] ?? 0 }} products</p>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 col-span-full py-8">Chưa có danh mục</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $categories->appends([
            'activeTab' => 'categories',
            'page_promotions' => request()->get('page_promotions'),
            'page_centers' => request()->get('page_centers'),
            'page_serials' => request()->get('page_serials'),
            'page_logs' => request()->get('page_logs'),
        ])->links() }}
    </div>
</div>

@push('scripts')
<script>
    // Search filter
    document.addEventListener('DOMContentLoaded', () => {
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
    });

    // Confirm delete
    function confirmDelete() {
        return confirm('Bạn có chắc chắn muốn xóa danh mục này không?');
    }
</script>
@endpush
