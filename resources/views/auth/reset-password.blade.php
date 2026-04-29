<x-guest-layout>
    <form class="flex flex-col gap-4" method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-2">
            <div
                class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-content-center flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-xl">lock_reset</span>
            </div>
            <div>
                <p class="text-body-md font-semibold text-on-surface">Nueva contraseña</p>
                <p class="text-body-sm text-on-surface-variant">Elige una contraseña segura</p>
            </div>
        </div>

        {{-- Token --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div class="flex flex-col gap-2">
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <div class="relative group">
                <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors">mail</span>
                <x-text-input placeholder="usuario@gmail.com" id="email" class="block mt-1 w-full" type="email"
                    name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        {{-- Password --}}
        <div class="flex flex-col gap-2">
            <x-input-label for="password" :value="__('Contraseña')" />
            <div class="relative group">
                <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors">lock</span>
                <x-text-input placeholder="••••••••" id="password" class="block mt-1 w-full" type="password"
                    name="password" required autocomplete="new-password" />
                <button
                    class="absolute right-4 top-1/2 mt-1 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors"
                    type="button" onclick="togglePassword('password', this)">
                    <span class="material-symbols-outlined">visibility</span>
                </button>
            </div>
            {{-- Strength bar --}}
            <div class="h-0.5 rounded-full bg-outline-variant/30 overflow-hidden mt-1">
                <div id="strength-bar" class="h-full w-0 transition-all duration-300 rounded-full"></div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        {{-- Confirm Password --}}
        <div class="flex flex-col gap-2">
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <div class="relative group">
                <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors">shield</span>
                <x-text-input placeholder="••••••••" id="password_confirmation" class="block mt-1 w-full"
                    type="password" name="password_confirmation" required autocomplete="new-password" />
                <button
                    class="absolute right-4 top-1/2 mt-1 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors"
                    type="button" onclick="togglePassword('password_confirmation', this)">
                    <span class="material-symbols-outlined">visibility</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <x-primary-button class="mt-2">
            {{ __('Restablecer contraseña') }}
            <span class="material-symbols-outlined text-xl">arrow_forward</span>
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
