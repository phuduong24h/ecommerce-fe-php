<template x-if="$store.tab === 'serials'">
    <div x-transition class="space-y-4">

        <!-- Header: Add Serial button -->
        <div class="flex justify-end">
            <a href="{{ route('admin.settings.serials.create') }}"
               class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Serial
            </a>
        </div>

        <!-- Table -->
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
                    @forelse($serials as $serial)
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
                                    <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                    </svg>
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
                                    <a href="{{ route('admin.settings.serials.edit', $serial['id']) }}" 
                                       class="h-8 w-8 rounded-md text-blue-600 hover:bg-blue-50 flex items-center justify-center">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.settings.serials.destroy', $serial['id']) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa serial này không?')">
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
                            <td colspan="4" class="text-center text-gray-500 py-8">Chưa có serial</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination giữ tab -->
            <div class="mt-6">
                {{ $serials->appends([
                    'activeTab' => 'serials',
                    'page_categories' => request()->get('page_categories'),
                    'page_promotions' => request()->get('page_promotions'),
                    'page_centers' => request()->get('page_centers'),
                ])->links() }}
            </div>

        </div>
    </div>
</template>
