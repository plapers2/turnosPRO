<x-guest-layout>
    <form class="flex flex-col gap-2" method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nombre -->
        <div class="flex flex-col gap-2">
            <x-input-label for="name" :value="__('Nombre')" />
            <div class="relative group">
                <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors"
                    data-icon="person">person</span>
                <x-text-input placeholder="Juan Pérez" id="name" class="block mt-1 w-full" type="text"
                    name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
        </div>

        <!-- Telefono -->
        <div class="mt-4 flex flex-col gap-2">
            <x-input-label for="phone" :value="__('Telefono')" />
            <div class="relative group">
                <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors"
                    data-icon="phone">phone</span>
                <x-text-input placeholder="123 456 7890" id="phone" class="block mt-1 w-full" type="text"
                    name="phone" :value="old('phone')" required autofocus autocomplete="phone" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <!-- Correo -->
        <div class="mt-4 flex flex-col gap-2">
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <div class="relative group">
                <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors"
                    data-icon="mail">mail</span>
                <x-text-input placeholder="usuario@gmail.com" id="email" class="block mt-1 w-full" type="email"
                    name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        <!-- Contraseña -->
        <div class="mt-4 flex flex-col gap-2">
            <x-input-label for="password" :value="__('Contraseña')" />
            <div class="relative group">
                <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors"
                    data-icon="lock">lock</span>
                <x-text-input placeholder="••••••••" id="password" class="block mt-1 w-full" type="password"
                    name="password" required autocomplete="new-password" />
                <button
                    class="absolute right-4 top-1/2 mt-1 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors"
                    type="button"
                    onclick="togglePassword('password', this)">
                    <span class="material-symbols-outlined" data-icon="visibility">visibility</span>
                </button>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
        </div>

        <!-- Confirmar Contraseña -->
        <div class="mt-4 flex flex-col gap-2">
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <div class="relative group">
                <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors"
                    data-icon="lock">lock</span>
                <x-text-input placeholder="••••••••" id="password_confirmation" class="block mt-1 w-full"
                    type="password" name="password_confirmation" required autocomplete="new-password" />
                <button
                    class="absolute right-4 top-1/2 mt-1 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors"
                    type="button"
                    onclick="togglePassword('password_confirmation', this)">
                    <span class="material-symbols-outlined" data-icon="visibility">visibility</span>
                </button>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-between px-1 my-3">
            <a class="text-body-sm text-primary font-semibold hover:underline decoration-primary/30 underline-offset-4"
                href="{{ route('login') }}">
                {{ __('¿Ya estás registrado?') }}
            </a>
        </div>

        <x-primary-button>
            {{ __('Registrarse') }}
            <span class="material-symbols-outlined text-xl" data-icon="arrow_forward">arrow_forward</span>
        </x-primary-button>

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