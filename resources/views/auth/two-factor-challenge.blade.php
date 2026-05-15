<x-guest-layout>
    <div x-data="{ recovery: false }">

        {{-- Código TOTP --}}
        <div x-show="!recovery">
            <p>Ingresa el código de tu aplicación autenticadora.</p>
            <form method="POST" action="/two-factor-challenge">
                @csrf
                <div>
                    <label for="code">Código</label>
                    <input id="code" type="text" name="code" inputmode="numeric"
                           autofocus autocomplete="one-time-code" maxlength="6" />
                    @error('code')
                        <span>{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit">Verificar</button>
            </form>
        </div>

        {{-- Código de recuperación --}}
        <div x-show="recovery">
            <p>Ingresa uno de tus códigos de recuperación de emergencia.</p>
            <form method="POST" action="/two-factor-challenge">
                @csrf
                <div>
                    <label for="recovery_code">Código de recuperación</label>
                    <input id="recovery_code" type="text" name="recovery_code" />
                    @error('recovery_code')
                        <span>{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit">Verificar</button>
            </form>
        </div>

        <button @click="recovery = !recovery" type="button">
            <span x-show="!recovery">Usar código de recuperación</span>
            <span x-show="recovery">Usar código del autenticador</span>
        </button>

    </div>
</x-guest-layout>
