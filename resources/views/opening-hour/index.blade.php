<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <x-header-admin icono="schedule" titulo="Horarios de Atención"
            mensaje="Visualiza y administra los horarios por día" textoBoton="Nuevo horario"
            mensajeEmpleado="Visualiza los horarios de la empresa a la que perteneces" ruta="opening-hours" />

        <div class="px-8 pb-20">

            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow p-6">

                <!-- GRID SEMANAL -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                    @php
                        $days = [
                            'Monday' => 'Lunes',
                            'Tuesday' => 'Martes',
                            'Wednesday' => 'Miércoles',
                            'Thursday' => 'Jueves',
                            'Friday' => 'Viernes',
                            'Saturday' => 'Sábado',
                            'Sunday' => 'Domingo',
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
                                <div
                                    class="bg-surface rounded-xl border border-outline-variant/20 p-5 h-full flex flex-col">

                                    <!-- HEADER DIA -->
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-base font-semibold text-primary">
                                            {{ $label }}
                                        </h3>

                                        <span class="text-xs text-on-surface-variant">
                                            {{ count($openingHours[$key] ?? []) }} horarios
                                        </span>
                                    </div>

                                    <!-- LISTA -->
                                    <div class="space-y-3 flex-1">

                                        @forelse ($openingHours[$key] ?? [] as $hour)
                                            <div
                                                class="bg-surface-container-high rounded-xl px-4 py-3 flex flex-col gap-3 hover:shadow-md transition">

                                                <!-- INFO -->
                                                <div class="flex items-center justify-between gap-3">

                                                    <div class="text-sm font-semibold text-on-surface">
                                                        {{ \Carbon\Carbon::parse($hour->start_time)->format('h:i A') }}
                                                        <span class="text-on-surface-variant font-normal mx-1">—</span>
                                                        {{ \Carbon\Carbon::parse($hour->end_time)->format('h:i A') }}
                                                    </div>
                                                </div>

                                                {{-- ESTADO --}}
                                                <div class="text-xs px-2 py-1 w-1/4 text-center rounded-md  {{ $hour->deleted_at ? 'bg-error/20 text-on-error-container' : 'bg-indigo-400/20 text-indigo-700' }}"
                                                    font-medium whitespace-nowrap">
                                                    {{ $hour->deleted_at ? 'Inactivo' : 'Activo' }}
                                                </div>

                                                <!-- ACCIONES -->
                                                @role('admin')
                                                    <div
                                                        class="flex items-center justify-end gap-4 text-xs border-t border-outline-variant/20 pt-2">

                                                        @if ($hour->deleted_at)
                                                            <button onclick="restoreHour(this, {{ $hour->id }})"
                                                                class="text-green-600 font-medium hover:underline">
                                                                Restaurar
                                                            </button>
                                                        @else
                                                            <a href="{{ route('opening-hours.edit', $hour->id) }}"
                                                                class="text-primary font-medium hover:underline">
                                                                Editar
                                                            </a>

                                                            <button onclick="deleteHour({{ $hour->id }})"
                                                                class="text-error font-medium hover:underline">
                                                                Eliminar
                                                            </button>
                                                        @endif

                                                    </div>
                                                @endrole

                                            </div>

                                        @empty
                                            <div class="flex-1 flex items-center justify-center">
                                                <p class="text-sm text-on-surface-variant italic">
                                                    Sin horarios
                                                </p>
                                            </div>
                                        @endforelse

                                    </div>

                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>

            </div>

        </div>

    </main>
</x-app-layout>

<script>
    async function restoreHour(button, id) {

        const original = button.innerHTML;

        button.innerHTML = `
        <span class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full"></span>
    `;
        button.disabled = true;

        try {
            const response = await fetch(`/opening-hours/${id}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error();

            location.reload();

        } catch (error) {
            button.innerHTML = original;
            button.disabled = false;

            Swal.fire({
                title: 'Error',
                text: 'No se pudo restaurar',
                icon: 'error'
            });
        }
    }


    async function deleteHour(id) {

        Swal.fire({
            title: '¿Eliminar horario?',
            text: 'Podrás restaurarlo después',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ba1a1a',
            cancelButtonColor: '#847467',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            background: '#fcf9f3',
            color: '#1c1c19',
        }).then(async (result) => {

            if (!result.isConfirmed) return;

            try {
                const response = await fetch(`/opening-hours/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Error desconocido');
                }

                Swal.fire({
                    title: 'Eliminado',
                    text: data.message || 'El horario fue eliminado',
                    icon: 'success',
                    timer: 1200,
                    showConfirmButton: false,
                    background: '#fcf9f3',
                    color: '#1c1c19'
                }).then(() => location.reload());

            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: error.message,
                    icon: 'error',
                    background: '#fcf9f3',
                    color: '#1c1c19',
                    showConfirmButton: false,
                    timer: 2000
                })
            }

        });
    }
</script>
