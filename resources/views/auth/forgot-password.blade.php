<x-guest-layout>
    <x-auth-session-status class="mb-2" :status="session('status')" />

    <form class="flex flex-col gap-5" method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- Título --}}
        <div>
            <h2 class="text-lg font-semibold text-on-surface">Recuperar contraseña</h2>
            <p class="text-sm text-on-surface-variant">Te enviaremos un enlace a tu correo electrónico</p>
        </div>

        {{-- Email --}}
        <div class="flex flex-col gap-1.5">
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <div class="relative group">
                <span
                    class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">mail</span>
                <x-text-input placeholder="usuario@correo.com" id="email" class="block w-full" type="email"
                    name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="px-1" />
        </div>

        {{-- Submit --}}
        <x-primary-button class="w-full justify-center mt-1">
            {{ __('Enviar instrucciones') }}
            <span class="material-symbols-outlined text-xl">send</span>
        </x-primary-button>

        {{-- Divider + Login --}}
        <div class="flex items-center gap-3">
            <div class="flex-1 h-px bg-outline-variant/40"></div>
            <span class="text-xs text-on-surface-variant">¿Recordaste tu contraseña?</span>
            <div class="flex-1 h-px bg-outline-variant/40"></div>
        </div>
        <a href="{{ route('login') }}"
            class="text-center text-sm text-primary font-semibold hover:underline underline-offset-4 decoration-primary/30">
            Iniciar sesión
        </a>

    </form>
</x-guest-layout>
