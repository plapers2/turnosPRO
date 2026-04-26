<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <x-header-admin
            icono="business"
            titulo="Gestión de Tipos de Empresa"
            mensaje="Administra los tipos de empresa registrados en el sistema"
            textoBoton="Nuevo Tipo de Empresa"
            ruta="type-companies" />

        <!-- TABLA -->
        <div class="px-8 pb-20">

            <div
                class="bg-surface-container-lowest rounded-xl border border-outline-variant/20
                shadow-[0_10px_30px_rgba(95,94,90,0.05)] overflow-hidden">

                <!-- HEADER TABLA -->
                <div class="px-6 py-4 border-b border-outline-variant/20">
                    <h3 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide">
                        Lista de Tipos de Empresas
                    </h3>
                </div>

                <!-- DESKTOP -->
                <div class="hidden md:block overflow-x-auto">

                    <table class="w-full text-sm">

                        <!-- THEAD -->
                        <thead class="bg-surface/50 text-on-surface-variant">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold">Nombre</th>
                                <th class="px-6 py-4 text-center font-semibold">Cantidad de Empresas Asociadas</th>
                                <th class="px-6 py-4 text-right font-semibold">Acciones</th>
                            </tr>
                        </thead>

                        <!-- TBODY -->
                        <tbody class="divide-y divide-outline-variant/10">

                            @forelse ($typeCompanies as $company)
                            <tr class="hover:bg-surface/40 transition">

                                <!-- EMPRESA -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">

                                        <img class="w-12 h-12 rounded-lg object-cover"
                                            src="{{ $company->logo ? asset('storage/' . $company->logo) : 'https://ui-avatars.com/api/?name=' . $company->name }}"
                                            alt="{{ $company->name }}">

                                        <div>
                                            <p class="font-semibold text-primary">
                                                {{ $company->name }}
                                            </p>

                                            <p class="text-xs text-on-surface-variant">
                                                ID: {{ $company->id }}
                                            </p>
                                        </div>

                                    </div>
                                </td>

                                <!-- CANTIDAD DE EMPRESAS ASOCIADAS -->
                                <td class="px-6 py-4 text-center">
                                    <p class="text-md badge-soft badge text-on-surface-variant">
                                        {{ $company->companies_count ?? 'Sin Empresas Asociadas' }}
                                    </p>
                                </td>

                                <!-- ACCIONES -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-3">

                                        <a
                                            href="{{ route('type-companies.show', $company->id) }}"
                                            class="text-secondary hover:text-primary transition">
                                            Ver
                                        </a>

                                        <a
                                            href="{{ route('type-companies.edit', $company->id) }}"
                                            class="text-primary hover:text-primary-container transition">
                                            Editar
                                        </a>

                                        <button
                                            onclick="deletecompany({{ $company->id }})"
                                            class="text-error hover:text-on-error-container transition">
                                            Eliminar
                                        </button>

                                    </div>
                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="6" class="text-center py-16">

                                    <div class="flex flex-col items-center gap-4">

                                        <span class="material-symbols-outlined text-4xl text-primary">
                                            business
                                        </span>

                                        <p class="text-on-surface-variant">
                                            No hay tipos de empresas registrados
                                        </p>

                                        <a
                                            href="{{ route('type-companies.create') }}"
                                            class="px-4 py-2 rounded-lg bg-primary text-white text-sm">
                                            Crear Tipo de Empresa
                                        </a>

                                    </div>

                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

                <!-- MOBILE -->
                <div class="md:hidden space-y-4">

                    @forelse ($typeCompanies as $company)
                    <div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-4 shadow-sm">

                        <!-- TOP -->
                        <div class="flex items-center gap-4 mb-3">

                            <img class="w-12 h-12 rounded-lg object-cover"
                                src="{{ $company->logo ? asset('storage/' . $company->logo) : 'https://ui-avatars.com/api/?name=' . $company->name }}"
                                alt="{{ $company->name }}">

                            <div>
                                <p class="font-semibold text-primary">
                                    {{ $company->name }}
                                </p>

                                <p class="text-xs text-on-surface-variant">
                                    ID: {{ $company->id }}
                                </p>
                            </div>

                        </div>

                        <!-- INFO -->
                        <div class="space-y-2 text-sm">

                            <p class="text-on-surface-variant">
                                <span class="text-secondary font-bold">Email:</span>
                                {{ $company->email }}
                            </p>

                            <p class="text-on-surface-variant">
                                <span class="text-secondary font-bold">Teléfono:</span>
                                {{ $company->phone ?? 'Sin teléfono' }}
                            </p>

                            <p class="text-on-surface-variant">
                                <span class="text-secondary font-bold">Dirección:</span>
                                {{ $company->address ?? 'Sin dirección' }}
                            </p>

                            <div>
                                <span
                                    class="inline-block text-xs px-2 py-1 rounded-md
                                        bg-secondary-fixed text-on-secondary-fixed">
                                    {{ $company->state }}
                                </span>
                            </div>

                        </div>

                        <!-- ACCIONES -->
                        <div class="flex justify-end gap-4 mt-4 border-t pt-3">

                            <a
                                href="{{ route('type-companies.show', $company->id) }}"
                                class="text-secondary text-sm">
                                Ver
                            </a>

                            <a
                                href="{{ route('type-companies.edit', $company->id) }}"
                                class="text-primary text-sm">
                                Editar
                            </a>

                            <button
                                onclick="deletecompany({{ $company->id }})"
                                class="text-error text-sm">
                                Eliminar
                            </button>

                        </div>

                    </div>

                    @empty
                    <p class="text-center text-sm text-gray-400">
                        No hay empresas registradas
                    </p>
                    @endforelse

                </div>

                <!-- PAGINACIÓN -->
                <div class="px-6 py-4 border-t border-outline-variant/20">
                    {!! $typeCompanies->withQueryString()->links() !!}
                </div>

            </div>

        </div>

    </main>
</x-app-layout>

<script>
    function deletecompany(id) {
        Swal.fire({
            title: '¿Eliminar este tipo de empresa?',
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
                    await fetch(`/type-companies/${id}`, {
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