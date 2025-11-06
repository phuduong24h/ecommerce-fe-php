{{-- resources/views/components/dashboard-card.blade.php --}}
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div class="flex items-center justify-between mb-4">
        {{-- Icon --}}
        @if($icon ?? false)
            <div class="text-3xl">{{ $icon }}</div>
        @else
            <div class="w-10 h-10"></div>
        @endif

        {{-- Value + Change --}}
        <div class="text-right">
            <p class="text-2xl font-bold text-gray-800">
                {{ $value ?? '0' }}  {{-- DEFAULT NẾU LỖI --}}
            </p>
            @if($change ?? false)
                <p class="text-sm {{ str_starts_with($change, '+') ? 'text-green-600' : 'text-red-600' }}">
                    {{ $change }}
                </p>
            @endif
        </div>
    </div>

    <div class="flex justify-between items-center">
        <h3 class="text-sm font-medium text-gray-600">{{ $title ?? 'No Title' }}</h3>
        {{ $slot }}
    </div>
</div>