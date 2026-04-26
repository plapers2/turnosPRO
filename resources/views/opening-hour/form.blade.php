<!-- COLUMNA PRINCIPAL -->
<div class="lg:col-span-2 space-y-8">

    <!-- CARD PRINCIPAL -->
    <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">

        <div>
            <h2 class="text-lg font-semibold text-primary">
                Configuración del horario
            </h2>
            <p class="text-sm text-on-surface-variant">
                Define el día y rango de atención
            </p>
        </div>

        <!-- DIA -->
        <x-form.field label="Día de la semana" for="day_of_week">
            <x-form.select name="day_of_week" id="day_of_week">
                <option value="">Seleccionar día</option>
                <option value="monday">Lunes</option>
                <option value="tuesday">Martes</option>
                <option value="wednesday">Miércoles</option>
                <option value="thursday">Jueves</option>
                <option value="friday">Viernes</option>
                <option value="saturday">Sábado</option>
                <option value="sunday">Domingo</option>
            </x-form.select>
        </x-form.field>

        <!-- HORAS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <x-form.field label="Hora de inicio" for="start_time">
                <x-form.input type="time" name="start_time" id="start_time" :value="old('start_time')" />
            </x-form.field>

            <x-form.field label="Hora de fin" for="end_time">
                <x-form.input type="time" name="end_time" id="end_time" :value="old('end_time')" />
            </x-form.field>

        </div>

        <!-- DURACION -->
        <x-form.field label="Duración por cita (minutos)" for="duration">
            <x-form.input type="number" name="duration" id="duration" placeholder="Ej. 30" :value="old('duration')" />
        </x-form.field>

    </div>

    <!-- BOTONES -->
    <div class="flex justify-end gap-4 pt-4">

        <a href="{{ route('opening-hours.index') }}"
            class="px-5 py-2.5 rounded-lg text-sm font-semibold
                   bg-surface-container hover:bg-surface-container-high transition">
            Cancelar
        </a>

        <button type="submit"
            class="px-6 py-2.5 rounded-lg text-sm font-semibold
                   bg-primary text-white hover:bg-primary/90 transition shadow-md">
            Guardar horario
        </button>

    </div>

</div>

<!-- SIDEBAR -->
<div class="space-y-8">

    <!-- PREVIEW -->
    <div class="bg-surface-container rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">

        <h3 class="text-md font-semibold text-primary">
            Vista previa
        </h3>

        <div id="previewBox" class="text-sm text-on-surface-variant">
            Completa los campos para ver el resumen
        </div>

    </div>

    <!-- INFO -->
    <div class="bg-primary-container/10 rounded-xl p-6 border border-primary/10 space-y-3">

        <h3 class="text-sm font-semibold text-primary">
            Recomendaciones
        </h3>

        <ul class="text-sm text-on-surface-variant space-y-2">
            <li>• Evita solapamientos de horarios</li>
            <li>• Usa intervalos claros (30, 45, 60 min)</li>
            <li>• Verifica que la hora fin sea mayor</li>
        </ul>

    </div>

</div>

<!-- SCRIPT PREVIEW -->
<script>
    const day = document.getElementById('day_of_week');
    const start = document.getElementById('start_time');
    const end = document.getElementById('end_time');
    const duration = document.getElementById('duration');
    const preview = document.getElementById('previewBox');

    function updatePreview() {
        const days = {
            monday: 'Lunes',
            tuesday: 'Martes',
            wednesday: 'Miércoles',
            thursday: 'Jueves',
            friday: 'Viernes',
            saturday: 'Sábado',
            sunday: 'Domingo'
        };

        if (!day.value || !start.value || !end.value) {
            preview.innerHTML = 'Completa los campos para ver el resumen';
            return;
        }

        preview.innerHTML = `
            <p><strong>${days[day.value]}</strong></p>
            <p>${start.value} - ${end.value}</p>
            <p>${duration.value || 0} min por cita</p>
        `;
    }

    [day, start, end, duration].forEach(el => {
        el.addEventListener('input', updatePreview);
    });
</script>
