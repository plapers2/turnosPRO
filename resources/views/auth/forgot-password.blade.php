<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Olvidaste tú contraseña? No hay problema.
        Lo unico que tienes que hacer es ingresar tú direccion de email y nostros te enviaremos un correo con un link para restablecer tú contraseña.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="flex flex-col gap-2">
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative group">
                <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors"
                    data-icon="mail">mail</span>
                <x-text-input placeholder="usuario@gmail.com" id="email" class="block mt-1 w-full" type="email"
                    name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Enviar instrucciones') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
