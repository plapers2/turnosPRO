<x-guest-layout>
    @if (session('error'))
    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        {{ session('error') }}
    </div>
    @endif
    <x-auth-session-status class="mb-2" :status="session('status')" />

    <form class="flex flex-col gap-5" method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Título de sección --}}
        <div class="mb-1">
            <h2 class="text-lg font-semibold text-on-surface">Bienvenido de nuevo</h2>
            <p class="text-sm text-on-surface-variant">Ingresa tus credenciales para continuar</p>
        </div>

        <!-- Email -->
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

        <!-- Password -->
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Contraseña')" />
                @if (Route::has('password.request'))
                <a class="text-xs text-primary font-medium hover:underline underline-offset-4"
                    href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
                @endif
            </div>
            <div class="relative group">
                <span
                    class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">lock</span>
                <x-text-input placeholder="••••••••" id="password" class="block w-full" type="password" name="password"
                    required autocomplete="current-password" />
                <button
                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors"
                    type="button" onclick="togglePassword('password', this)">
                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="px-1" />
        </div>

        <!-- Submit -->
        <x-primary-button class="w-full justify-center mt-1">
            {{ __('Ingresar') }}
            <span class="material-symbols-outlined text-xl">arrow_forward</span>
        </x-primary-button>

        <!-- Divider + Register -->
        <div class="flex items-center gap-3">
            <div class="flex-1 h-px bg-outline-variant/40"></div>
            <span class="text-xs text-on-surface-variant">¿No tienes cuenta?</span>
            <div class="flex-1 h-px bg-outline-variant/40"></div>
        </div>
        <a href="{{ route('register') }}"
            class="text-center text-sm text-primary font-semibold hover:underline underline-offset-4 decoration-primary/30">
            Crear una cuenta
        </a>
    </form>
</x-guest-layout>

<script>
    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon = btn.querySelector('.material-symbols-outlined');
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        icon.textContent = isPassword ? 'visibility_off' : 'visibility';
    }
</script>