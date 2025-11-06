@props(['href', 'icon', 'label', 'active' => false])

@php
    $isCollapsed = !($sidebarOpen ?? true);
@endphp

<a
    href="{{ $href }}"
    class="group flex items-center gap-3 rounded-md px-2 py-2 text-sm font-medium transition-colors relative"
    :class="{
        'bg-sidebar-accent text-sidebar-accent-foreground': {{ $active ? 'true' : 'false' }},
        'hover:bg-sidebar-accent hover:text-sidebar-accent-foreground': !{{ $active ? 'true' : 'false' }},
        'justify-center': {{ $isCollapsed ? 'true' : 'false' }}
    }"
    x-data
>
    <i class="{{ $icon }} text-base"></i>
    <span x-show="!{{ $isCollapsed ? 'true' : 'false' }}">{{ $label }}</span>

    <!-- Tooltip -->
    <div
        x-show="{{ $isCollapsed ? 'true' : 'false' }}"
        class="absolute left-full ml-2 px-2 py-1 bg-sidebar-accent text-sidebar-accent-foreground text-xs rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50"
    >
        {{ $label }}
    </div>
</a>