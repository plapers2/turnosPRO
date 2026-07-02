{{-- resources/views/auth/invitation-invalid.blade.php --}}
<x-guest-layout>
    <div class="flex flex-col items-center text-center gap-5">
        {{-- Ícono --}}
        <div class="w-16 h-16 rounded-2xl bg-error-container text-on-error-container flex items-center justify-center shadow-sm">
            <span class="material-symbols-outlined text-4xl" style="font-variation-settings:'FILL' 1">
                link_off
            </span>
        </div>

        {{-- Texto --}}
        <div class="flex flex-col gap-1.5">
            <h1 class="text-xl font-semibold text-on-surface">Enlace no válido</h1>
            <p class="text-sm text-on-surface-variant leading-relaxed max-w-[320px]">
                {{ $message }}
            </p>
        </div>

        {{-- Divider sutil --}}
        <div class="w-full h-px bg-outline-variant/40"></div>

        {{-- Acción --}}
        <a href="{{ route('login') }}"
            class="w-full flex items-center justify-center gap-2 rounded-xl bg-primary text-on-primary font-medium text-sm py-3 px-4 transition hover:opacity-90 active:scale-[0.98]">
            <span class="material-symbols-outlined text-lg">home</span>
            Volver al inicio
        </a>

        {{-- Ayuda secundaria --}}
        <p class="text-xs text-on-surface-variant">
            ¿Necesitas una nueva invitación? Contacta al administrador de tu empresa.
        </p>
    </div>
</x-guest-layout>
