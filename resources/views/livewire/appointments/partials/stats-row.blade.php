{{-- resources/views/livewire/appointments/partials/stats-row.blade.php --}}
{{-- Props: $stats = ['total', 'pending', 'confirmed', 'cancelled'] --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-6">

    {{-- Total --}}
    <div
        class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm p-4 flex flex-col gap-1.5">
        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center mb-1">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="text-blue-600">
                <rect x="1" y="3" width="14" height="11" rx="2" stroke="currentColor" stroke-width="1.5" />
                <path d="M5 3V2M11 3V2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                <path d="M1 7h14" stroke="currentColor" stroke-width="1.5" />
            </svg>
        </div>
        <span class="text-xs text-on-surface-variant">Total</span>
        <span class="text-2xl font-semibold text-blue-700 leading-none">{{ $stats['total'] }}</span>
    </div>

    {{-- Pendientes --}}
    <div
        class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm p-4 flex flex-col gap-1.5">
        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center mb-1">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="text-amber-700">
                <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                <path d="M8 5v3.5l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </div>
        <span class="text-xs text-on-surface-variant">Pendientes</span>
        <span class="text-2xl font-semibold text-amber-800 leading-none">{{ $stats['pending'] }}</span>
    </div>

    {{-- Confirmadas --}}
    <div
        class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm p-4 flex flex-col gap-1.5">
        <div class="w-8 h-8 rounded-lg bg-[#E1F5EE] flex items-center justify-center mb-1">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="text-[#0F6E56]">
                <path d="M2.5 8.5l3.5 3.5 7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </div>
        <span class="text-xs text-on-surface-variant">Confirmadas</span>
        <span class="text-2xl font-semibold text-[#0F6E56] leading-none">{{ $stats['confirmed'] }}</span>
    </div>

    {{-- Canceladas --}}
    <div
        class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm p-4 flex flex-col gap-1.5">
        <div class="w-8 h-8 rounded-lg bg-[#FCEBEB] flex items-center justify-center mb-1">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="text-[#A32D2D]">
                <path d="M3 3l10 10M13 3L3 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
        </div>
        <span class="text-xs text-on-surface-variant">Canceladas</span>
        <span class="text-2xl font-semibold text-[#A32D2D] leading-none">{{ $stats['cancelled'] }}</span>
    </div>

</div>
