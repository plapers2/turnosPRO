<div>
    {{-- LOADING BAR --}}
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- FILTROS --}}
    <div class="px-8 pt-4 pb-2 flex flex-col sm:flex-row gap-3">

        {{-- Estado --}}
        <div class="relative">
            <select wire:model.live="status"
                class="appearance-none pl-4 pr-9 py-2.5 rounded-xl border-[1.5px] border-outline-variant
                       bg-surface-container-lowest text-on-surface text-[13px] font-medium
                       focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10
                       transition-all cursor-pointer min-w-[160px]">
                <option value="">Todos los estados</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>
            <span
                class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant">
                expand_more
            </span>
        </div>

        {{-- Día --}}
        <div class="relative">
            <select wire:model.live="day"
                class="appearance-none pl-4 pr-9 py-2.5 rounded-xl border-[1.5px] border-outline-variant
                       bg-surface-container-lowest text-on-surface text-[13px] font-medium
                       focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10
                       transition-all cursor-pointer min-w-[160px]">
                <option value="">Todos los días</option>
                @foreach ($days as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <span
                class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[16px] text-on-surface-variant">
                expand_more
            </span>
        </div>

    </div>

    {{-- GRID SEMANAL --}}
    <div class="px-8 pb-20">
        <div
            class="bg-surface-container-lowest rounded-2xl border border-outline-variant/20
                    shadow-[0_2px_16px_rgba(95,94,90,0.05)] p-6">

            <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200"
                wire:target="status, day" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

                @foreach ($visibleDays as $key => $label)
                    <div class="bg-surface rounded-2xl border border-outline-variant/25 p-4 flex flex-col gap-3">

                        {{-- HEADER DÍA --}}
                        <div class="flex items-center justify-between">
                            <h3 class="text-[13.5px] font-semibold text-primary flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[15px] opacity-70">calendar_today</span>
                                {{ $label }}
                            </h3>
                            <span
                                class="text-[11px] font-semibold px-2.5 py-1 rounded-full
                                         bg-primary-fixed text-primary">
                                {{ count($profesionalAvailability[$key] ?? []) }} horarios
                            </span>
                        </div>

                        <div class="h-px bg-outline-variant/30"></div>

                        {{-- LISTA --}}
                        <div class="space-y-2.5">
                            @forelse ($profesionalAvailability[$key] ?? [] as $hour)
                                <div
                                    class="group bg-surface-container-lowest rounded-2xl border border-outline-variant/20
               p-4 flex flex-col gap-4
               hover:shadow-[0_6px_24px_rgba(95,94,90,0.10)]
               hover:border-primary/20
               hover:-translate-y-[1px]
               transition-all duration-300">

                                    {{-- HEADER PROFESIONAL --}}
                                    <div class="flex items-start gap-3">

                                        {{-- AVATAR --}}
                                        <div
                                            class="h-11 w-11 rounded-2xl bg-primary/10
                       flex items-center justify-center
                       text-primary font-semibold text-[14px]
                       shrink-0">

                                            {{ strtoupper(substr($hour->user->name ?? 'P', 0, 1)) }}

                                        </div>

                                        {{-- INFO --}}
                                        <div class="min-w-0 flex-1">

                                            <div class="flex items-center justify-between gap-2">

                                                <h4 class="text-[13.5px] font-semibold text-on-surface truncate">
                                                    {{ $hour->user->name }}
                                                </h4>

                                                <span
                                                    class="text-[10px] font-semibold px-2.5 py-1 rounded-full
                        {{ $hour->deleted_at ? 'bg-error-container text-on-error-container' : 'bg-emerald-100 text-emerald-700' }}">

                                                    {{ $hour->deleted_at ? 'Inactivo' : 'Activo' }}

                                                </span>

                                            </div>

                                            {{-- EMAIL / ROL --}}
                                            <div class="flex items-center gap-1 mt-1">

                                                <span
                                                    class="material-symbols-outlined text-[13px] text-on-surface-variant">
                                                    badge
                                                </span>

                                                <p class="text-[11.5px] text-on-surface-variant truncate">
                                                    Profesional
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

                                            <span class="material-symbols-outlined text-[18px] text-indigo-600">
                                                schedule
                                            </span>

                                        </div>

                                        <div class="flex-1">

                                            <div class="flex items-center gap-2 flex-wrap">

                                                <span class="text-[14px] font-semibold text-on-surface">
                                                    {{ \Carbon\Carbon::parse($hour->start_time)->format('h:i A') }}
                                                </span>

                                                <span class="text-on-surface-variant text-[12px]">
                                                    —
                                                </span>

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
