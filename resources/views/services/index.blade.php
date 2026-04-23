<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">
        <!-- TopAppBar -->
        <header
            class="bg-[#fcf9f3]/80 backdrop-blur-md text-primary font-['Inter'] text-2xl font-bold tracking-tight h-20 flex items-center flex-col lg:flex-row justify-between px-8 w-full mt-10 mb-2">
            <h2 class="text-2xl font-bold tracking-tight -ml-2">Gestión de Servicios</h2>
            <button
                class="bg-primary-container mt-2 w-full lg:w-auto text-center justify-center hover:bg-primary/90 text-white scale-98 transition-transform px-6 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2"
                onclick="modalForm.showModal()">
                + Nuevo servicio
            </button>
       <x-modal-form id="modalForm" title="Nuevo Servicio">

    <form class="space-y-8">

        <x-form.field label="Nombre del servicio" for="nombre">
            <x-form.input id="nombre" placeholder="Ej. Manicura Spa" />
        </x-form.field>

        <x-form.field label="Descripción" for="descripcion">
            <x-form.textarea id="descripcion" placeholder="Detalla los beneficios..." />
        </x-form.field>

        <div class="grid grid-cols-2 gap-6">

            <x-form.field label="Duración" for="duracion">
                <x-form.select id="duracion">
                    <option value="15">15 min</option>
                    <option value="30">30 min</option>
                    <option value="45" selected>45 min</option>
                    <option value="60">60 min</option>
                </x-form.select>
            </x-form.field>

            <x-form.field label="Precio ref. (COP)" for="precio">
                <x-form.input id="precio" placeholder="0" />
            </x-form.field>

        </div>

    </form>

    <x-slot name="actions">
        <x-button variant="secondary">
            Guardar
        </x-button>

        <form method="dialog">
            <x-button  variant="error">
                Cerrar
            </x-button>
        </form>
    </x-slot>

</x-modal-form>
        </header>
        <!-- Canvas -->
        <div class="p-8 pb-20">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service Card -->
                @foreach ($services as $service)
                    <article
                        class="bg-surface-container-lowest rounded-xl flex flex-col shadow-[0_10px_30px_rgba(95,94,90,0.06)] transition-all hover:-translate-y-1 duration-300">
                        <figure class="w-full h-48 overflow-hidden rounded-t-xl">
                            <img alt="Tratamiento Facial" class="w-full h-full object-cover"
                                src='{{ $service->image }}' />
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
                                    {{ $service->duration }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-secondary-fixed text-on-secondary-fixed text-xs font-semibold font-label">
                                    <span class="material-symbols-outlined text-[16px]"
                                        data-icon="payments">payments</span>
                                    {{ $service->price }}
                                </span>
                            </div>
                            <div class="flex justify-end gap-4 pt-4 mt-2">
                                <button
                                    class="text-sm font-semibold text-primary hover:text-primary-container transition-colors px-2 py-1 rounded"
                                    wire:click="edit({{ $service->id }})">Editar</button>
                                <button
                                    class="text-sm font-semibold text-error hover:text-on-error-container transition-colors px-2 py-1 rounded"
                                    wire:click='delete({{ $service->id }})'>Eliminar</button>
                            </div>
                        </div>
                    </article>
                @endforeach

            </div>
        </div>
    </main>
</x-app-layout>
