<x-app-layout>
    <div class="flex flex-col items-center justify-center min-h-[60vh] px-6 text-center gap-6">
        <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-[36px]">store_off</span>
        </div>
        <div class="flex flex-col gap-2 max-w-sm">
            <h2 class="text-lg font-semibold text-on-surface">No tienes empresas asignadas</h2>
            <p class="text-sm text-on-surface-variant">Para reservar una cita tienes dos opciones:</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full max-w-lg">
            <div class="bg-surface-container-lowest border border-outline-variant/20 rounded-2xl p-5 flex flex-col gap-3 text-left">
                <span class="material-symbols-outlined text-primary text-[28px]">mail</span>
                <h3 class="text-sm font-semibold text-on-surface">Pide una invitación</h3>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Solicita al administrador de tu empresa favorita que te envíe un enlace de invitación a nuestro sistema.
                </p>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant/20 rounded-2xl p-5 flex flex-col gap-3 text-left">
                <span class="material-symbols-outlined text-primary text-[28px]">workspace_premium</span>
                <h3 class="text-sm font-semibold text-on-surface">Plan Premium</h3>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Accede a todo el catálogo de empresas del sistema sin necesitar invitación.
                </p>
                <a href="#" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-primary/90 transition self-start">
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    Ver planes
                </a>
            </div>
        </div>
    </div>
</x-app-layout>