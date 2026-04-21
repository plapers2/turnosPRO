<x-app-layout>
    <div class="max-w-7xl mx-auto px-8 py-8 flex flex-col gap-12">
        <!-- Greeting Header -->
        <header class="flex flex-col gap-2">
            <h1 class="text-4xl font-extrabold tracking-tight text-on-surface font-headline"
                style="letter-spacing: -0.02em;">
                Buenos días, Admin
            </h1>
            <p class="text-lg text-on-surface-variant font-body">
                Lunes 20 de abril — Resumen de operaciones
            </p>
        </header>
        <!-- Content Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left Column: KPIs & Next Appointments -->
            <div class="lg:col-span-8 flex flex-col gap-10">
                <!-- KPI Bento Grid -->
                <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- KPI Card 1 -->
                    <div class="bg-surface-container-lowest p-6 rounded-xl flex flex-col justify-between"
                        style="box-shadow: 0 10px 30px rgba(95, 94, 90, 0.04);">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-on-surface-variant tracking-wide uppercase">Citas
                                Hoy</span>
                            <span
                                class="material-symbols-outlined text-primary-container bg-surface-container p-2 rounded-full text-sm">calendar_today</span>
                        </div>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-bold text-on-surface">12</span>
                        </div>
                    </div>
                    <!-- KPI Card 2 -->
                    <div class="bg-surface-container-lowest p-6 rounded-xl flex flex-col justify-between"
                        style="box-shadow: 0 10px 30px rgba(95, 94, 90, 0.04);">
                        <div class="flex items-center justify-between mb-4">
                            <span
                                class="text-sm font-semibold text-on-surface-variant tracking-wide uppercase">Confirmadas</span>
                            <span
                                class="material-symbols-outlined text-tertiary-container bg-surface-container p-2 rounded-full text-sm">check_circle</span>
                        </div>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-bold text-on-surface">8</span>
                        </div>
                    </div>
                    <!-- KPI Card 3 -->
                    <div class="bg-surface-container-lowest p-6 rounded-xl flex flex-col justify-between"
                        style="box-shadow: 0 10px 30px rgba(95, 94, 90, 0.04);">
                        <div class="flex items-center justify-between mb-4">
                            <span
                                class="text-sm font-semibold text-on-surface-variant tracking-wide uppercase">Canceladas</span>
                            <span
                                class="material-symbols-outlined text-error bg-error-container p-2 rounded-full text-sm">cancel</span>
                        </div>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-bold text-error">2</span>
                        </div>
                    </div>
                    <!-- KPI Card 4 -->
                    <div class="bg-surface-container-lowest p-6 rounded-xl flex flex-col justify-between relative overflow-hidden"
                        style="box-shadow: 0 10px 30px rgba(95, 94, 90, 0.04);">
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-primary-fixed-dim rounded-full blur-3xl opacity-20 -mr-8 -mt-8">
                        </div>
                        <div class="flex items-center justify-between mb-4 relative z-10">
                            <span
                                class="text-sm font-semibold text-on-surface-variant tracking-wide uppercase">Asistencia</span>
                            <span
                                class="material-symbols-outlined text-primary-container bg-surface-container p-2 rounded-full text-sm">trending_up</span>
                        </div>
                        <div class="flex items-end gap-2 relative z-10">
                            <span class="text-4xl font-bold text-primary-container">83%</span>
                        </div>
                    </div>
                </section>
                <!-- Next Appointments Section -->
                <section class="flex flex-col gap-6 mt-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-on-surface font-headline tracking-tight">Próximas citas</h2>
                        <span
                            class="text-sm font-medium text-on-surface-variant bg-surface-container-low px-3 py-1 rounded-full">Siguiente
                            2 horas</span>
                    </div>
                    <div class="flex flex-col gap-4">
                        <!-- Appointment Card 1 -->
                        <div class="bg-surface-container-lowest p-5 rounded-xl flex items-center justify-between group hover:bg-surface-container-low transition-colors duration-300 cursor-pointer"
                            style="box-shadow: 0 10px 30px rgba(95, 94, 90, 0.02);">
                            <div class="flex items-center gap-5">
                                <div
                                    class="flex flex-col items-center justify-center bg-surface-container px-4 py-2 rounded-lg min-w-[80px]">
                                    <span
                                        class="text-sm font-semibold text-on-surface-variant uppercase tracking-wider">Hoy</span>
                                    <span class="text-xl font-bold text-primary-container">10:30</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="text-lg font-bold text-on-surface">María Gómez</span>
                                    <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                        <span class="flex items-center gap-1"><span
                                                class="material-symbols-outlined text-[16px]">medical_services</span>
                                            Consulta General</span>
                                        <span class="text-surface-dim">•</span>
                                        <span class="flex items-center gap-1"><span
                                                class="material-symbols-outlined text-[16px]">person</span> Dr.
                                            Pérez</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold bg-tertiary-fixed text-on-tertiary-fixed-variant flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary-container animate-pulse"></span>
                                    Confirmada
                                </span>
                                <button
                                    class="opacity-0 group-hover:opacity-100 transition-opacity p-2 text-on-surface-variant hover:text-primary bg-surface rounded-full">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>
                            </div>
                        </div>
                        <!-- Appointment Card 2 -->
                        <div class="bg-surface-container-lowest p-5 rounded-xl flex items-center justify-between group hover:bg-surface-container-low transition-colors duration-300 cursor-pointer relative overflow-hidden"
                            style="box-shadow: 0 10px 30px rgba(95, 94, 90, 0.02);">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary-container"></div>
                            <div class="flex items-center gap-5 pl-2">
                                <div
                                    class="flex flex-col items-center justify-center bg-surface-container px-4 py-2 rounded-lg min-w-[80px]">
                                    <span
                                        class="text-sm font-semibold text-on-surface-variant uppercase tracking-wider">Hoy</span>
                                    <span class="text-xl font-bold text-primary-container">11:00</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="text-lg font-bold text-on-surface">Carlos Rodríguez</span>
                                    <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                        <span class="flex items-center gap-1"><span
                                                class="material-symbols-outlined text-[16px]">content_cut</span> Corte
                                            Premium</span>
                                        <span class="text-surface-dim">•</span>
                                        <span class="flex items-center gap-1"><span
                                                class="material-symbols-outlined text-[16px]">person</span> Ana
                                            L.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold bg-surface-container-high text-on-surface-variant flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-outline"></span> Pendiente
                                </span>
                                <button
                                    class="opacity-0 group-hover:opacity-100 transition-opacity p-2 text-on-surface-variant hover:text-primary bg-surface rounded-full">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>
                            </div>
                        </div>
                        <!-- Appointment Card 3 -->
                        <div class="bg-surface-container-lowest p-5 rounded-xl flex items-center justify-between group hover:bg-surface-container-low transition-colors duration-300 cursor-pointer"
                            style="box-shadow: 0 10px 30px rgba(95, 94, 90, 0.02);">
                            <div class="flex items-center gap-5">
                                <div
                                    class="flex flex-col items-center justify-center bg-surface-container px-4 py-2 rounded-lg min-w-[80px]">
                                    <span
                                        class="text-sm font-semibold text-on-surface-variant uppercase tracking-wider">Hoy</span>
                                    <span class="text-xl font-bold text-primary-container">11:45</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="text-lg font-bold text-on-surface">Elena Silva</span>
                                    <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                                        <span class="flex items-center gap-1"><span
                                                class="material-symbols-outlined text-[16px]">spa</span> Masaje
                                            Relajante</span>
                                        <span class="text-surface-dim">•</span>
                                        <span class="flex items-center gap-1"><span
                                                class="material-symbols-outlined text-[16px]">person</span> Marta
                                            M.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold bg-tertiary-fixed text-on-tertiary-fixed-variant flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary-container"></span> Confirmada
                                </span>
                                <button
                                    class="opacity-0 group-hover:opacity-100 transition-opacity p-2 text-on-surface-variant hover:text-primary bg-surface rounded-full">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- Right Column: Chart Placeholder & Actions -->
            <div class="lg:col-span-4 flex flex-col gap-8">
                <!-- Chart Widget -->
                <div class="bg-surface-container-low p-6 rounded-2xl flex flex-col h-full min-h-[400px]">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-on-surface tracking-tight">Ocupación esta semana</h3>
                        <button class="text-on-surface-variant hover:text-primary transition-colors">
                            <span class="material-symbols-outlined">calendar_month</span>
                        </button>
                    </div>
                    <!-- Chart Placeholder Visuals -->
                    <div class="flex-1 flex items-end gap-3 pt-8 pb-2 relative">
                        <!-- Background horizontal lines -->
                        <div class="absolute inset-0 flex flex-col justify-between py-2 z-0">
                            <div class="border-b border-surface-container-highest w-full h-0"></div>
                            <div class="border-b border-surface-container-highest w-full h-0"></div>
                            <div class="border-b border-surface-container-highest w-full h-0"></div>
                            <div class="border-b border-surface-container-highest w-full h-0"></div>
                        </div>
                        <!-- Bars -->
                        <div class="w-full flex justify-between items-end h-full z-10 px-2">
                            <div
                                class="w-8 bg-surface-container-highest rounded-t-md h-[40%] group relative hover:bg-primary-container transition-colors duration-300">
                            </div>
                            <div
                                class="w-8 bg-surface-container-highest rounded-t-md h-[70%] group relative hover:bg-primary-container transition-colors duration-300">
                            </div>
                            <div class="w-8 bg-primary-container rounded-t-md h-[85%] relative shadow-sm">
                                <!-- Tooltip for current day -->
                                <div
                                    class="absolute -top-10 left-1/2 -translate-x-1/2 bg-inverse-surface text-inverse-on-surface text-xs py-1 px-2 rounded font-medium whitespace-nowrap">
                                    85% Hoy
                                </div>
                            </div>
                            <div
                                class="w-8 bg-surface-container-highest rounded-t-md h-[60%] group relative hover:bg-primary-container transition-colors duration-300">
                            </div>
                            <div
                                class="w-8 bg-surface-container-highest rounded-t-md h-[30%] group relative hover:bg-primary-container transition-colors duration-300">
                            </div>
                        </div>
                    </div>
                    <!-- X Axis Labels -->
                    <div
                        class="flex justify-between items-center px-2 mt-4 text-xs font-semibold text-on-surface-variant uppercase">
                        <span>L</span>
                        <span>M</span>
                        <span class="text-primary-container">M</span>
                        <span>J</span>
                        <span>V</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
