<div x-data="disponibilidad({{ $disponibilidades->toJson() }})" x-init="init()">

    {{-- Selector de días --}}
    <div class="grid grid-cols-7 gap-2 mb-6">
        <template x-for="dia in dias" :key="dia.key">
            <button type="button" @click="toggleDia(dia.key)"
                :class="dia.activo ?
                    'bg-primary/10 border-primary/40 text-primary font-semibold' :
                    'border-outline-variant/30 text-on-surface-variant hover:bg-surface-container'"
                class="py-2 rounded-lg border text-sm transition text-center">
                <span x-text="dia.label"></span>
            </button>
        </template>
    </div>

    {{-- Filas de horario --}}
    <div class="space-y-3">
        <template x-for="(item, i) in activos()" :key="item.key">
            <div class="grid grid-cols-[1fr_1fr_1fr_auto] gap-4 items-center">
                <span class="text-sm font-medium text-on-surface capitalize" x-text="item.nombre"></span>

                <div>
                    <select :name="`disponibilidad[${i}][hora_inicio]`" x-model="item.inicio"
                        class="w-full rounded-lg border border-outline-variant/30 text-sm px-3 py-2 bg-surface focus:border-primary/40 focus:ring-primary/10">
                        <template x-for="h in horas" :key="h">
                            <option :value="h" x-text="h"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <select :name="`disponibilidad[${i}][hora_fin]`" x-model="item.fin"
                        class="w-full rounded-lg border border-outline-variant/30 text-sm px-3 py-2 bg-surface focus:border-primary/40 focus:ring-primary/10">
                        <template x-for="h in horas" :key="h">
                            <option :value="h" x-text="h"></option>
                        </template>
                    </select>
                </div>

                <button type="button" @click="toggleDia(item.key)"
                    class="text-on-surface-variant hover:text-error transition text-lg leading-none">×</button>

                <input type="hidden" :name="`disponibilidad[${i}][dia_semana]`" :value="item.key">
                <input type="hidden" :name="`disponibilidad[${i}][activo]`" value="1">
            </div>
        </template>

        <p x-show="activos().length === 0" class="text-sm text-on-surface-variant text-center py-4">
            Selecciona al menos un día para configurar el horario
        </p>
    </div>
</div>

<script>
    function disponibilidad(existentes) {
        const HORARIOS_EMPRESA = @json($horariosEmpresa);
        return {
            horas: [...Array(17 * 2)].map((_, i) => {
                const total = 360 + i * 30;
                return `${String(Math.floor(total/60)).padStart(2,'0')}:${String(total%60).padStart(2,'0')}`;
            }),
            dias: [{
                    key: 'lunes',
                    label: 'Lun',
                    nombre: 'Lunes',
                    activo: false,
                    inicio: '08:00',
                    fin: '17:00'
                },
                {
                    key: 'martes',
                    label: 'Mar',
                    nombre: 'Martes',
                    activo: false,
                    inicio: '08:00',
                    fin: '17:00'
                },
                {
                    key: 'miercoles',
                    label: 'Mié',
                    nombre: 'Miércoles',
                    activo: false,
                    inicio: '08:00',
                    fin: '17:00'
                },
                {
                    key: 'jueves',
                    label: 'Jue',
                    nombre: 'Jueves',
                    activo: false,
                    inicio: '08:00',
                    fin: '17:00'
                },
                {
                    key: 'viernes',
                    label: 'Vie',
                    nombre: 'Viernes',
                    activo: false,
                    inicio: '08:00',
                    fin: '17:00'
                },
                {
                    key: 'sabado',
                    label: 'Sáb',
                    nombre: 'Sábado',
                    activo: false,
                    inicio: '08:00',
                    fin: '17:00'
                },
                {
                    key: 'domingo',
                    label: 'Dom',
                    nombre: 'Domingo',
                    activo: false,
                    inicio: '08:00',
                    fin: '17:00'
                },
            ],
            init() {
                existentes.forEach(e => {
                    const dia = this.dias.find(d => d.key === e.dia_semana);
                    if (dia) {
                        dia.activo = true;
                        dia.inicio = e.hora_inicio.slice(0, 5);
                        dia.fin = e.hora_fin.slice(0, 5);
                    }
                });
            },
            toggleDia(key) {
                const d = this.dias.find(d => d.key === key);
                if (d) d.activo = !d.activo;
            },
            activos() {
                return this.dias.filter(d => d.activo);
            }
        }
    }
</script>
