<x-guest-layout>
    <form class="flex flex-col gap-5" method="POST" action="{{ route('password.confirm') }}">
        @csrf

        {{-- Título --}}
        <div>
            <h2 class="text-lg font-semibold text-on-surface">Confirmar identidad</h2>
            <p class="text-sm text-on-surface-variant">Esta es una zona segura. Ingresa tu contraseña para continuar.</p>
        </div>

        {{-- Contraseña --}}
        <div class="flex flex-col gap-1.5">
            <x-input-label for="password" :value="__('Contraseña')" />
            <div class="relative group">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">lock</span>
                <x-text-input placeholder="••••••••" id="password" class="block w-full" type="password"
                    name="password" required autocomplete="current-password" />
                <button class="absolute right-3.5 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors"
                    type="button" onclick="togglePassword('password', this)">
                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="px-1" />
        </div>

        {{-- Submit --}}
        <x-primary-button class="w-full justify-center mt-1">
            {{ __('Confirmar') }}
            <span class="material-symbols-outlined text-xl">verified_user</span>
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
