<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <x-header-admin icono="picture_as_pdf" titulo="Exportar Citas" mensaje="Genera un reporte PDF de las citas según los filtros aplicados" />

        <div class="px-8 pb-20">
            <div class="max-w-2xl mx-auto mt-6">

                <!-- CARD FILTROS -->
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm overflow-hidden">

                    <div class="px-6 py-5 border-b border-outline-variant/20 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-[20px]">tune</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-on-surface">Filtros del reporte</h3>
                            <p class="text-xs text-on-surface-variant">Todos los filtros son opcionales</p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('appointments.export-pdf') }}" class="p-6 space-y-5">

                        <!-- Rango de fechas -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide">
                                    Desde
                                </label>
                                <input type="date" name="desde" id="desde"
                                    class="px-3 py-2 rounded-lg border border-outline-variant/30 bg-surface text-sm text-on-surface
                focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide">
                                    Hasta
                                </label>
                                <input type="date" name="hasta" id="hasta"
                                    class="px-3 py-2 rounded-lg border border-outline-variant/30 bg-surface text-sm text-on-surface
                focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide">
                                Estado
                            </label>
                            <select name="status"
                                class="px-3 py-2 rounded-lg border border-outline-variant/30 bg-surface text-sm text-on-surface
                                    focus:outline-none focus:border-primary transition">
                                <option value="">Todos los estados</option>
                                <option value="confirmed">Confirmada</option>
                                <option value="completed">Completada</option>
                                <option value="cancelled">Cancelada</option>
                            </select>
                        </div>

                        <!-- Modo de color -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide">
                                Modo de impresión
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center gap-3 px-4 py-3 rounded-lg border border-outline-variant/30
                       bg-surface cursor-pointer hover:bg-surface-container transition has-[:checked]:border-primary
                       has-[:checked]:bg-primary/5">
                                    <input type="radio" name="modo" value="color" checked class="accent-primary">
                                    <div>
                                        <p class="text-sm font-semibold text-on-surface">A color</p>
                                        <p class="text-xs text-on-surface-variant">Con colores y fondos</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 px-4 py-3 rounded-lg border border-outline-variant/30
                       bg-surface cursor-pointer hover:bg-surface-container transition has-[:checked]:border-primary
                       has-[:checked]:bg-primary/5">
                                    <input type="radio" name="modo" value="bw" class="accent-primary">
                                    <div>
                                        <p class="text-sm font-semibold text-on-surface">Blanco y negro</p>
                                        <p class="text-xs text-on-surface-variant">Bajo consumo de tinta</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex items-start gap-3 bg-primary/5 border border-primary/20 rounded-lg p-4">
                            <span class="material-symbols-outlined text-primary text-[18px] mt-0.5 flex-shrink-0">info</span>
                            <p class="text-xs text-on-surface-variant leading-relaxed">
                                El PDF incluirá todas las citas de <strong class="text-on-surface">{{ session('active_company_name') }}</strong> que coincidan con los filtros.
                                Si no seleccionas ningún filtro, se exportarán <strong class="text-on-surface">todas las citas</strong>.
                            </p>
                        </div>

                        <!-- Botón -->
                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold
                                    bg-primary text-white hover:bg-primary/90 transition shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">download</span>
                                Descargar PDF
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const desde = document.getElementById('desde');
                const hasta = document.getElementById('hasta');
                const btn = document.querySelector('button[type="submit"]');

                desde.addEventListener('change', () => {
                    hasta.min = desde.value;
                    if (hasta.value && hasta.value < desde.value) {
                        hasta.value = '';
                    }
                });

                hasta.addEventListener('change', () => {
                    if (desde.value && hasta.value < desde.value) {
                        hasta.value = '';
                        alert('La fecha "Hasta" no puede ser anterior a "Desde".');
                    }
                });
            });
        </script>
    </main>
</x-app-layout>