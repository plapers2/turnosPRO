@if (auth()->user()->hasRole('admin') && $showPendingBanner && $appointmentsForConfirmed->isNotEmpty())
    <div id="banner-citas" class="bg-red-50 border-l-4 border-red-500 rounded-r-xl p-4 mb-6" role="alert">

        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-red-700" style="font-size:20px">error</span>
                <span class="text-sm font-medium text-red-900">Citas confirmadas sin completar</span>
                <span class="bg-red-500 text-red-50 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    {{ $appointmentsForConfirmed->count() }}
                </span>
            </div>
            <button wire:click="dismissBanner" class="text-red-600 hover:text-red-800 transition-colors"
                aria-label="Cerrar alerta">
                <span class="material-symbols-outlined" style="font-size:18px">close</span>
            </button>
        </div>

        {{-- Tarjetas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach ($appointmentsForConfirmed as $appt)
                @php
                    $initials = strtoupper(
                        substr($appt->customer->name, 0, 1) .
                            (str_contains($appt->customer->name, ' ')
                                ? substr(strrchr($appt->customer->name, ' '), 1, 1)
                                : ''),
                    );
                @endphp

                <div class="bg-white border border-red-200 rounded-xl p-3">

                    {{-- Cliente --}}
                    <div class="flex items-center gap-2 pb-2 mb-3 border-b border-red-100">
                        <div
                            class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center
                                    text-xs font-medium text-red-700 shrink-0">
                            {{ $initials }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-red-900 truncate">{{ $appt->customer->name }}</p>
                            <p class="text-xs text-red-500">Cliente</p>
                        </div>
                    </div>

                    {{-- Detalles --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-1.5 text-xs text-red-800">
                            <span class="material-symbols-outlined text-red-500 text-sm">person</span>
                            <span class="truncate">{{ $appt->user->name }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-red-800">
                            <span class="material-symbols-outlined text-red-500 text-sm">phone</span>
                            <span>{{ $appt->user->phone }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-red-800">
                            <span class="material-symbols-outlined text-red-500 text-sm">calendar_today</span>
                            <span>{{ \Carbon\Carbon::parse($appt->start_time)->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-red-800">
                            <span class="material-symbols-outlined text-red-500 text-sm">schedule</span>
                            <span>
                                {{ \Carbon\Carbon::parse($appt->start_time)->format('H:i') }} —
                                {{ \Carbon\Carbon::parse($appt->end_time)->format('H:i') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-red-800">
                            <span class="material-symbols-outlined text-red-500 text-sm">design_services</span>
                            @foreach ($appt->services as $servicio)
                                <span>{{ $servicio->name }}</span>
                            @endforeach

                        </div>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
@endif
