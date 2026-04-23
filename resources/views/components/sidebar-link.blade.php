@props([
    'route' => null,
    'pattern' => null,
    'icon' => null,
])

@php
    $isActive = request()->routeIs($pattern);

    $base = "flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-300 text-sm tracking-wide Inter";

    $active = "bg-white text-[#854F0B] font-semibold shadow-sm";

    $inactive = "text-stone-600 hover:bg-[#fcf9f3] hover:translate-x-1";
@endphp

<a href="{{ route($route) }}"
   class="{{ $base }} {{ $isActive ? $active : $inactive }}">

    <span class="material-symbols-outlined"
        style="font-variation-settings: 'FILL' {{ $isActive ? 1 : 0 }};">
        {{ $icon }}
    </span>

    {{ $slot }}
</a>
