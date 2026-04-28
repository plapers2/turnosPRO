@php
    $oldDisp = old('disponibilidad');

    if ($oldDisp) {
        $dispParaJs = collect($oldDisp)
            ->filter(fn($s) => !empty($s['dia_semana'])) // ← filtrar vacíos
            ->map(
                fn($s) => [
                    'dia_semana' => $s['dia_semana'],
                    'hora_inicio' => $s['hora_inicio'] ?? '08:00',
                    'hora_fin' => $s['hora_fin'] ?? '09:00',
                ],
            )
            ->values();
    } else {
        $dispParaJs = $disponibilidades
            ->map(
                fn($d) => [
                    'dia_semana' => $d->day_of_week,
                    'hora_inicio' => substr($d->start_time, 0, 5),
                    'hora_fin' => substr($d->end_time, 0, 5),
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
                :class="estaActivo(dia.key) ?
                    'bg-primary/10 border-primary/40 text-primary font-semibold' :
                    'border-outline-variant/30 text-on-surface-variant hover:bg-surface-container'"
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
                                <select :name="`disponibilidad[${indexGlobal(dia.key, ti)}][hora_inicio]`"
                                    x-model="turno.inicio"
                                    :class="tieneError(dia.key, ti, 'hora_inicio') ? 'border-red-400 focus:border-red-400' : ''"
                                    class="w-full rounded-lg border border-outline-variant/30 text-sm px-3 py-2 bg-surface focus:border-primary/40 focus:ring-primary/10">
                                    <template x-for="h in horas" :key="h">
                                        {{-- Agrega :selected explícito --}}
                                        <option :value="h" :selected="h === turno.inicio" x-text="h">
                                        </option>
                                    </template>
                                </select>

                                <select :name="`disponibilidad[${indexGlobal(dia.key, ti)}][hora_fin]`"
                                    x-model="turno.fin"
                                    :class="tieneError(dia.key, ti, 'hora_fin') ? 'border-red-400 focus:border-red-400' : ''"
                                    class="w-full rounded-lg border border-outline-variant/30 text-sm px-3 py-2 bg-surface focus:border-primary/40 focus:ring-primary/10">
                                    <template x-for="h in horas" :key="h">
                                        <option :value="h" :selected="h === turno.fin" x-text="h">
                                        </option>
                                    </template>
                                </select>

                                <button type="button" @click="eliminarTurno(dia.key, ti)"
                                    class="text-on-surface-variant hover:text-error transition text-lg leading-none px-1">×</button>

                                <input type="hidden" :name="`disponibilidad[${indexGlobal(dia.key, ti)}][dia_semana]`"
                                    :value="dia.key">
                                <input type="hidden" :name="`disponibilidad[${indexGlobal(dia.key, ti)}][activo]`"
                                    value="1">
                            </div>

                            {{-- Mensaje de error por turno (viene del servidor via Blade) --}}
                            <template x-if="mensajeError(dia.key, ti, 'hora_inicio')">
                                <p class="text-xs text-red-500 mt-0.5"
                                    x-text="mensajeError(dia.key, ti, 'hora_inicio')"></p>
                            </template>
                            <template x-if="mensajeError(dia.key, ti, 'hora_fin')">
                                <p class="text-xs text-red-500 mt-0.5" x-text="mensajeError(dia.key, ti, 'hora_fin')">
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
        const HORARIOS_EMPRESA = @json($horariosEmpresa);
        const ERRORES = @json($errors->toArray());

        // Construir turnos ANTES de retornar el objeto reactivo
        const turnosIniciales = {};
        existentes.forEach(e => {
            if (!e.dia_semana) return;
            if (!turnosIniciales[e.dia_semana]) turnosIniciales[e.dia_semana] = [];
            turnosIniciales[e.dia_semana].push({
                inicio: (e.hora_inicio ?? '08:00').slice(0, 5),
                fin: (e.hora_fin ?? '09:00').slice(0, 5),
            });
        });

        // Construir errores indexados ANTES de retornar
        const erroresIniciales = {};
        Object.entries(ERRORES).forEach(([clave, msgs]) => {
            const match = clave.match(/^disponibilidad\.(\d+)\.(\w+)$/);
            if (!match) return;
            const idx = parseInt(match[1]);
            const campo = match[2];
            if (!erroresIniciales[idx]) erroresIniciales[idx] = {};
            erroresIniciales[idx][campo] = msgs[0];
        });

        return {
            horas: [...Array(17 * 2)].map((_, i) => {
                const total = 360 + i * 30;
                return `${String(Math.floor(total / 60)).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`;
            }),
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

            // Ya inicializados, Alpine NO los mutará al arrancar
            turnos: turnosIniciales,
            erroresIndexados: erroresIniciales,

            // init() ya no hace nada con datos, solo podría usarse para efectos secundarios
            init() {},

            tieneError(key, ti, campo) {
                const idx = this.indexGlobal(key, ti);
                return !!this.erroresIndexados?.[idx]?.[campo];
            },
            mensajeError(key, ti, campo) {
                const idx = this.indexGlobal(key, ti);
                return this.erroresIndexados?.[idx]?.[campo] ?? null;
            },
            estaActivo(key) {
                return !!this.turnos[key]?.length;
            },
            toggleDia(key) {
                if (this.estaActivo(key)) {
                    delete this.turnos[key];
                } else {
                    this.turnos[key] = [{
                        inicio: '08:00',
                        fin: '17:00'
                    }];
                }
            },
            agregarTurno(key) {
                if (!this.turnos[key]) this.turnos[key] = [];
                this.turnos[key].push({
                    inicio: '08:00',
                    fin: '17:00'
                });
            },
            eliminarTurno(key, index) {
                this.turnos[key].splice(index, 1);
                if (!this.turnos[key].length) delete this.turnos[key];
            },
            diasActivos() {
                return this.dias.filter(d => this.estaActivo(d.key));
            },
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
