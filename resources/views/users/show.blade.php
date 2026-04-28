<x-app-layout>
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <x-form.header icono="arrow_back" ruta="users" titulo="{{ $user->name }}" subtitulo="Detalle del profesional" />

        <div class="px-8 pb-20">
            <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">

                <!-- COLUMNA PRINCIPAL -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- CARD INFO -->
                    <div
                        class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">

                        <div>
                            <h2 class="text-lg font-semibold text-primary mb-1">
                                Información del usuario
                            </h2>
                            <p class="text-sm text-on-surface-variant">
                                Datos básicos del profesional
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <p class="text-sm text-on-surface-variant">Nombre</p>
                                <p class="font-medium text-on-surface">{{ $user->name }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-on-surface-variant">Teléfono</p>
                                <p class="font-medium text-on-surface">{{ $user->phone ?? '—' }}</p>
                            </div>

                        </div>

                        <div>
                            <p class="text-sm text-on-surface-variant">Correo electrónico</p>
                            <p class="font-medium text-on-surface">{{ $user->email }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-on-surface-variant">Rol</p>
                            <p class="font-medium text-on-surface">
                                {{ ucfirst($user->roles->first()?->name ?? 'Sin rol') }}
                            </p>
                        </div>

                    </div>

                    <!-- CARD DISPONIBILIDAD -->
                    <div
                        class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">

                        <div>
                            <h2 class="text-lg font-semibold text-primary mb-1">
                                Disponibilidad semanal
                            </h2>
                            <p class="text-sm text-on-surface-variant">
                                Horarios configurados del profesional
                            </p>
                        </div>

                        @php
                            $dias = [
                                'Monday' => 'Lunes',
                                'Tuesday' => 'Martes',
                                'Wednesday' => 'Miércoles',
                                'Thursday' => 'Jueves',
                                'Friday' => 'Viernes',
                                'Saturday' => 'Sábado',
                                'Sunday' => 'Domingo',
                            ];


                            $agrupados = $user->professionalAvailabilities->groupBy('day_of_week');
                        @endphp

                        <div class="space-y-4">

                            @forelse ($dias as $key => $nombre)

                                <div>
                                    <h3>{{ $nombre }}</h3>

                                    @if ($agrupados->has($key))
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($agrupados[$key] as $horario)
                                                <span
                                                    class="px-3 py-1 mt-1 text-xs rounded-full bg-primary/10 text-primary">
                                                    {{ \Carbon\Carbon::parse($horario->start_time)->format('g:i A') }}
                                                    -
                                                    {{ \Carbon\Carbon::parse($horario->end_time)->format('g:i A') }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        No disponible
                                    @endif

                                </div>
                            @empty
                                <p class="text-sm text-on-surface-variant">
                                    No hay horarios registrados
                                </p>
                            @endforelse

                        </div>

                    </div>

                    <!-- BOTONES -->
                    <div class="flex justify-end gap-4 pt-4">

                        <a href="{{ route('users.index') }}"
                            class="px-5 py-2.5 rounded-lg text-sm font-semibold
                                   bg-surface-container hover:bg-surface-container-high transition">
                            Volver
                        </a>

                        <a href="{{ route('users.edit', $user->id) }}"
                            class="px-6 py-2.5 rounded-lg text-sm font-semibold
                                   bg-primary text-white hover:bg-primary/90 transition shadow-md hover:shadow-lg">
                            Editar usuario
                        </a>

                    </div>

                </div>

                <!-- SIDEBAR -->
                <div class="space-y-8">

                    {{-- CARD SERVICIOS — insertar después de CARD DISPONIBILIDAD y antes de BOTONES --}}
<div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">

    <div>
        <h2 class="text-lg font-semibold text-primary mb-1">Servicios asignados</h2>
        <p class="text-sm text-on-surface-variant">
            Servicios que este profesional puede gestionar
        </p>
    </div>

    @if ($user->services->isNotEmpty())
        <div class="flex flex-wrap gap-2">
            @foreach ($user->services as $service)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium
                             bg-primary/10 text-primary border border-primary/15">
                    <span class="material-symbols-outlined text-[13px]">design_services</span>
                    {{ $service->name }}
                </span>
            @endforeach
        </div>
    @else
        <div class="flex items-center gap-3 py-4 text-sm text-on-surface-variant">
            <span class="material-symbols-outlined text-xl opacity-40">design_services</span>
            Sin servicios asignados
        </div>
    @endif

</div>


                    <!-- FOTO -->
                    <div
                        class="bg-surface-container rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">

                        <h3 class="text-md font-semibold text-primary">
                            Foto de perfil
                        </h3>

                        @if ($user->image)
                            <img src="{{ asset('storage/' . $user->image) }}"
                                class="w-full h-48 object-cover rounded-lg border border-outline-variant/20">
                        @else
                            <div
                                class="w-full h-48 flex items-center justify-center rounded-lg border border-dashed border-outline-variant text-sm text-on-surface-variant">
                                Sin imagen
                            </div>
                        @endif

                    </div>

                    <!-- INFO EXTRA -->
                    <div class="bg-primary-container/10 rounded-xl p-6 border border-primary/10 space-y-3">
                        <h3 class="text-sm font-semibold text-primary">
                            Información
                        </h3>

                        <ul class="text-sm text-on-surface-variant space-y-2">
                            <li>• Usuario creado correctamente</li>
                            <li>• Puedes editar sus horarios</li>
                            <li>• Verifica su disponibilidad antes de asignar citas</li>
                        </ul>
                    </div>

                </div>

            </div>
        </div>

    </main>
</x-app-layout>
