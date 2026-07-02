{{-- resources/views/auth/reset-link-invalid.blade.php --}}
<x-guest-layout>
    <div class="flex flex-col items-center text-center gap-5">
        {{-- Ícono --}}
        <div class="w-16 h-16 rounded-2xl bg-error-container text-on-error-container flex items-center justify-center shadow-sm">
            <span class="material-symbols-outlined text-4xl" style="font-variation-settings:'FILL' 1">
                schedule
            </span>
        </div>

        {{-- Texto --}}
        <div class="flex flex-col gap-1.5">
            <h1 class="text-xl font-semibold text-on-surface">Enlace expirado</h1>
            <p class="text-sm text-on-surface-variant leading-relaxed max-w-[320px]">
                {{ $message }}
            </p>
        </div>

        {{-- Divider sutil --}}
        <div class="w-full h-px bg-outline-variant/40"></div>

        {{-- Acción principal --}}
        <a href="{{ route('password.request') }}"
            class="w-full flex items-center justify-center gap-2 rounded-xl bg-primary text-on-primary font-medium text-sm py-3 px-4 transition hover:opacity-90 active:scale-[0.98]">
            <span class="material-symbols-outlined text-lg">mail</span>
            Solicitar nuevo enlace
        </a>

        {{-- Acción secundaria --}}
        <a href="{{ route('login') }}"
            class="w-full flex items-center justify-center gap-2 rounded-xl bg-surface-container text-on-surface font-medium text-sm py-3 px-4 transition hover:bg-surface-container-high active:scale-[0.98]">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Volver al inicio de sesión
        </a>
    </div>
</x-guest-layout>
