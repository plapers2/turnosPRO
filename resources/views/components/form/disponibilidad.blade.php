@php
    $oldDisp = old('disponibilidad');

    if ($oldDisp) {
        $dispParaJs = collect($oldDisp)
            ->filter(fn($s) => !empty($s['day_of_week']))
            ->map(
                fn($s) => [
                    'day_of_week' => $s['day_of_week'],
                    'start_time' => $s['start_time'] ?? '08:00',
                    'end_time' => $s['end_time'] ?? '09:00',
                ],
            )
            ->values();
    } else {
        $dispParaJs = $disponibilidades
            ->map(
                fn($d) => [
                    'day_of_week' => $d->day_of_week,
                    'start_time' => substr($d->start_time, 0, 5),
                    'end_time' => substr($d->end_time, 0, 5),
                ],
            )
            ->values();
    }
@endphp


<div x-data="disponibilidad({{ $dispParaJs->toJson() }})">

    {{-- Selector de días --}}
    <div class="grid grid-cols-7 gap-2 mb-6">
        <template x-for="dia in dias" :key="dia.key">
            <button type="button" @click="toggleDia(dia.key)"
                :disabled="!diaDisponible(dia.key)"
                :title="!diaDisponible(dia.key) ? 'Sin horario configurado para este día' : ''"
                :class="!diaDisponible(dia.key) ?
                    'border-outline-variant/20 text-on-surface-variant/40 cursor-not-allowed opacity-50' :
                    (estaActivo(dia.key) ?
                        'bg-primary/10 border-primary/40 text-primary font-semibold' :
                        'border-outline-variant/30 text-on-surface-variant hover:bg-surface-container')"
                class="py-2 rounded-lg border text-sm transition text-center">
                <span x-text="dia.label"></span>
            </button>
        </template>
    </div>

    {{-- Grupos por día --}}
    <div class="space-y-4">
        <template x-for="dia in diasActivos()" :key="dia.key">
            <div class="bg-surface-container/40 rounded-xl p-4 border border-outline-variant/20">

                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-on-surface" x-text="dia.nombre"></span>
                    <button type="button" @click="agregarTurno(dia.key)"
                        class="text-xs text-primary bg-primary/10 hover:bg-primary/20 px-3 py-1 rounded-full transition font-medium">
                        + Agregar turno
                    </button>
                </div>

                <div class="space-y-2">
                    <template x-for="(turno, ti) in turnos[dia.key]" :key="`${dia.key}-${ti}`">
                        <div class="flex flex-col gap-1">

                            <div class="grid grid-cols-[1fr_1fr_auto] gap-3 items-center">

                                {{-- Select inicio: usa horasDia(dia.key) --}}
                                <select :name="`disponibilidad[${indexGlobal(dia.key, ti)}][start_time]`"
                                    x-model="turno.inicio"
                                    :class="tieneError(dia.key, ti, 'start_time') ? 'border-red-400 focus:border-red-400' : ''"
                                    class="w-full rounded-lg border border-outline-variant/30 text-sm px-3 py-2 bg-surface focus:border-primary/40 focus:ring-primary/10">
                                    <template x-for="h in horasDia(dia.key)" :key="h">
                                        <option :value="h" :selected="h === turno.inicio" x-text="h">
                                        </option>
                                    </template>
                                </select>

                                {{-- Select fin: usa horasDia(dia.key) --}}
                                <select :name="`disponibilidad[${indexGlobal(dia.key, ti)}][end_time]`"
                                    x-model="turno.fin"
                                    :class="tieneError(dia.key, ti, 'end_time') ? 'border-red-400 focus:border-red-400' : ''"
                                    class="w-full rounded-lg border border-outline-variant/30 text-sm px-3 py-2 bg-surface focus:border-primary/40 focus:ring-primary/10">
                                    <template x-for="h in horasDia(dia.key)" :key="h">
                                        <option :value="h" :selected="h === turno.fin" x-text="h">
                                        </option>
                                    </template>
                                </select>

                                <button type="button" @click="eliminarTurno(dia.key, ti)"
                                    class="text-on-surface-variant hover:text-error transition text-lg leading-none px-1">×</button>

                                <input type="hidden"
                                    :name="`disponibilidad[${indexGlobal(dia.key, ti)}][day_of_week]`"
                                    :value="dia.key">
                                <input type="hidden" :name="`disponibilidad[${indexGlobal(dia.key, ti)}][activo]`"
                                    value="1">
                            </div>

                            {{-- Mensajes de error por turno (servidor via Blade) --}}
                            <template x-if="mensajeError(dia.key, ti, 'start_time')">
                                <p class="text-xs text-red-500 mt-0.5" x-text="mensajeError(dia.key, ti, 'start_time')">
                                </p>
                            </template>
                            <template x-if="mensajeError(dia.key, ti, 'end_time')">
                                <p class="text-xs text-red-500 mt-0.5" x-text="mensajeError(dia.key, ti, 'end_time')">
                                </p>
                            </template>

                        </div>
                    </template>
                </div>

            </div>
        </template>

        <p x-show="diasActivos().length === 0" class="text-sm text-on-surface-variant text-center py-4">
            Selecciona al menos un día para configurar el horario
        </p>
    </div>
