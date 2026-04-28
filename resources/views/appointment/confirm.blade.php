<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <header
            class="relative bg-[#fcf9f3]/80 backdrop-blur-md border border-outline-variant/20
           rounded-xl mx-8 mt-10 mb-4 px-6 py-5 flex flex-col lg:flex-row
           items-start lg:items-center justify-between gap-4 shadow-[0_8px_20px_rgba(95,94,90,0.04)]">

            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">event_available</span>
                    </div>

                    <h2 class="text-xl font-bold text-primary tracking-tight">
                        Confirmar Cita
                    </h2>
                </div>

                <p class="text-sm text-on-surface-variant ml-13">
                    Elige fecha, hora y profesional para tu cita
                </p>
            </div>
        </header>

        <!-- HERO -->
        <div class="relative bg-gradient-to-br from-primary/10 via-surface to-secondary/10 px-8 py-8 border-b border-outline-variant/20">
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
            <div class="absolute right-6 top-2 opacity-5 pointer-events-none select-none">
                <span class="material-symbols-outlined" style="font-size:130px">calendar_month</span>
            </div>
        </div>

        <!-- FORM -->
        <form method="POST" action="{{ route('appointments.store') }}" class="p-8 pb-24">
            @csrf

            <!-- Campos ocultos -->
            <input type="hidden" name="company_id" value="{{ $company->id }}" />
            <input type="hidden" name="end_time" id="end_time" />
            @foreach ($services as $service)
            <input type="hidden" name="services[]" value="{{ $service->id }}" />
            @endforeach
            <input type="hidden" name="total_duration" value="{{ $totalDuration }}" />

            <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- COLUMNA PRINCIPAL -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- FECHA Y HORA -->
                    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-5">
                        <div>
                            <h3 class="text-base font-bold text-on-surface font-headline">Fecha y hora</h3>
                            <p class="text-xs text-on-surface-variant mt-0.5">Selecciona cuándo quieres tu cita</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Fecha -->
                            <div class="flex flex-col gap-1.5">
                                <label for="fecha" class="text-xs font-semibold text-on-surface-variant font-label">Fecha</label>
                                <input
                                    type="date"
                                    id="fecha"
                                    name="fecha"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    class="w-full px-3 py-2.5 rounded-lg bg-surface-container border border-outline-variant/30
                                        text-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition"
                                    required />
                                @error('fecha') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Hora -->
                            <div class="flex flex-col gap-1.5">
                                <label for="hora" class="text-xs font-semibold text-on-surface-variant font-label">Hora de inicio</label>
                                <input
                                    type="time"
                                    id="hora"
                                    name="hora"
                                    class="w-full px-3 py-2.5 rounded-lg bg-surface-container border border-outline-variant/30
                                        text-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition"
                                    required />
                                @error('hora') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Hora fin calculada -->
                        <div id="horaFinPreview" class="hidden items-center gap-2 px-4 py-2.5 rounded-lg bg-primary/5 border border-primary/20">
                            <span class="material-symbols-outlined text-primary text-[16px]">schedule</span>
                            <span class="text-xs text-on-surface-variant">Tu cita terminaría aproximadamente a las</span>
                            <span id="horaFinTexto" class="text-xs font-bold text-primary"></span>
                        </div>
                    </div>

                    <!-- PROFESIONAL -->
                    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-on-surface font-headline">Profesional</h3>
                            <p class="text-xs text-on-surface-variant mt-0.5">Elige quién te atenderá — selecciona primero la fecha y hora</p>
                        </div>

                        <!-- Estado inicial -->
                        <div id="profesionalPlaceholder" class="flex flex-col items-center justify-center py-8 text-center">
                            <span class="material-symbols-outlined text-on-surface-variant/40 text-4xl mb-2">person_search</span>
                            <p class="text-xs text-on-surface-variant">Selecciona una fecha y hora para ver los profesionales disponibles</p>
                        </div>

                        <!-- Loading -->
                        <div id="profesionalLoading" class="hidden flex-col items-center justify-center py-8">
                            <div class="animate-spin w-6 h-6 border-2 border-primary border-t-transparent rounded-full mb-2"></div>
                            <p class="text-xs text-on-surface-variant">Buscando disponibilidad...</p>
                        </div>

                        <!-- Grid profesionales -->
                        <div id="profesionalesGrid" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                        </div>

                        <!-- Sin disponibilidad -->
                        <div id="sinDisponibilidad" class="hidden flex-col items-center justify-center py-8 text-center">
                            <span class="material-symbols-outlined text-error/50 text-4xl mb-2">event_busy</span>
                            <p class="text-sm font-semibold text-on-surface mb-1">Sin disponibilidad</p>
                            <p class="text-xs text-on-surface-variant">No hay profesionales disponibles en este horario. Intenta otra fecha u hora.</p>
                        </div>

                        <input type="hidden" name="user_id" id="user_id" required />
                        @error('user_id') <p class="text-xs text-error">{{ $message }}</p> @enderror
                    </div>

                    <!-- NOTAS -->
                    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-on-surface font-headline">Notas adicionales</h3>
                            <p class="text-xs text-on-surface-variant mt-0.5">Opcional — indica alguna preferencia o indicación especial</p>
                        </div>
                        <textarea
                            name="notas"
                            id="notas"
                            rows="3"
                            placeholder="Ej. Tengo alergia a ciertos productos, prefiero el lado izquierdo..."
                            class="w-full px-3 py-2.5 rounded-lg bg-surface-container border border-outline-variant/30
                                text-sm text-on-surface placeholder:text-on-surface-variant/50
                                focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition resize-none">{{ old('notas') }}</textarea>
                    </div>

                </div>

                <!-- COLUMNA RESUMEN -->
                <div class="space-y-5">

                    <!-- Resumen empresa -->
                    <div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant/20 shadow-sm space-y-4 sticky top-4">
                        <h3 class="text-sm font-bold text-on-surface font-headline">Resumen de tu cita</h3>

                        <!-- Empresa -->
                        <div class="flex items-center gap-3 pb-4 border-b border-outline-variant/20">
                            <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-primary/10 flex items-center justify-center">
                                @if ($company->logo)
                                <img src="{{ $company->logo }}" alt="{{ $company->name }}" class="w-full h-full object-cover" />
                                @else
                                <span class="text-sm font-bold text-primary/50">{{ strtoupper(substr($company->name, 0, 2)) }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-on-surface">{{ $company->name }}</p>
                                <p class="text-xs text-on-surface-variant line-clamp-1">{{ $company->address }}</p>
                            </div>
                        </div>

                        <!-- Servicios -->
                        <div class="space-y-2">
                            <p class="text-xs font-semibold text-on-surface-variant font-label uppercase tracking-wide">Servicios</p>
                            @foreach ($services as $service)
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs text-on-surface line-clamp-1 flex-1">{{ $service->name }}</span>
                                <span class="text-xs font-semibold text-primary flex-shrink-0">${{ number_format($service->price, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>

                        <!-- Totales -->
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

                        <!-- Botón confirmar -->
                        <button type="submit" id="btnConfirmar" disabled
                            class="w-full py-3 rounded-lg text-sm font-semibold text-white transition shadow-sm
                            bg-primary/40 cursor-not-allowed"
                            data-active-class="bg-primary hover:bg-primary/90 cursor-pointer shadow-md"
                            data-inactive-class="bg-primary/40 cursor-not-allowed">
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

    </main>
</x-app-layout>

<script>
    const fechaInput = document.getElementById('fecha');
    const horaInput = document.getElementById('hora');
    const totalDuration = parseInt("{{ $totalDuration }}");
    const companyId = parseInt("{{ $company->id }}");
    const csrfToken = '{{ csrf_token() }}';

    // ─── Calcular hora fin ───────────────────────────────────────────────
    function calcularHoraFin() {
        const fecha = fechaInput.value;
        const hora = horaInput.value;
        if (!fecha || !hora) return;

        const inicio = new Date(`${fecha}T${hora}:00`);
        const fin = new Date(inicio.getTime() + totalDuration * 60000);

        // Mostrar preview
        const hh = String(fin.getHours()).padStart(2, '0');
        const mm = String(fin.getMinutes()).padStart(2, '0');
        document.getElementById('horaFinTexto').textContent = `${hh}:${mm}`;
        document.getElementById('horaFinPreview').classList.remove('hidden');
        document.getElementById('horaFinPreview').classList.add('flex');

        // Guardar en campo oculto (datetime-local format)
        const yyyy = fin.getFullYear();
        const mo = String(fin.getMonth() + 1).padStart(2, '0');
        const dd = String(fin.getDate()).padStart(2, '0');
        document.getElementById('end_time').value = `${yyyy}-${mo}-${dd} ${hh}:${mm}:00`;

        // Buscar profesionales disponibles
        buscarProfesionales(fecha, hora);
    }

    fechaInput.addEventListener('change', calcularHoraFin);
    horaInput.addEventListener('change', calcularHoraFin);

    // ─── Buscar profesionales disponibles ───────────────────────────────
    async function buscarProfesionales(fecha, hora) {
        document.getElementById('profesionalPlaceholder').classList.add('hidden');
        document.getElementById('sinDisponibilidad').classList.add('hidden');
        document.getElementById('profesionalesGrid').classList.add('hidden');
        document.getElementById('profesionalLoading').classList.remove('hidden');
        document.getElementById('profesionalLoading').classList.add('flex');

        try {
            const res = await fetch(`/booking/profesionales-disponibles?company_id=${companyId}&fecha=${fecha}&hora=${hora}&duration=${totalDuration}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await res.json();

            document.getElementById('profesionalLoading').classList.add('hidden');
            document.getElementById('profesionalLoading').classList.remove('flex');

            if (!data.profesionales || data.profesionales.length === 0) {
                document.getElementById('sinDisponibilidad').classList.remove('hidden');
                document.getElementById('sinDisponibilidad').classList.add('flex');
                return;
            }

            renderProfesionales(data.profesionales);

        } catch (e) {
            document.getElementById('profesionalLoading').classList.add('hidden');
            document.getElementById('sinDisponibilidad').classList.remove('hidden');
            document.getElementById('sinDisponibilidad').classList.add('flex');
        }
    }

    function renderProfesionales(profesionales) {
        const grid = document.getElementById('profesionalesGrid');
        grid.innerHTML = '';

        profesionales.forEach(prof => {
            const card = document.createElement('label');
            card.className = 'profesional-card flex items-center gap-3 p-3 rounded-xl border-2 border-outline-variant/20 cursor-pointer transition-all hover:border-primary/40';
            card.innerHTML = `
                <input type="radio" name="_profesional_radio" value="${prof.id}" class="sr-only profesional-radio" />
                <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 bg-primary/10 flex items-center justify-center border border-outline-variant/20">
                    ${prof.foto
                        ? `<img src="${prof.image}" alt="${prof.name}" class="w-full h-full object-cover" />`
                        : `<span class="text-sm font-bold text-primary/50">${prof.name.substring(0,2).toUpperCase()}</span>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-on-surface line-clamp-1">${prof.name}</p>
                    <p class="text-xs text-on-surface-variant">${prof.phone || ''}</p>
                </div>
            `;
            grid.appendChild(card);
        });

        grid.classList.remove('hidden');
        grid.classList.add('grid');

        // Evento selección
        grid.querySelectorAll('.profesional-radio').forEach(radio => {
            radio.addEventListener('change', () => {
                grid.querySelectorAll('.profesional-card').forEach(c => {
                    c.classList.remove('border-primary', 'bg-primary/5');
                    c.classList.add('border-outline-variant/20');
                });
                const selected = radio.closest('.profesional-card');
                selected.classList.add('border-primary', 'bg-primary/5');
                selected.classList.remove('border-outline-variant/20');
                document.getElementById('user_id').value = radio.value;
                checkFormReady();
            });
        });
    }

    // ─── Habilitar botón confirmar ───────────────────────────────────────
    function checkFormReady() {
        const fecha = fechaInput.value;
        const hora = horaInput.value;
        const prof = document.getElementById('user_id').value;
        const btn = document.getElementById('btnConfirmar');

        if (fecha && hora && prof) {
            btn.disabled = false;
            btn.className = btn.className
                .replace('bg-primary/40 cursor-not-allowed', '') +
                ' bg-primary hover:bg-primary/90 cursor-pointer shadow-md';
        }
    }

    fechaInput.addEventListener('change', checkFormReady);
    horaInput.addEventListener('change', checkFormReady);
</script>