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
        <x-form.field label="Día de la semana" for="dia">
            <x-form.select name="dia" id="dia">
                @foreach ([
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo',
    ] as $value => $label)
                    <option value="{{ $value }}"
                        {{ old('dia', $openingHour->day_of_week ?? '') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </x-form.select>
        </x-form.field>

        <!-- HORAS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <x-form.field label="Hora de inicio" for="hora_inicio">
                <x-form.input type="time" name="hora_inicio" id="hora_inicio" :value="old('hora_inicio', $openingHour->start_time ?? '')" />
            </x-form.field>

            <x-form.field label="Hora de fin" for="hora_fin">
                <x-form.input type="time" name="hora_fin" id="hora_fin" :value="old('hora_fin', $openingHour->end_time ?? '')" />
            </x-form.field>


        </div>

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
            Guardar
        </button>

    </div>

</div>

<!-- SIDEBAR -->
<div class="space-y-8">

    <!-- INFO -->
    <div class="bg-primary-container/10 rounded-xl p-6 border border-primary/10 space-y-3">

        <h3 class="text-sm font-semibold text-primary">
            Recomendaciones
        </h3>

        <ul class="text-sm text-on-surface-variant space-y-2">
            <li>• Evita que los horarios choquen entre si</li>
            <li>• Usa informacion real</li>
            <li>• Verifica que la hora fin sea mayor</li>
        </ul>

    </div>

</div>
