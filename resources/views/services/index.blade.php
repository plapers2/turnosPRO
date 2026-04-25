<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <!-- HEADER-->
        <x-header-admin
        icono="service_toolbox"
        titulo="Gestion de Servicios"
        mensaje="Administra, edita y organiza los servicios de tú negocio"
        textoBoton="Nuevo Servicio" />

        <!-- Canvas -->
        <div class="p-8 pb-20">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="serviceCard">
                <!-- Service Card -->
                @forelse ($services as $service)
                    <article data-id="{{ $service->id }}"
                        class="bg-surface-container-lowest rounded-xl flex flex-col shadow-[0_10px_30px_rgba(95,94,90,0.06)] transition-all hover:-translate-y-1 duration-300">
                        <figure class="w-full h-48 overflow-hidden rounded-t-xl">
                            <img alt="Tratamiento Facial" class="w-full h-full object-cover"
                                src="{{ asset('storage/' . $service->image) }}" />
                        </figure>
                        <div class="p-6 flex flex-col gap-6 flex-1">
                            <div>
                                <h3 class="text-xl font-bold text-primary mb-2 font-headline tracking-tight">
                                    {{ $service->name }}</h3>
                                <p class="text-on-surface-variant text-sm leading-relaxed line-clamp-2">
                                    {{ $service->description }}</p>
                            </div>
                            <div class="flex flex-wrap gap-3 mt-auto">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-primary-fixed text-on-primary-fixed text-xs font-semibold font-label">
                                    <span class="material-symbols-outlined text-[16px]"
                                        data-icon="schedule">schedule</span>
                                    {{ $service->duration }} Min
                                </span>
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-secondary-fixed text-on-secondary-fixed text-xs font-semibold font-label">
                                    <span class="material-symbols-outlined text-[16px]"
                                        data-icon="payments">payments</span>
                                    {{ $service->price }}
                                </span>
                            </div>
                            <div class="flex justify-end gap-4 pt-4 mt-2">
                                <a href="{{ route('services.edit', $service->id) }}"
                                    class="text-sm font-semibold text-primary hover:text-primary-container transition-colors px-2 py-1 rounded">Editar</a>
                                <button id="btnEliminar" onclick="deleteService({{ $service->id }})"
                                    class="text-sm font-semibold text-error hover:text-on-error-container transition-colors px-2 py-1 rounded btnEliminar">Eliminar</button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div
                        class="col-span-full flex flex-col items-center justify-center text-center py-20 px-6
            bg-surface-container-lowest rounded-xl border border-outline-variant/20
            shadow-[0_10px_30px_rgba(95,94,90,0.04)] animate-fadeIn">

                        <!-- ICONO -->
                        <div
                            class="w-16 h-16 flex items-center justify-center rounded-full
                bg-primary/10 text-primary mb-6">
                            <span class="material-symbols-outlined">
                                service_toolbox
                            </span>
                        </div>

                        <!-- TITULO -->
                        <h3 class="text-xl font-semibold text-primary mb-2">
                            Aún no tienes servicios
                        </h3>

                        <!-- DESCRIPCIÓN -->
                        <p class="text-sm text-on-surface-variant max-w-md mb-6 leading-relaxed">
                            Empieza creando tu primer servicio para que puedas gestionarlo, asignarlo y mostrarlo a tus
                            clientes.
                        </p>

                        <!-- BOTÓN CTA -->
                        <a href="{{ route('services.create') }}"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold
              bg-primary text-white hover:bg-primary/90 transition shadow-sm hover:shadow-md">

                            <span class="material-symbols-outlined text-[18px]">add</span>
                            Crear primer servicio
                        </a>

                    </div>
                @endempty



        </div>
    </div>
</main>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function deleteService(id) {

        Swal.fire({
            title: '¿Eliminar servicio?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ba1a1a', // color error
            cancelButtonColor: '#847467', // outline
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            background: '#fcf9f3',
            color: '#1c1c19',
            customClass: {
                popup: 'rounded-xl shadow-lg',
                confirmButton: 'px-4 py-2 rounded-lg font-semibold',
                cancelButton: 'px-4 py-2 rounded-lg'
            },
        }).then(async (result) => {

            if (result.isConfirmed) {

                try {
                    const response = await fetch(`/services/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {

                        // animación elegante
                        const card = document.querySelector(`[data-id="${id}"]`);
                        if (card) {
                            card.style.transition = 'all 0.3s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.95)';

                            setTimeout(() => {
                                card.remove();
                            }, 300);
                        }

                        Swal.fire({
                            title: 'Eliminado',
                            text: 'El servicio fue eliminado correctamente',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#fcf9f3',
                            color: '#1c1c19'
                        });

                    } else {
                        throw new Error();
                    }

                } catch (error) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo eliminar' + error,
                        icon: 'error',
                        background: '#fcf9f3',
                        color: '#1c1c19'
                    });
                }

            }

        });
    }
</script>
