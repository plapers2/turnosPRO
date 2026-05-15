<x-app-layout>
    <x-slot name="header">
        <h2>Autenticación de Dos Factores</h2>
    </x-slot>

    <div class="max-w-xl mx-auto py-8 px-4">

        @if (session('status') == 'two-factor-authentication-enabled')
            <div class="mb-4 p-4 bg-yellow-100 rounded">
                ⚠️ Escanea el QR y luego ingresa el código para confirmar.
            </div>
        @endif

        @if (session('status') == 'two-factor-authentication-confirmed')
            <div class="mb-4 p-4 bg-green-100 rounded">
                ✅ ¡2FA activado correctamente!
            </div>
        @endif

        {{-- ESTADO: 2FA activo --}}
        @if (auth()->user()->two_factor_confirmed_at)

            <p class="mb-4">✅ El 2FA está <strong>activo</strong> en tu cuenta.</p>

            {{-- Códigos de recuperación --}}
            @if ($recoveryCodes)
                <div class="mb-6 p-4 bg-gray-100 rounded">
                    <p class="font-semibold mb-2">Códigos de recuperación (guárdalos en un lugar seguro):</p>
                    @foreach ($recoveryCodes as $code)
                        <code class="block text-sm">{{ $code }}</code>
                    @endforeach
                </div>

                {{-- Regenerar códigos --}}
                <form method="POST" action="/user/two-factor-recovery-codes" class="mb-4">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded">
                        Regenerar códigos
                    </button>
                </form>
            @endif

            {{-- Desactivar --}}
            <form method="POST" action="/user/two-factor-authentication">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded"
                    onclick="return confirm('¿Desactivar 2FA?')">
                    Desactivar 2FA
                </button>
            </form>

            {{-- ESTADO: Secret generado, esperando confirmación --}}
        @elseif(auth()->user()->two_factor_secret)
            <p class="mb-4">Escanea este código QR con tu aplicación autenticadora:</p>

            <div class="mb-4">
                {!! $qrSvg !!}
            </div>

            <p class="mb-4 text-sm text-gray-600">
                ¿No puedes escanear? Ingresa manualmente: <br>
                <code class="font-bold">{{ $secret }}</code>
            </p>

            {{-- Confirmar con código --}}
            <form method="POST" action="/user/confirmed-two-factor-authentication">
                @csrf
                <label class="block mb-1">Código de confirmación:</label>
                <input type="text" name="code" inputmode="numeric" maxlength="6" placeholder="123456"
                    class="border px-3 py-2 rounded w-full mb-3" required />
                @error('code')
                    <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
                @enderror
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
                    Confirmar y activar
                </button>
            </form>

            {{-- ESTADO: 2FA inactivo --}}
        @else
            <p class="mb-4">Protege tu cuenta con autenticación de dos factores.</p>

            <form method="POST" action="/user/two-factor-authentication">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                    Activar 2FA
                </button>
            </form>

        @endif

    </div>
</x-app-layout>     
