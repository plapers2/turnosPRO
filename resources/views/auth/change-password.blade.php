<x-guest-layout>
    <div class="max-w-md w-full mx-auto px-6 py-10">

        <div class="flex flex-col items-center gap-2 mb-8">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-[24px]">lock_reset</span>
            </div>
            <h1 class="text-xl font-bold text-on-surface tracking-tight">Cambia tu contraseña</h1>
            <p class="text-sm text-on-surface-variant text-center">
                Por seguridad, debes establecer una contraseña personal antes de continuar.
            </p>
        </div>

        @if (session('success'))
        <div class="mb-4 flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <span class="material-symbols-outlined text-[16px]">check_circle</span>
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.change.update') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="password" value="Nueva contraseña" />
                <x-text-input id="password" name="password" type="password"
                    class="mt-1 block w-full" placeholder="Mínimo 8 caracteres" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirmar contraseña" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                    class="mt-1 block w-full" placeholder="Repite la contraseña" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            <button type="submit"
                class="w-full py-3 rounded-xl bg-primary text-sm font-semibold text-on-primary
                           shadow-sm hover:opacity-90 transition flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check</span>
                Establecer contraseña
            </button>
        </form>
    </div>
</x-guest-layout>