<x-app-layout>
    <main class="flex-1 flex flex-col min-h-0 overflow-y-auto bg-surface">

        <!-- HEADER-->
        <x-header-admin icono="service_toolbox" titulo="Gestion de Servicios"
            mensaje="Administra, edita y organiza los servicios de tú negocio"
            mensajeEmpleado="Visualiza los servicios que brindas en esta empresa" textoBoton="Nuevo Servicio"
            ruta="services" />

        {{-- FILTROS --}}
        @livewire('services.service-index')

    </main>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    async function restoreService(button, id) {

        const original = button.innerHTML;

        button.innerHTML = `
        <span class="animate-spin inline-block w-5 h-5 border-2 border-current border-t-transparent rounded-full"></span>
    `;
        button.disabled = true;

        try {
            const response = await fetch(`/services/${id}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                location.reload();
            } else {
                throw new Error();
            }

            console.log(response)
        } catch (error) {
            button.innerHTML = original;
            button.disabled = false;
        }
    }

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
                        // const card = document.querySelector(`[data-id="${id}"]`);
                        // if (card) {
                        //     card.style.transition = 'all 0.3s ease';
                        //     card.style.opacity = '0';
                        //     card.style.transform = 'scale(0.95)';

                        //     setTimeout(() => {
                        //         card.remove();
                        //     }, 300);
                        // }

                        Swal.fire({
                            title: 'Eliminado',
                            text: 'El servicio fue eliminado correctamente',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#fcf9f3',
                            color: '#1c1c19'
                        }).then(() => {
                            location.reload()
                        })

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
