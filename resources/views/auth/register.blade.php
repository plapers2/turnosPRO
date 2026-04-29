<x-guest-layout>
    <form class="flex flex-col gap-5" method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Título --}}
        <div>
            <h2 class="text-lg font-semibold text-on-surface">Crear una cuenta</h2>
            <p class="text-sm text-on-surface-variant">Completa los datos para registrarte</p>
        </div>

        {{-- Fila 1: Nombre + Teléfono --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
                <x-input-label for="name" :value="__('Nombre')" />
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">person</span>
                    <x-text-input placeholder="Juan Pérez" id="name" class="block w-full" type="text"
                        name="name" :value="old('name')" required autofocus autocomplete="name" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="px-1" />
            </div>

            <div class="flex flex-col gap-1.5">
                <x-input-label for="phone" :value="__('Teléfono')" />
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">phone</span>
                    <x-text-input placeholder="123 456 7890" id="phone" class="block w-full" type="tel"
                        name="phone" :value="old('phone')" required autocomplete="tel" />
                </div>
                <x-input-error :messages="$errors->get('phone')" class="px-1" />
            </div>
        </div>

        {{-- Fila 2: Correo (ancho completo) --}}
        <div class="flex flex-col gap-1.5">
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <div class="relative group">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">mail</span>
                <x-text-input placeholder="usuario@correo.com" id="email" class="block w-full" type="email"
                    name="email" :value="old('email')" required autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="px-1" />
        </div>

        {{-- Fila 3: Contraseña + Confirmar --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
                <x-input-label for="password" :value="__('Contraseña')" />
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">lock</span>
                    <x-text-input placeholder="••••••••" id="password" class="block w-full" type="password"
                        name="password" required autocomplete="new-password" />
                    <button class="absolute right-3.5 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors"
                        type="button" onclick="togglePassword('password', this)">
                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="px-1" />
            </div>

            <div class="flex flex-col gap-1.5">
                <x-input-label for="password_confirmation" :value="__('Confirmar')" />
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">lock</span>
                    <x-text-input placeholder="••••••••" id="password_confirmation" class="block w-full"
                        type="password" name="password_confirmation" required autocomplete="new-password" />
                    <button class="absolute right-3.5 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors"
                        type="button" onclick="togglePassword('password_confirmation', this)">
                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="px-1" />
            </div>
        </div>

        {{-- Submit --}}
        <x-primary-button class="w-full justify-center mt-1">
            {{ __('Crear cuenta') }}
            <span class="material-symbols-outlined text-xl">arrow_forward</span>
        </x-primary-button>

        {{-- Divider + Login --}}
        <div class="flex items-center gap-3">
            <div class="flex-1 h-px bg-outline-variant/40"></div>
            <span class="text-xs text-on-surface-variant">¿Ya tienes cuenta?</span>
            <div class="flex-1 h-px bg-outline-variant/40"></div>
        </div>
        <a href="{{ route('login') }}"
            class="text-center text-sm text-primary font-semibold hover:underline underline-offset-4 decoration-primary/30">
            Iniciar sesión
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
