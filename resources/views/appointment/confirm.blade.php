<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <!-- HERO -->
        <div class="relative bg-surface px-8 py-8 border-b border-outline-variant/60">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary text-[28px]">event_available</span>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-semibold tracking-widest uppercase text-primary mb-1 font-label">Paso 3 de 3</p>
                    <h2 class="text-2xl font-bold text-on-surface font-headline tracking-tight">
                        Confirma tu cita en <span class="text-primary">{{ $company->name }}</span>
                    </h2>
                    <p class="text-xs text-on-surface-variant mt-1">
                        {{ $services->count() }} servicio(s) · {{ $totalDuration }} min · ${{ number_format($totalPrice, 0, ',', '.') }}
                    </p>
                </div>
                <a href="{{ route('appointments.selectServices', $company->id) }}"
                    class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-on-surface-variant hover:text-primary transition px-3 py-2 rounded-lg hover:bg-primary/5">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    Cambiar servicios
                </a>
            </div>
        </div>

        <!-- FORM -->
        <form method="POST" action="{{ route('appointments.store') }}" class="p-8 pb-24" id="continueForm">
            @csrf

            <input type="hidden" name="company_id" value="{{ $company->id }}" />
            <input type="hidden" name="end_time" id="end_time" />
            <input type="hidden" name="fecha" id="fecha" />
            <input type="hidden" name="hora" id="hora" />
            @foreach ($services as $service)
            <input type="hidden" name="services[]" value="{{ $service->id }}" />
            @endforeach
            <input type="hidden" name="total_duration" value="{{ $totalDuration }}" />

            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- COLUMNA PRINCIPAL -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- LEYENDA DE COLORES -->
                    <div class="flex flex-wrap gap-x-6 gap-y-3 mb-4 px-1">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:rgba(34,197,94,0.5); border: 1px solid rgba(34,197,94,0.3)"></span>
                            <span class="text-xs text-on-surface-variant">Disponible</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:rgba(249,115,22,0.5); border: 1px solid rgba(249,115,22,0.4)"></span>
                            <span class="text-xs text-on-surface-variant">Último slot</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:rgba(239,68,68,0.3); background-image:repeating-linear-gradient(45deg,transparent,transparent 2px,rgba(239,68,68,0.15) 2px,rgba(239,68,68,0.15) 4px)"></span>
                            <span class="text-xs text-on-surface-variant">No disponible</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background:rgba(234,179,8,0.3); background-image:repeating-linear-gradient(45deg,transparent,transparent 2px,rgba(234,179,8,0.15) 2px,rgba(234,179,8,0.15) 4px)"></span>
                            <span class="text-xs text-on-surface-variant">Horario insuficiente</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full flex-shrink-0 bg-gray-200"></span>
                            <span class="text-xs text-on-surface-variant">Fuera del horario</span>
                        </div>
                    </div>
                    <!-- CALENDARIO -->
                    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-bold text-on-surface font-headline">Fecha y hora</h3>
                                <p class="text-xs text-on-surface-variant mt-0.5">Haz clic en un horario disponible para seleccionarlo</p>
                            </div>
                            <!-- Slot seleccionado -->
                            <div id="slotSeleccionado" class="hidden items-center gap-2 px-3 py-2 rounded-lg bg-primary/5 border border-primary/20">
                                <span class="material-symbols-outlined text-primary text-[16px]">schedule</span>
                                <div class="text-xs">
                                    <span class="font-bold text-primary" id="slotFechaTexto"></span>
                                    <span class="text-on-surface-variant ml-1" id="slotHoraTexto"></span>
                                    <span class="text-on-surface-variant"> → </span>
                                    <span class="font-semibold text-primary" id="slotFinTexto"></span>
                                </div>
                            </div>
                        </div>
                        <div id="calendarLoading" class="flex flex-col items-center justify-center py-16">
                            <div class="animate-spin w-8 h-8 border-2 border-primary border-t-transparent rounded-full mb-3"></div>
                            <p class="text-xs text-on-surface-variant">Cargando disponibilidad...</p>
                        </div>
                        <!-- FullCalendar container -->
                        <div id="calendar"
                            data-duration="{{ $totalDuration }}"
                            data-company="{{ $company->id }}"
                            data-services="{{ $services->pluck('id')->join(',') }}"
                            data-services-json="{{ $services->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'duration' => $s->duration])->toJson() }}">
                        </div>
                    </div>

                    <!-- PROFESIONAL -->
                    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-on-surface font-headline">Profesionales</h3>
                            <p class="text-xs text-on-surface-variant mt-0.5">Elige un profesional por cada servicio — selecciona primero un horario</p>
                        </div>

                        <div id="profesionalPlaceholder" class="flex flex-col items-center justify-center py-8 text-center">
                            <span class="material-symbols-outlined text-on-surface-variant/40 text-4xl mb-2">person_search</span>
                            <p class="text-xs text-on-surface-variant">Selecciona un horario en el calendario para ver los profesionales disponibles</p>
                        </div>

                        <div id="profesionalLoading" class="hidden flex-col items-center justify-center py-8">
                            <div class="animate-spin w-6 h-6 border-2 border-primary border-t-transparent rounded-full mb-2"></div>
                            <p class="text-xs text-on-surface-variant">Buscando disponibilidad...</p>
                        </div>

                        <div id="sinDisponibilidad" class="hidden flex-col items-center justify-center py-8 text-center">
                            <span class="material-symbols-outlined text-error/50 text-4xl mb-2">event_busy</span>
                            <p class="text-sm font-semibold text-on-surface mb-1">Sin disponibilidad</p>
                            <p class="text-xs text-on-surface-variant">No hay combinación de profesionales disponibles en este horario. Intenta otro.</p>
                        </div>

                        <!-- Una sección por servicio, generada dinámicamente por JS -->
                        <div id="serviciosProfesionales" class="hidden space-y-6"></div>
                    </div>

                    <!-- Hidden inputs para profesionales (uno por servicio, generados por JS) -->
                    <div id="profesionalesHiddenInputs"></div>

                    <!-- NOTAS -->
                    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-on-surface font-headline">Notas adicionales</h3>
                            <p class="text-xs text-on-surface-variant mt-0.5">Opcional — preferencias o indicaciones especiales</p>
                        </div>
                        <textarea
                            name="notas"
                            rows="3"
                            placeholder="Ej. Tengo alergia a ciertos productos, prefiero el lado izquierdo..."
                            class="w-full px-3 py-2.5 rounded-lg bg-surface-container border border-outline-variant/30
                                text-sm text-on-surface placeholder:text-on-surface-variant/50
                                focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition resize-none">{{ old('notas') }}</textarea>
                    </div>
                </div>

                <!-- COLUMNA RESUMEN -->
                <div class="space-y-5">
                    <div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant/20 shadow-sm space-y-4 sticky top-4">
                        <h3 class="text-sm font-bold text-on-surface font-headline">Resumen de tu cita</h3>

                        <div class="flex items-center gap-3 pb-4 border-b border-outline-variant/20">
                            <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-primary/10 flex items-center justify-center">
                                @if ($company->logo)
                                <img src="{{ $company->logo ? asset('storage/' . $company->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($company->name) }}" alt="{{ $company->name }}" class="w-full h-full object-cover" />
                                @else
                                <span class="text-sm font-bold text-primary/50">{{ strtoupper(substr($company->name, 0, 2)) }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-on-surface">{{ $company->name }}</p>
                                <p class="text-xs text-on-surface-variant line-clamp-1">{{ $company->address }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-xs font-semibold text-on-surface-variant font-label uppercase tracking-wide">Servicios</p>
                            @foreach ($services as $service)
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs text-on-surface line-clamp-1 flex-1">{{ $service->name }}</span>
                                <span class="text-xs font-semibold text-primary flex-shrink-0">${{ number_format($service->price, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>

                        <div class="pt-3 border-t border-outline-variant/20 space-y-2">
                            <div class="flex items-center justify-between text-xs text-on-surface-variant">
                                <span>Duración total</span>
                                <span class="font-semibold">{{ $totalDuration }} min</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-on-surface">Total</span>
                                <span class="text-base font-bold text-primary">${{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Resumen slot seleccionado -->
                        <div id="resumenSlot" class="hidden pt-3 border-t border-outline-variant/20 space-y-1">
                            <p class="text-xs font-semibold text-on-surface-variant font-label uppercase tracking-wide">Horario</p>
                            <p class="text-xs text-on-surface" id="resumenFecha"></p>
                            <p class="text-xs text-primary font-semibold" id="resumenHora"></p>
                        </div>

                        <button type="submit" id="btnConfirmar" disabled
                            class="w-full py-3 rounded-lg text-sm font-semibold text-white transition shadow-sm bg-primary/40 cursor-not-allowed">
                            <span class="flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">event_available</span>
                                Confirmar cita
                            </span>
                        </button>

                        <p class="text-[10px] text-on-surface-variant text-center leading-relaxed">
                            Al confirmar aceptas los términos del servicio. Recibirás un correo de confirmación.
                        </p>
                    </div>
                </div>
            </div>
        </form>
        @if (session('error'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3
               bg-red-600 text-white text-sm font-medium px-5 py-3.5 rounded-2xl
               shadow-xl max-w-sm w-full mx-4">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0">error</span>
            <span class="flex-1">{{ session('error') }}</span>
            <button @click="show = false" class="ml-2 opacity-70 hover:opacity-100 transition">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        @endif
    </main>
</x-app-layout>

<style>
    /* Personalización del calendario para que combine con el diseño */
    #calendar .fc-toolbar-title {
        font-size: 1rem;
        font-weight: 700;
    }

    #calendar .fc-button {
        font-size: 0.75rem;
        padding: 0.3rem 0.75rem;
    }

    #calendar .fc-timegrid-slot {
        height: 2.5rem;
    }

    #calendar .fc-timegrid-slot-label {
        font-size: 0.7rem;
    }

    #calendar .fc-col-header-cell-cushion {
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Slot seleccionado */
    .fc-slot-selected {
        background: rgba(var(--primary-rgb, 99, 91, 255), 0.15) !important;
    }

    /* Evento temporal de la cita */
    .fc-event.cita-preview {
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        border: none;
    }

    /* Botones prev/next */
    .fc .fc-prev-button,
    .fc .fc-next-button {
        background-color: transparent !important;
        border: 1px solid rgba(107, 58, 31, 0.3) !important;
        color: #6b3a1f !important;
        border-radius: 8px !important;
        padding: 4px 10px !important;
        transition: all 0.15s !important;
    }

    .fc .fc-prev-button:hover,
    .fc .fc-next-button:hover {
        background-color: rgba(107, 58, 31, 0.08) !important;
        border-color: rgba(107, 58, 31, 0.5) !important;
    }

    /* Botón Hoy */
    .fc .fc-today-button {
        background-color: transparent !important;
        border: 1px solid rgba(107, 58, 31, 0.3) !important;
        color: #6b3a1f !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 0.75rem !important;
        transition: all 0.15s !important;
    }

    .fc .fc-today-button:hover:not(:disabled) {
        background-color: rgba(107, 58, 31, 0.08) !important;
    }

    .fc .fc-today-button:disabled {
        opacity: 0.4 !important;
    }

    /* Botones Semana/Día */
    .fc .fc-timeGridWeek-button,
    .fc .fc-timeGridDay-button {
        background-color: transparent !important;
        border: 1px solid rgba(107, 58, 31, 0.3) !important;
        color: #6b3a1f !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 0.75rem !important;
        transition: all 0.15s !important;
    }

    /* Botón activo Semana/Día */
    .fc .fc-timeGridWeek-button:hover,
    .fc .fc-timeGridDay-button:hover {
        background-color: rgba(107, 58, 31, 0.08) !important;
        border-color: rgba(107, 58, 31, 0.6) !important;
        color: #6b3a1f !important;
    }

    .fc .fc-timeGridWeek-button:hover,
    .fc .fc-timeGridDay-button:hover {
        background-color: rgba(107, 58, 31, 0.08) !important;
    }

    /* Título del calendario */
    .fc .fc-toolbar-title {
        font-size: 1rem !important;
        font-weight: 700 !important;
        color: #1c1208 !important;
    }

    .fc .fc-col-header-cell {
        background-color: rgba(107, 58, 31, 0.04) !important;
        border-color: rgba(107, 58, 31, 0.1) !important;
    }

    .fc .fc-col-header-cell-cushion {
        color: #6b3a1f !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        padding: 8px 0 !important;
        text-decoration: none !important;
    }

    /* Línea del indicador de hora actual */
    .fc-timegrid-now-indicator-line {
        border-color: #6b3a1f !important;
        border-width: 2px !important;
    }

    /* Flecha del indicador */
    .fc-timegrid-now-indicator-arrow {
        border-top-color: #6b3a1f !important;
        border-bottom-color: transparent !important;
        border-left-color: transparent !important;
    }

    /* Día actual */
    .fc-day-today .fc-timegrid-col-frame {
        background-color: rgba(107, 58, 31, 0.03) !important;
    }

    .fc .fc-timegrid-slot {
        border-color: rgba(107, 58, 31, 0.06) !important;
    }

    .fc .fc-timegrid-slot-label {
        font-size: 0.7rem !important;
        color: rgba(107, 58, 31, 0.5) !important;
        font-weight: 500 !important;
    }

    .fc td,
    .fc th {
        border-color: rgba(107, 58, 31, 0.08) !important;
    }

    .fc .fc-button:focus,
    .fc .fc-button:focus-visible {
        outline: none !important;
        box-shadow: 0 0 0 2px rgba(107, 58, 31, 0.3) !important;
    }
</style>
<script>
    document.getElementById('continueForm').addEventListener('submit', function(e) {
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = `
        <span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
        Confirmando...
    `;
    });
</script>