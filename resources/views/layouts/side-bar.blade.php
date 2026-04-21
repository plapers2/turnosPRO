<!-- SideNavBar -->
<aside id="sidebar"
    class="fixed top-0 left-0 h-screen w-64 bg-[#f6f3ee] border-r border-stone-200/20 z-[60]
           transform -translate-x-full md:translate-x-0
           transition-transform duration-300">
    <!-- Brand Header -->
    <div class="px-6 py-8 flex flex-col gap-1">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-3xl text-[#854F0B]"
                style="font-variation-settings: 'FILL' 1;">calendar_month</span>
            <span class="text-xl font-bold tracking-tighter text-[#854F0B]">TurnosPRO</span>
        </div>
        <p class="text-xs text-on-surface-variant ml-10">Gestión de Turnos</p>
    </div>
    <!-- Navigation Links -->
    <div class="p-4 gap-2 flex flex-col h-full overflow-y-auto">
        <!-- Active Tab -->
        <a class="flex items-center gap-4 px-4 py-3 bg-white text-[#854F0B] font-semibold rounded-lg shadow-sm text-sm tracking-wide Inter"
            href="#">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
            Dashboard
        </a>
        <a class="flex items-center gap-4 px-4 py-3 text-stone-600 hover:bg-[#fcf9f3] transition-all duration-300 font-medium text-sm tracking-wide Inter hover:translate-x-1 rounded-lg"
            href="#">
            <span class="material-symbols-outlined">calendar_month</span>
            Citas
        </a>
        <a class="flex items-center gap-4 px-4 py-3 text-stone-600 hover:bg-[#fcf9f3] transition-all duration-300 font-medium text-sm tracking-wide Inter hover:translate-x-1 rounded-lg"
            href="#">
            <span class="material-symbols-outlined">medical_services</span>
            Servicios
        </a>
        <a class="flex items-center gap-4 px-4 py-3 text-stone-600 hover:bg-[#fcf9f3] transition-all duration-300 font-medium text-sm tracking-wide Inter hover:translate-x-1 rounded-lg"
            href="#">
            <span class="material-symbols-outlined">group</span>
            Profesionales
        </a>
        <a class="flex items-center gap-4 px-4 py-3 text-stone-600 hover:bg-[#fcf9f3] transition-all duration-300 font-medium text-sm tracking-wide Inter hover:translate-x-1 rounded-lg"
            href="#">
            <span class="material-symbols-outlined">person</span>
            Clientes
        </a>
        <a class="flex items-center gap-4 px-4 py-3 text-stone-600 hover:bg-[#fcf9f3] transition-all duration-300 font-medium text-sm tracking-wide Inter hover:translate-x-1 rounded-lg"
            href="#">
            <span class="material-symbols-outlined">settings</span>
            Configuración
        </a>
    </div>
    <!-- Bottom User / CTA -->
    <div class="p-4 mt-auto border-t border-stone-200/20">
        <button
            class="w-full flex items-center justify-center gap-2 bg-primary-container text-on-primary-container py-3 rounded-lg font-semibold hover:scale-[0.98] transition-transform duration-200">
            <span class="material-symbols-outlined text-sm">add</span>
            Nueva Cita
        </button>
        <div class="mt-6 flex items-center gap-3 px-2 cursor-pointer group">
            <img alt="Admin Avatar"
                class="w-10 h-10 rounded-full object-cover shadow-sm group-hover:scale-105 transition-transform"
                data-alt="professional portrait of male administrator in soft lighting"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDLWYEj_C0m-SUbpl8eco4NCcYiwDTQZAIHna4GfNOuzEwQhSpYCRRioJjNZKFWY6vnXv5_IrgEZlWqEd1fS528d6zZGq_2ReK_aWID6n482hrvwLxSicpQpVWG8XRlpL2PNkJwpBdgc2WaAd0k2Xv89P5CetVWISRSuBE1PRJtoMSbdWLE5aGqSIxcNxn2uex8OKAzs2tB-SNf_a5Yb5hv5jgdkJ77zybgANcRIhdkId24eTLLE6GYKknKRvxGONDyi3GbEM8O2dwH" />
            <div class="flex flex-col">
                <span class="text-sm font-semibold text-on-surface group-hover:text-primary transition-colors">Admin
                    Usuario</span>
                <span
                    class="text-xs text-on-surface-variant flex items-center gap-1 hover:text-error transition-colors">
                    <span class="material-symbols-outlined text-[14px]">logout</span> Cerrar sesión
                </span>
            </div>
        </div>
    </div>
</aside>
<!-- Overlay (AQUÍ VA) -->
<div id="overlay"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden md:hidden z-50 transition-all duration-300">
</div>
