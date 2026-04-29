<!-- TopNavBar Component -->
<div
    class="sticky top-0 z-40 bg-surface/80 backdrop-blur-md border-b border-surface-container-highest w-full px-8 py-4 flex items-center justify-between">
    <button id="menuBtn" class="md:hidden mr-4 p-2 rounded-lg hover:bg-surface-container-low">
        <span class="material-symbols-outlined">menu</span>
    </button>
    <div class="flex-1 flex items-center">
        <div class="relative w-full max-w-md">
            <span
                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
            <input
                class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-surface-container-highest rounded-full text-sm text-on-surface focus:ring-2 focus:ring-primary focus:border-primary placeholder:text-on-surface-variant transition-all outline-none shadow-sm"
                placeholder="Buscar..." type="text" />
        </div>
    </div>
    <div class="flex items-center gap-4">
        <button
            class="relative p-2 text-on-surface-variant hover:text-primary transition-colors rounded-full hover:bg-surface-container-low">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-error rounded-full border-2 border-surface"></span>
        </button>
        <div class="w-px h-6 bg-surface-container-highest mx-1"></div>
        <div class="flex items-center gap-3 px-2 cursor-pointer group">
            <img alt="Admin Avatar"
                class="w-10 h-10 rounded-full object-cover shadow-sm group-hover:scale-105 transition-transform"
                data-alt="professional portrait of male administrator in soft lighting"
                src="{{ asset('storage/' . auth()->user()->image) }}" />
            <div class="flex flex-col">
                <span
                    class="text-sm font-semibold text-on-surface group-hover:text-primary transition-colors">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="text-xs text-on-surface-variant flex items-center gap-1 hover:text-error transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[14px]">logout</span> Cerrar sesión
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
