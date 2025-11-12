<div x-transition class="space-y-4">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Activity Log</h3>
        <div class="space-y-4">
            @forelse($logs as $log)
            <div class="flex items-start gap-4 p-4 border rounded-lg hover:bg-muted/50 transition-colors">
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="flex-1 space-y-1">
                    <p class="text-sm font-medium">{{ $log['action'] }}</p>
                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                        <span>{{ $log['adminName'] ?? $log['adminId'] }}</span>
                        <span>•</span>
                        <span>{{ $log['timestamp'] ?? $log['createdAt'] }}</span>
                        @if($log['target'])
                        <span>•</span>
                        <span>{{ $log['target'] }}</span>
                        @endif
                    </div>
                </div>
                <!-- Delete button -->
                <div class="flex-shrink-0">
                    <form action="{{ route('admin.settings.logs.destroy', $log['id']) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa log này không?')">
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
            </div>
            @empty
                <div class="text-center text-gray-500 py-8">Chưa có log</div>
            @endforelse
        </div>
    </div>

    <!-- Pagination giữ tab -->
    <div class="mt-4">
        {{ $logs->appends([
            'activeTab' => 'logs',
            'page_categories' => request()->get('page_categories'),
            'page_promotions' => request()->get('page_promotions'),
            'page_centers' => request()->get('page_centers'),
            'page_serials' => request()->get('page_serials'),
        ])->links() }}
    </div>
</div>
