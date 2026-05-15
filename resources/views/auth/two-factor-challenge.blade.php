<x-guest-layout>
    <div x-data="{ recovery: false }">

        {{-- Header de sección --}}
        <div class="mb-5">
            <div class="flex items-center justify-between mb-1">
                <h2 class="text-lg font-semibold text-on-surface" x-show="!recovery">
                    Ingresa tu código
                </h2>
                <h2 class="text-lg font-semibold text-on-surface" x-show="recovery" x-cloak>
                    Código de recuperación
                </h2>

                <span x-show="!recovery"
                    class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[14px]">smartphone</span>
                    Autenticador
                </span>
                <span x-show="recovery" x-cloak
                    class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full bg-orange-100 text-orange-700">
                    <span class="material-symbols-outlined text-[14px]">key</span>
                    Emergencia
                </span>
            </div>

            <p class="text-sm text-on-surface-variant" x-show="!recovery">
                Abre tu aplicación autenticadora e ingresa el código de 6 dígitos.
            </p>
            <p class="text-sm text-on-surface-variant" x-show="recovery" x-cloak>
                Ingresa uno de los códigos de recuperación que guardaste al activar el 2FA.
            </p>
        </div>

        {{-- Formulario TOTP --}}
        <form x-show="!recovery" method="POST" action="{{ route('two-factor.login.store') }}"
            class="flex flex-col gap-5">
            @csrf

            <div class="flex flex-col gap-1.5">

                {{-- 6 inputs estáticos OTP --}}
                <div class="flex gap-2 justify-center">
                    <input type="text" inputmode="numeric" maxlength="1" id="otp-0"
                        class="w-11 h-14 text-center text-xl font-semibold rounded-xl border border-outline-variant
                               focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none
                               bg-surface-container-lowest text-on-surface transition-all" />
                    <input type="text" inputmode="numeric" maxlength="1" id="otp-1"
                        class="w-11 h-14 text-center text-xl font-semibold rounded-xl border border-outline-variant
                               focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none
                               bg-surface-container-lowest text-on-surface transition-all" />
                    <input type="text" inputmode="numeric" maxlength="1" id="otp-2"
                        class="w-11 h-14 text-center text-xl font-semibold rounded-xl border border-outline-variant
                               focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none
                               bg-surface-container-lowest text-on-surface transition-all" />
                    <input type="text" inputmode="numeric" maxlength="1" id="otp-3"
                        class="w-11 h-14 text-center text-xl font-semibold rounded-xl border border-outline-variant
                               focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none
                               bg-surface-container-lowest text-on-surface transition-all" />
                    <input type="text" inputmode="numeric" maxlength="1" id="otp-4"
                        class="w-11 h-14 text-center text-xl font-semibold rounded-xl border border-outline-variant
                               focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none
                               bg-surface-container-lowest text-on-surface transition-all" />
                    <input type="text" inputmode="numeric" maxlength="1" id="otp-5"
                        class="w-11 h-14 text-center text-xl font-semibold rounded-xl border border-outline-variant
                               focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none
                               bg-surface-container-lowest text-on-surface transition-all" />
                    <input type="hidden" name="code" id="otp-hidden">
                </div>

                @error('code')
                    <p class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2 text-center">
                        {{ $message }}
                    </p>
                @enderror

                <p class="text-xs text-on-surface-variant text-center mt-1">
                    El código se renueva cada 30 segundos
                </p>
            </div>

            <x-primary-button class="w-full justify-center">
                Verificar identidad
                <span class="material-symbols-outlined text-xl">arrow_forward</span>
            </x-primary-button>
        </form>

        {{-- Formulario Recuperación --}}
        <form x-show="recovery" x-cloak method="POST" action="{{ route('two-factor.login.store') }}"
            class="flex flex-col gap-5">
            @csrf

            <div class="flex flex-col gap-1.5">
                <x-input-label for="recovery_code" value="Código de recuperación" />
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px]
                                 text-outline-variant group-focus-within:text-primary transition-colors">key</span>
                    <x-text-input
                        id="recovery_code"
                        type="text"
                        name="recovery_code"
                        class="block w-full font-mono tracking-wider"
                        placeholder="xxxx-xxxx-xxxx"
                        autocomplete="off"
                        spellcheck="false"
                    />
                </div>
                @error('recovery_code')
                    <p class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <x-primary-button class="w-full justify-center">
                Verificar código
                <span class="material-symbols-outlined text-xl">arrow_forward</span>
            </x-primary-button>
        </form>

        {{-- Divider --}}
        <div class="flex items-center gap-3 my-4">
            <div class="flex-1 h-px bg-outline-variant/40"></div>
            <span class="text-xs text-on-surface-variant">o</span>
            <div class="flex-1 h-px bg-outline-variant/40"></div>
        </div>

        {{-- Toggle autenticador / recuperación --}}
        <button @click="recovery = !recovery" type="button"
            class="w-full flex items-center justify-center gap-2 text-sm font-semibold text-primary
                   border border-outline-variant/60 rounded-xl py-2.5 hover:bg-primary/5 transition-colors">
            <span class="material-symbols-outlined text-[18px]" x-show="!recovery">key</span>
            <span class="material-symbols-outlined text-[18px]" x-show="recovery" x-cloak>smartphone</span>
            <span x-show="!recovery">Usar código de recuperación</span>
            <span x-show="recovery" x-cloak>Usar código del autenticador</span>
        </button>

    </div>
</x-guest-layout>

<script>
    // OTP — auto-avance, backspace y paste
    const boxes = [0,1,2,3,4,5].map(i => document.getElementById('otp-' + i));
    const hidden = document.getElementById('otp-hidden');

    function syncHidden() {
        hidden.value = boxes.map(b => b.value).join('');
    }

    boxes.forEach((box, i) => {
        box.addEventListener('input', e => {
            const val = e.target.value.replace(/\D/g, '');
            box.value = val;
            syncHidden();
            if (val && i < 5) boxes[i + 1].focus();
        });

        box.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !box.value && i > 0) {
                boxes[i - 1].value = '';
                boxes[i - 1].focus();
                syncHidden();
            }
        });

        box.addEventListener('paste', e => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/\D/g, '').slice(0, 6);
            paste.split('').forEach((ch, idx) => {
                if (boxes[idx]) boxes[idx].value = ch;
            });
            syncHidden();
            boxes[Math.min(paste.length - 1, 5)]?.focus();
        });
    });

    // Enfocar primer input al cargar
    document.addEventListener('DOMContentLoaded', () => {
        boxes[0]?.focus();
    });
</script>
