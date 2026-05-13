{{-- resources/views/livewire/appointments/partials/stats-row.blade.php --}}
<div class="grid grid-cols-2 xl:grid-cols-5 gap-3 mb-5">

    @php
        $stats_cards = [
            [
                'label' => 'Total',
                'value' => $stats['total'],
                'icon' =>
                    '<rect x="1" y="3" width="14" height="11" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M5 3V2M11 3V2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M1 7h14" stroke="currentColor" stroke-width="1.5"/>',
                'bg' => 'bg-primary-container',
                'text' => 'text-on-primary-container',
                'num' => 'text-primary',
            ],
            [
                'label' => 'Pendientes',
                'value' => $stats['pending'],
                'icon' =>
                    '<circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5"/><path d="M8 5v3.5l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
                'bg' => 'bg-[#FAEEDA]',
                'text' => 'text-[#854F0B]',
                'num' => 'text-[#854F0B]',
            ],
            [
                'label' => 'Confirmadas',
                'value' => $stats['confirmed'],
                'icon' =>
                    '<path d="M2.5 8.5l3.5 3.5 7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
                'bg' => 'bg-[#E1F5EE]',
                'text' => 'text-[#0F6E56]',
                'num' => 'text-[#0F6E56]',
            ],
            [
                'label' => 'Completadas',
                'value' => $stats['completed'],
                'icon' =>
                    '<circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.3" stroke-dasharray="3 2"/><path d="M4.5 8.2l2.8 2.8 4.2-5.2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>',
                'bg' => 'bg-[#E6F1FB]',
                'text' => 'text-[#185FA5]',
                'num' => 'text-[#185FA5]',
            ],
            [
                'label' => 'Canceladas',
                'value' => $stats['cancelled'],
                'icon' =>
                    '<path d="M3 3l10 10M13 3L3 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
                'bg' => 'bg-[#FCEBEB]',
                'text' => 'text-[#A32D2D]',
                'num' => 'text-[#A32D2D]',
            ],
        ];
    @endphp

    @foreach ($stats_cards as $card)
        <div
            class="flex items-center gap-3 rounded-xl border border-outline-variant/40
                    bg-surface-container-lowest px-4 py-3
                    shadow-[0_1px_4px_rgba(95,94,90,0.05)]">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center
                        rounded-[10px] {{ $card['bg'] }} {{ $card['text'] }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    {!! $card['icon'] !!}
                </svg>
            </div>
            <div class="flex flex-col gap-0.5">
                <span class="text-[17px] font-semibold leading-none {{ $card['num'] }}">
                    {{ $card['value'] }}
                </span>
                <span class="text-[11px] font-normal text-on-surface-variant">
                    {{ $card['label'] }}
                </span>
            </div>
        </div>
    @endforeach

</div>
