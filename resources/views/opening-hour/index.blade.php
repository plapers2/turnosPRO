<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <x-header-admin icono="schedule" titulo="Horarios de Atención"
            mensaje="Visualiza y administra los horarios por día" textoBoton="Nuevo horario" ruta="opening-hours" />

        <div class="px-8 pb-20">

            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow p-6">

                <!-- GRID SEMANAL -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">

                    @php
                        $days = [
                            'monday' => 'Lunes',
                            'tuesday' => 'Martes',
                            'wednesday' => 'Miércoles',
                            'thursday' => 'Jueves',
                            'friday' => 'Viernes',
                            'saturday' => 'Sábado',
                            'sunday' => 'Domingo',
                        ];
                    @endphp

                    @foreach ($days as $key => $label)
                        <div class="border border-outline-variant/20 rounded-xl p-4 bg-surface">

                            <!-- DIA -->
                            <h3 class="text-sm font-semibold text-primary mb-3">
                                {{ $label }}
                            </h3>

                            <!-- HORARIOS -->
                            <div class="space-y-2">

                                @forelse ($openingHours[$key] ?? [] as $hour)
                                    <div
                                        class="bg-surface-container-high rounded-lg p-3 flex flex-col justify-between h-full">

                                        <!-- CONTENIDO -->
                                        <div>
                                            <p class="text-sm font-medium text-on-surface">
                                                {{ \Carbon\Carbon::parse($hour->start_time)->format('h:i A') }}
                                                -
                                                {{ \Carbon\Carbon::parse($hour->end_time)->format('h:i A') }}
                                            </p>

                                            <p class="text-xs text-on-surface-variant">
                                                {{ $hour->duration }} min
                                            </p>
                                        </div>

                                        <!-- ACCIONES -->
                                        <div
                                            class="flex lg:flex-col lg:items-start justify-start gap-3 mt-3 pt-3 border-t border-outline-variant/20">

                                            <a href="{{ route('opening-hours.edit', $hour->id) }}"
                                                class="text-sm font-semibold text-primary hover:text-primary-container transition">
                                                Editar
                                            </a>

                                            <button onclick="deleteHour({{ $hour->id }})"
                                                class="text-sm font-semibold text-error hover:text-on-error-container transition">
                                                Eliminar
                                            </button>

                                        </div>

                                    </div>

                                @empty
                                    <p class="text-xs text-on-surface-variant italic">
                                        Sin horarios
                                    </p>
                                @endforelse

                            </div>

                        </div>
                    @endforeach

                </div>

            </div>

        </div>

    </main>
</x-app-layout>