</div>

<script>
    function disponibilidad(existentes) {
        const HORARIOS_EMPRESA = (() => {
            const raw = @json($horariosEmpresa);
            if (!raw) return [];
            if (Array.isArray(raw)) return raw;
            if (typeof raw === 'object') return Object.values(raw);
            return [];
        })();
        const ERRORES = @json($errors->toArray());

        // ── Todas las horas posibles en intervalos de 30 min (06:00 – 22:30) ──
        const TODAS_LAS_HORAS = [...Array(17 * 2)].map((_, i) => {
            const total = 360 + i * 30;
            return `${String(Math.floor(total / 60)).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`;
        });

        /**
         * Indica si la empresa tiene algún horario configurado para ese día.
         * Se usa para decidir si el día puede marcarse en el selector.
         */
        function horarioEmpresaExiste(dayKey) {
            return HORARIOS_EMPRESA.some(h => h.day_of_week === dayKey);
        }

        /**
         * Devuelve el array de horas permitidas para un día concreto
         * según los rangos de HORARIOS_EMPRESA.
         * Si el día no tiene horario de empresa configurado, devuelve todas las horas
         * (fallback usado solo para construir los <select>, no para habilitar el día).
         */
        function horasPorDia(dayKey) {
            const rangos = HORARIOS_EMPRESA.filter(h => h.day_of_week === dayKey);
            if (!rangos.length) return TODAS_LAS_HORAS;

            return TODAS_LAS_HORAS.filter(h =>
                rangos.some(r =>
                    h >= r.start_time.slice(0, 5) &&
                    h <= r.end_time.slice(0, 5)
                )
            );
        }

        /**
         * Devuelve la primera y última hora válida para un día,
         * usadas al crear turnos nuevos.
         */
        function primeraYUltimaHora(dayKey) {
            const horas = horasPorDia(dayKey);
            return {
                inicio: horas[0] ?? '08:00',
                fin: horas[horas.length - 1] ?? '17:00',
            };
        }

        // ── Turnos iniciales (desde BD o de old()) ──────────────────────────
        const turnosIniciales = {};
        existentes.forEach(e => {
            if (!e.day_of_week) return;
            if (!turnosIniciales[e.day_of_week]) turnosIniciales[e.day_of_week] = [];
            turnosIniciales[e.day_of_week].push({
                inicio: (e.start_time ?? '08:00').slice(0, 5),
                fin: (e.end_time ?? '09:00').slice(0, 5),
            });
        });

        // ── Errores del servidor indexados por posición global ───────────────
        const erroresIniciales = {};
        Object.entries(ERRORES).forEach(([clave, msgs]) => {
            const match = clave.match(/^disponibilidad\.(\d+)\.(\w+)$/);
            if (!match) return;
            const idx = parseInt(match[1]);
            const campo = match[2];
            if (!erroresIniciales[idx]) erroresIniciales[idx] = {};
            erroresIniciales[idx][campo] = msgs[0];
        });

        // ── Objeto Alpine ────────────────────────────────────────────────────
        return {
            dias: [{
                    key: 'Monday',
                    label: 'Lun',
                    nombre: 'Lunes'
                },
                {
                    key: 'Tuesday',
                    label: 'Mar',
                    nombre: 'Martes'
                },
                {
                    key: 'Wednesday',
                    label: 'Mié',
                    nombre: 'Miércoles'
                },
                {
                    key: 'Thursday',
                    label: 'Jue',
                    nombre: 'Jueves'
                },
                {
                    key: 'Friday',
                    label: 'Vie',
                    nombre: 'Viernes'
                },
                {
                    key: 'Saturday',
                    label: 'Sáb',
                    nombre: 'Sábado'
                },
                {
                    key: 'Sunday',
                    label: 'Dom',
                    nombre: 'Domingo'
                },
            ],

            turnos: turnosIniciales,
            erroresIndexados: erroresIniciales,

            init() {
                // Si por alguna razón llegan turnos precargados (old() o BD) en un día
                // que ya no tiene horario de empresa, los quitamos para mantener consistencia.
                Object.keys(this.turnos).forEach(key => {
                    if (!this.diaDisponible(key)) {
                        delete this.turnos[key];
                    }
                });
            },

            // ── Horas disponibles para un día (expuesto al template) ─────────
            horasDia(dayKey) {
                return horasPorDia(dayKey);
            },

            // ── Disponibilidad del día (horario de empresa) ───────────────────
            diaDisponible(key) {
                return horarioEmpresaExiste(key);
            },

            // ── Errores ──────────────────────────────────────────────────────
            tieneError(key, ti, campo) {
                const idx = this.indexGlobal(key, ti);
                return !!this.erroresIndexados?.[idx]?.[campo];
            },
            mensajeError(key, ti, campo) {
                const idx = this.indexGlobal(key, ti);
                return this.erroresIndexados?.[idx]?.[campo] ?? null;
            },

            // ── Días ─────────────────────────────────────────────────────────
            estaActivo(key) {
                return !!this.turnos[key]?.length;
            },
            toggleDia(key) {
                if (this.estaActivo(key)) {
                    delete this.turnos[key];
                    return;
                }

                // Bloquea la activación de días sin horario de empresa configurado
                if (!this.diaDisponible(key)) return;

                const {
                    inicio,
                    fin
                } = primeraYUltimaHora(key);
                this.turnos[key] = [{
                    inicio,
                    fin
                }];
            },
            diasActivos() {
                return this.dias.filter(d => this.estaActivo(d.key));
            },

            // ── Turnos ───────────────────────────────────────────────────────
            agregarTurno(key) {
                if (!this.diaDisponible(key)) return;
                if (!this.turnos[key]) this.turnos[key] = [];
                const {
                    inicio,
                    fin
                } = primeraYUltimaHora(key);
                this.turnos[key].push({
                    inicio,
                    fin
                });
            },
            eliminarTurno(key, index) {
                this.turnos[key].splice(index, 1);
                if (!this.turnos[key].length) delete this.turnos[key];
            },

            // ── Índice global para los nombres de los inputs ─────────────────
            indexGlobal(key, ti) {
                let idx = 0;
                for (const dia of this.dias) {
                    if (!this.turnos[dia.key]) continue;
                    if (dia.key === key) return idx + ti;
                    idx += this.turnos[dia.key].length;
                }
                return idx;
            },
        };
    }
</script>
