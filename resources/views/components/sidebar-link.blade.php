{{-- resources/views/components/sidebar-link.blade.php --}}
@props(['route', 'pattern', 'icon'])

@php
    $isActive = request()->routeIs($pattern);
@endphp

<a href="{{ route($route) }}"
    class="group flex items-center gap-2.5 px-2.5 py-2 rounded-xl transition-all duration-150 relative overflow-hidden"
    style="{{ $isActive
        ? 'background: linear-gradient(90deg, rgba(133,79,11,0.13) 0%, rgba(133,79,11,0.06) 100%);'
        : 'background: transparent;' }}">

    {{-- Indicador lateral activo --}}
    @if ($isActive)
        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] rounded-r-full"
            style="height: 55%; background: #854f0b;"></div>
    @endif

    {{-- Ícono --}}
    <div class="flex items-center justify-center w-[30px] h-[30px] rounded-lg shrink-0 transition-all duration-150"
        style="{{ $isActive ? 'background: linear-gradient(135deg, #854f0b, #663a00);' : 'background: transparent;' }}
             {{ !$isActive ? '' : '' }}">
        <span class="material-symbols-rounded text-[17px] leading-none transition-colors duration-150"
            style="{{ $isActive ? 'color: #ffca99;' : 'color: #847467;' }}">
            {{ $icon }}
        </span>
    </div>

    {{-- Texto --}}
    <span class="text-[13px] transition-colors duration-150 flex-1"
        style="{{ $isActive ? 'color: #854f0b; font-weight: 600;' : 'color: #524438; font-weight: 500;' }}">
        {{ $slot }}
    </span>
</a>

<style>
    a.group:not([style*="linear-gradient"]):hover {
        background: rgba(102, 58, 0, 0.06) !important;
    }

    a.group:not([style*="linear-gradient"]):hover span.material-symbols-rounded {
        color: #663a00 !important;
    }

    a.group:not([style*="linear-gradient"]):hover>div:first-of-type {
        background: rgba(102, 58, 0, 0.08) !important;
    }
</style>
