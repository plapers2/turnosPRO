<div>
    <section class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
            $meta = [
                [
                    'icon' => 'calendar_month',
                    'accent' => 'bg-primary',
                    'wrap' => 'bg-primary-container text-on-primary-container',
                ],
                [
                    'icon' => 'schedule',
                    'accent' => 'bg-[#0F6E56]',
                    'wrap' => 'bg-[#DDF4E8] text-[#0F6E56]',
                ],
                [
                    'icon' => 'cancel',
                    'accent' => 'bg-error',
                    'wrap' => 'bg-error-container/60 text-on-error-container',
                ],
                [
                    'icon' => 'assignment_turned_in',
                    'accent' => 'bg-blue-600',
                    'wrap' => 'bg-blue-200 text-blue-700',
                ],
            ];
        @endphp

        @foreach ($kpis as $i => $k)
            <div
                class="relative overflow-hidden flex items-center gap-3 rounded-xl shadow-sm bg-surface-container-lowest px-4 py-3 transition-all duration-200 hover:border-outline-variant hover:bg-surface-container-lowest">

                {{-- Accent lateral --}}
                <div class="absolute left-0 top-2 bottom-2 w-[3px] rounded-r-full {{ $meta[$i]['accent'] }}"></div>

                {{-- Ícono --}}
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] {{ $meta[$i]['wrap'] }}">
                    <span class="material-symbols-outlined text-[17px]">{{ $meta[$i]['icon'] }}</span>
                </div>

                {{-- Contenido --}}
                <div class="flex min-w-0 flex-col gap-0.5">
                    <span class="text-[17px] font-semibold leading-none text-on-surface">{{ $k['value'] }}</span>
                    <span class="text-[11px] font-normal text-on-surface-variant">{{ $k['label'] }}</span>
                </div>

            </div>
        @endforeach
    </section>
</div>
