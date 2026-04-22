<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">
        <!-- TopAppBar -->
        <header
            class="bg-[#fcf9f3]/80 backdrop-blur-md text-primary font-['Inter'] text-2xl font-bold tracking-tight h-20 flex items-center flex-col lg:flex-row justify-between px-8 w-full mt-10 mb-2">
            <h2 class="text-2xl font-bold tracking-tight -ml-2">Gestión de Servicios</h2>
            <a href="{{ route('services.create') }}"
                class="bg-primary-container mt-2 w-full lg:w-auto text-center justify-center hover:bg-primary/90 text-white scale-98 transition-transform px-6 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2">
                + Nuevo servicio
            </a>
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
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDF8b42TD59YB4nD4payBUVeTLV4y-ZZ9_xOVivoa-Daph0cuHri8hnfE5JK3pJHnfAkensRL2skGnOuiktrMdDMoUiSp94SG8oS2IWTUCzVv2I5HOhwHLQtJvEJxvDeg1IuDl97bMpmvf-EnHd5okSvVM18DnPgrWLD4gKoYUuepXSmoQAismtiYkvGxW_5RVnQCbDmTProZsq3LItrx87qSl7xo7TJwa2-mmEnrQKvPpdu1erU6epnXo9363bg0UI5YxqzJjp7hAK" />
                        </figure>
                        <div class="p-6 flex flex-col gap-6 flex-1">
                            <div>
                                <h3 class="text-xl font-bold text-primary mb-2 font-headline tracking-tight">Tratamiento
                                    Facial</h3>
                                <p class="text-on-surface-variant text-sm leading-relaxed line-clamp-2">Limpieza
                                    profunda,
                                    extracción de impurezas, mascarilla calmante y masaje facial relajante.</p>
                            </div>
                            <div class="flex flex-wrap gap-3 mt-auto">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-primary-fixed text-on-primary-fixed text-xs font-semibold font-label">
                                    <span class="material-symbols-outlined text-[16px]"
                                        data-icon="schedule">schedule</span>
                                    90 min
                                </span>
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-secondary-fixed text-on-secondary-fixed text-xs font-semibold font-label">
                                    <span class="material-symbols-outlined text-[16px]"
                                        data-icon="payments">payments</span>
                                    $80.000 COP
                                </span>
                            </div>
                            <div class="flex justify-end gap-4 pt-4 mt-2">
                                <button
                                    class="text-sm font-semibold text-primary hover:text-primary-container transition-colors px-2 py-1 rounded">Editar</button>
                                <button
                                    class="text-sm font-semibold text-error hover:text-on-error-container transition-colors px-2 py-1 rounded">Eliminar</button>
                            </div>
                        </div>
                    </article>
                @endforeach

            </div>
        </div>
    </main>
</x-app-layout>
