<div x-transition class="space-y-4">

    <!-- Header: Add Promotion button -->
    <div class="flex justify-end">
        <a href="{{ route('admin.settings.promotions.create') }}"
           class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm Khuyến Mãi
        </a>
    </div>

    <!-- Table -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="border-b bg-muted/50">
                <tr>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Mã khuyến mãi</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Chiết khấu</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Trạng thái</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground text-xs uppercase tracking-wider">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($promotions as $promo)
                <tr class="hover:bg-muted/50 transition-colors">
                    <td class="p-4 align-middle">
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <span class="font-mono font-medium">{{ $promo['code'] }}</span>
                        </div>
                    </td>
                    <td class="p-4 align-middle">{{ $promo['discount'] * 100 }}% off</td>
                    <td class="p-4 align-middle">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $promo['isActive'] ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $promo['isActive'] ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="p-4 align-middle">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.settings.promotions.edit', $promo['id']) }}" 
                               class="h-8 w-8 rounded-md text-blue-600 hover:bg-blue-50 flex items-center justify-center">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.settings.promotions.destroy', $promo['id']) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa khuyến mãi này không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="h-8 w-8 rounded-md text-red-600 hover:bg-red-50 flex items-center justify-center">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2.5 2.5 0 0116.138 21H7.862a2.5 2.5 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500 py-8">Chưa có khuyến mãi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination giữ tab -->
        <div class="mt-4">
            {{ $promotions->appends([
                'activeTab' => 'promotions',
                'page_categories' => request()->get('page_categories'),
                'page_centers' => request()->get('page_centers'),
                'page_serials' => request()->get('page_serials'),
                'page_logs' => request()->get('page_logs'),
            ])->links() }}
        </div>
    </div>

</div>
