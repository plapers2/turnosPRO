<div>
    {{-- LOADING BAR --}}
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- FILTROS --}}
    <div class="flex flex-col gap-3 px-4 pt-3 sm:px-8 lg:flex-row lg:items-center mb-3">
        @role('admin')
            <div class="relative w-full lg:max-w-[280px]">
                <span
                    class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2
                     -translate-y-1/2 text-[17px] text-on-surface-variant">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por usuario o email..."
                    class="w-full rounded-xl border border-outline-variant/60 bg-surface-container-lowest
                       py-2.5 pl-9 pr-4 text-[13px] text-on-surface placeholder:text-on-surface-variant/50
                       focus:outline-none focus:ring-2 focus:ring-primary/30 transition-shadow" />
            </div>
        @endrole

        <div class="relative w-full lg:max-w-[200px]">
            <span
                class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2
                     -translate-y-1/2 text-[17px] text-on-surface-variant">calendar_month</span>
            <select wire:model.live="day"
                class="w-full appearance-none rounded-xl border border-outline-variant/60
                       bg-surface-container-lowest py-2.5 pl-9 pr-8
                       text-[13px] text-on-surface
                       focus:outline-none focus:ring-2 focus:ring-primary/30 transition-shadow cursor-pointer">
                <option value="">Todos los días</option>
                @foreach ($days as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <span
                class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2
                     -translate-y-1/2 text-[17px] text-on-surface-variant">expand_more</span>
        </div>

        <div
            class="flex gap-1 rounded-xl border border-outline-variant/60
                bg-surface-container-lowest p-1 shrink-0 self-start lg:self-auto">
            <button wire:click="$set('status', '')"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5
                       text-[12.5px] font-semibold transition-all duration-150
                       {{ $status === '' ? 'bg-on-primary-fixed-variant text-primary-fixed shadow-sm' : 'text-on-surface-variant hover:bg-surface-container' }}">
                Todos
            </button>
            <button wire:click="$set('status', 'active')"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5
                       text-[12.5px] font-semibold transition-all duration-150
                       {{ $status === 'active' ? 'bg-on-primary-fixed-variant text-primary-fixed shadow-sm' : 'text-on-surface-variant hover:bg-surface-container' }}">
                Activos
            </button>
            <button wire:click="$set('status', 'inactive')"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5
                       text-[12.5px] font-semibold transition-all duration-150
                       {{ $status === 'inactive' ? 'bg-on-primary-fixed-variant text-primary-fixed shadow-sm' : 'text-on-surface-variant hover:bg-surface-container' }}">
                Inactivos
            </button>
        </div>
    </div>

    {{-- GRID SEMANAL --}}
    <div class="px-8 pb-20">
        <div
            class="bg-surface-container-lowest rounded-2xl border border-outline-variant/20
                shadow-[0_2px_16px_rgba(95,94,90,0.05)] p-6">

            <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200"
                wire:target="status, day, search"
                class="{{ count($visibleDays) === 1 ? 'grid grid-cols-1 gap-4' : 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4' }}">

                @foreach ($visibleDays as $key => $label)
                    <div class="bg-surface rounded-2xl border border-outline-variant/25 p-4 flex flex-col gap-3">

                        {{-- HEADER DÍA --}}
                        <div class="flex items-center justify-between">
                            <h3 class="text-[13.5px] font-semibold text-primary flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[15px] opacity-70">calendar_today</span>
                                {{ $label }}
                            </h3>
                            <span
                                class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-primary-fixed text-primary">
                                {{ count($profesionalAvailability[$key] ?? []) }} horarios
                            </span>
                        </div>

                        <div class="h-px bg-outline-variant/30"></div>

                        {{-- LISTA: en un solo día se distribuye en columnas, sino apilado --}}
                        <div
                            class="{{ count($visibleDays) === 1 ? 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4' : 'space-y-2.5' }}">

                            @forelse ($profesionalAvailability[$key] ?? [] as $hour)
                                {{-- CARD: siempre igual, nunca cambia su layout interno --}}
                                <div
                                    class="group bg-surface-container-lowest rounded-2xl border border-outline-variant/20
                                    p-4 flex flex-col gap-4
                                    hover:shadow-[0_6px_24px_rgba(95,94,90,0.10)]
                                    hover:border-primary/20
                                    hover:-translate-y-[1px]
                                    transition-all duration-300">

                                    {{-- HEADER PROFESIONAL --}}
                                    <div
                                        class="flex items-start gap-3
                                        {{ $hour->deleted_at ? 'opacity-85' : '' }}">

                                        {{-- AVATAR --}}
                                        <div
                                            class="h-11 w-11 rounded-2xl bg-primary/10
                                            flex items-center justify-center overflow-hidden shrink-0">
                                            @if ($hour->user->image)
                                                <img src="{{ asset('storage/' . $hour->user->image) }}"
                                                    alt="{{ $hour->user->name }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-primary font-semibold text-[14px]">
                                                    {{ strtoupper(substr($hour->user->name ?? 'P', 0, 1)) }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- INFO --}}
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <h4 class="text-md font-semibold text-on-surface truncate">
                                                    {{ $hour->user->name }}
                                                    <span
                                                        class="block text-sm text-primary-container">{{ $hour->user->email }}</span>
                                                </h4>
                                                <span
                                                    class="text-[10px] font-semibold px-2.5 py-1 rounded-full shrink-0
                                                    {{ $hour->deleted_at ? 'bg-error-container text-on-error-container' : 'bg-emerald-100 text-emerald-700' }}">
                                                    {{ $hour->deleted_at ? 'Inactivo' : 'Activo' }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1 mt-1">
                                                <span
                                                    class="material-symbols-outlined text-[13px] text-on-surface-variant">badge</span>
                                                <p class="text-[11.5px] text-on-surface-variant truncate">Profesional
                                                </p>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- HORARIO --}}
                                    <div
                                        class="bg-surface rounded-xl border border-outline-variant/15
                                        px-3.5 py-3 flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 rounded-xl bg-indigo-50
                                            flex items-center justify-center shrink-0">
                                            <span
                                                class="material-symbols-outlined text-[18px] text-indigo-600">schedule</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-[14px] font-semibold text-on-surface">
                                                    {{ \Carbon\Carbon::parse($hour->start_time)->format('h:i A') }}
                                                </span>
                                                <span class="text-on-surface-variant text-[12px]">—</span>
                                                <span class="text-[14px] font-semibold text-on-surface">
                                                    {{ \Carbon\Carbon::parse($hour->end_time)->format('h:i A') }}
                                                </span>
                                            </div>
                                            <p class="text-[11.5px] text-on-surface-variant mt-0.5">
                                                Duración:
                                                {{ \Carbon\Carbon::parse($hour->start_time)->diffInMinutes(\Carbon\Carbon::parse($hour->end_time)) }}
                                                minutos
                                            </p>
                                        </div>
                                    </div>

                                </div>

                            @empty
                                <div class="flex flex-col items-center justify-center py-6 gap-2 text-center">
                                    <span
                                        class="material-symbols-outlined text-[24px] text-on-surface-variant/40">schedule_off</span>
                                    <p class="text-[12px] text-on-surface-variant italic">Sin horarios registrados</p>
                                </div>
                            @endforelse

                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
