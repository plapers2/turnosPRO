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
        <div class="flex items-center gap-3 px-2 cursor-pointer group relative" x-data="{ open: false }">
            <img alt="Admin Avatar"
                class="w-10 h-10 rounded-full object-cover shadow-sm group-hover:scale-105 transition-transform"
                src="{{ asset('storage/' . auth()->user()->image) }}"
                @click="open = !open" />
            <div class="flex flex-col" @click="open = !open">
                <span class="text-sm font-semibold text-on-surface group-hover:text-primary transition-colors">
                    {{ auth()->user()->name }}
                </span>
                <span class="text-xs text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">expand_more</span> Mi cuenta
                </span>
            </div>

            <!-- Dropdown -->
            <div
                x-show="open"
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 top-14 w-52 bg-surface border border-surface-container-highest rounded-2xl shadow-lg z-50 overflow-hidden">
                <!-- Header del dropdown -->
                <div class="px-4 py-3 border-b border-surface-container-highest">
                    <p class="text-sm font-semibold text-on-surface">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-on-surface-variant truncate">{{ auth()->user()->email }}</p>
                </div>

                <!-- Opción editar perfil -->
                @role('cliente')
                <a href="{{ route('customer.profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-on-surface hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-[18px] text-on-surface-variant">manage_accounts</span>
                    Editar perfil
                </a>
                @endrole

                <!-- Separador -->
                <div class="h-px bg-surface-container-highest mx-3"></div>

                <!-- Cerrar sesión -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm text-error hover:bg-error/5 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>