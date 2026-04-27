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
                        <span class="material-symbols-outlined">people</span>
                    </div>

                    <h2 class="text-xl font-bold text-primary tracking-tight">
                        Gestión de Clientes
                    </h2>
                </div>

                <p class="text-sm text-on-surface-variant ml-13">
                    Administra los clientes registrados en el sistema
                </p>
            </div>
        </header>

        <!-- TABLA -->
        <div class="px-8 pb-20">

            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20
                shadow-[0_10px_30px_rgba(95,94,90,0.05)] overflow-hidden">

                <!-- HEADER TABLA -->
                <div class="px-6 py-4 border-b border-outline-variant/20">
                    <h3 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide">
                        Lista de clientes
                    </h3>
                </div>

                <!-- DESKTOP -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">

                        <!-- THEAD -->
                        <thead class="bg-surface/50 text-on-surface-variant">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold">Cliente</th>
                                <th class="px-6 py-4 text-left font-semibold">Correo</th>
                                <th class="px-6 py-4 text-left font-semibold">Teléfono</th>
                                <th class="px-6 py-4 text-right font-semibold">Acciones</th>
                            </tr>
                        </thead>

                        <!-- TBODY -->
                        <tbody class="divide-y divide-outline-variant/10">

                            @forelse ($costumers as $costumer)
                            <tr class="hover:bg-surface/40 transition">

                                <!-- CLIENTE -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center">
                                            <span class="material-symbols-outlined text-primary text-xl">person</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-primary">
                                                {{ $costumer->name }}
                                            </p>
                                            <p class="text-xs text-on-surface-variant">
                                                ID: {{ $costumer->id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- CORREO -->
                                <td class="px-6 py-4">
                                    <p class="text-on-surface-variant">
                                        {{ $costumer->email }}
                                    </p>
                                </td>

                                <!-- TELÉFONO -->
                                <td class="px-6 py-4">
                                    <p class="text-on-surface-variant">
                                        {{ $costumer->phone ?? 'Sin teléfono' }}
                                    </p>
                                </td>

                                <!-- ACCIONES -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('costumers.show', $costumer->id) }}"
                                            class="text-secondary hover:text-primary transition">
                                            Ver
                                        </a>
                                        <a href="{{ route('costumers.edit', $costumer->id) }}"
                                            class="text-primary hover:text-primary-container transition">
                                            Editar
                                        </a>
                                        <button
                                            onclick="deleteCostumer({{ $costumer->id }})"
                                            class="text-error hover:text-on-error-container transition">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>

                            </tr>

                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-16">
                                    <div class="flex flex-col items-center gap-4">
                                        <span class="material-symbols-outlined text-4xl text-primary">
                                            people
                                        </span>
                                        <p class="text-on-surface-variant">
                                            No hay clientes registrados
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <!-- MOBILE -->
                <div class="md:hidden space-y-4 p-4">

                    @forelse ($costumers as $costumer)
                    <div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-4 shadow-sm">

                        <!-- TOP -->
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-xl">person</span>
                            </div>
                            <div>
                                <p class="font-semibold text-primary">
                                    {{ $costumer->name }}
                                </p>
                                <p class="text-xs text-on-surface-variant">
                                    ID: {{ $costumer->id }}
                                </p>
                            </div>
                        </div>

                        <!-- INFO -->
                        <div class="space-y-2 text-sm">
                            <p class="text-on-surface-variant">
                                <span class="text-secondary font-bold">Email:</span>
                                {{ $costumer->email }}
                            </p>
                            <p class="text-on-surface-variant">
                                <span class="text-secondary font-bold">Teléfono:</span>
                                {{ $costumer->phone ?? 'Sin teléfono' }}
                            </p>
                        </div>

                        <!-- ACCIONES -->
                        <div class="flex justify-end gap-4 mt-4 border-t pt-3">
                            <a href="{{ route('costumers.show', $costumer->id) }}"
                                class="text-secondary text-sm">
                                Ver
                            </a>
                            <a href="{{ route('costumers.edit', $costumer->id) }}"
                                class="text-primary text-sm">
                                Editar
                            </a>
                            <button
                                onclick="deleteCostumer({{ $costumer->id }})"
                                class="text-error text-sm">
                                Eliminar
                            </button>
                        </div>

                    </div>

                    @empty
                    <p class="text-center text-sm text-on-surface-variant">
                        No hay clientes registrados
                    </p>
                    @endforelse

                </div>

                <!-- PAGINACIÓN -->
                <div class="px-6 py-4 border-t border-outline-variant/20">
                    {!! $costumers->withQueryString()->links() !!}
                </div>

            </div>
        </div>

    </main>
</x-app-layout>

<script>
    function deleteCostumer(id) {
        Swal.fire({
            title: '¿Eliminar este cliente?',
            text: 'Esta acción no se puede deshacer',
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
            if (result.isConfirmed) {
                try {
                    await fetch(`/costumers/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    window.location.reload();
                } catch (error) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo eliminar',
                        icon: 'error',
                        background: '#fcf9f3',
                        color: '#1c1c19'
                    });
                }
            }
        });
    }
</script>