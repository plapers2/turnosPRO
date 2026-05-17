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
                    <span
                        class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">person</span>
                    <x-text-input placeholder="Juan Pérez" id="name" class="block w-full" type="text"
                        name="name" :value="old('name')" required autofocus autocomplete="name" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="px-1" />
            </div>

            <div class="flex flex-col gap-1.5">
                <x-input-label for="phone" :value="__('Teléfono')" />
                <div class="relative group">
                    <span
                        class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">phone</span>
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
                <span
                    class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">mail</span>
                <x-text-input placeholder="usuario@correo.com" id="email" class="block w-full" type="email"
                    name="email" :value="old('email')" required autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="px-1" />
        </div>

        {{-- Fila 3: Contraseña + Confirmar --}}
        <div class="">

            <!-- Nueva contraseña -->
            <div class="space-y-2">
                <x-input-label for="password" :value="__('Contraseña')" />
                <div class="relative group">
                    <span
                        class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">lock</span>
                    <x-text-input placeholder="••••••••" id="password" class="block w-full" type="password"
                        name="password" required autocomplete="new-password" />
                    <button
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors"
                        type="button" onclick="togglePassword('password', this)">
                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password') ? [$errors->get('password')[0]] : []" class="mt-1" />


                <!-- Confirmar contraseña -->
                <div class="space-y-2">
                    <x-input-label for="password_confirmation" :value="__('Confirmar')" />
                    <div class="relative group">
                        <span
                            class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">lock</span>
                        <x-text-input placeholder="••••••••" id="password_confirmation" class="block w-full"
                            type="password" name="password_confirmation" required autocomplete="new-password" />
                        <button
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors"
                            type="button" onclick="togglePassword('password_confirmation', this)">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="px-1" />
                    <p id="match-msg" class="text-xs font-medium hidden"></p>
                </div>

            </div>

            <!-- Barras de fortaleza -->
            <div class="mt-3">
                <div>
                    <h2 class="my-3">Requisitos para la contraseña</h2>
                </div>
                <div class="flex gap-1.5">
                    <div id="bar-1"
                        class="h-1 flex-1 rounded-full bg-outline-variant/30 transition-all duration-300">
                    </div>
                    <div id="bar-2"
                        class="h-1 flex-1 rounded-full bg-outline-variant/30 transition-all duration-300">
                    </div>
                    <div id="bar-3"
                        class="h-1 flex-1 rounded-full bg-outline-variant/30 transition-all duration-300">
                    </div>
                    <div id="bar-4"
                        class="h-1 flex-1 rounded-full bg-outline-variant/30 transition-all duration-300">
                    </div>
                </div>
                <p id="strength-label" class="text-xs font-semibold text-on-surface-variant hidden"></p>

                <!-- Checklist de requisitos -->
                <div class="grid grid-cols-2 gap-y-2 gap-x-3 pt-1">
                    <div id="req-len"
                        class="flex items-center gap-1.5 text-xs text-on-surface-variant transition-colors">
                        <span
                            class="req-dot w-4 h-4 rounded-full border border-outline-variant/40 flex items-center justify-center flex-shrink-0"></span>
                        <span>8 caracteres mínimo</span>
                    </div>
                    <div id="req-upper"
                        class="flex items-center gap-1.5 text-xs text-on-surface-variant transition-colors">
                        <span
                            class="req-dot w-4 h-4 rounded-full border border-outline-variant/40 flex items-center justify-center flex-shrink-0"></span>
                        <span>Mayúscula (A-Z)</span>
                    </div>
                    <div id="req-lower"
                        class="flex items-center gap-1.5 text-xs text-on-surface-variant transition-colors">
                        <span
                            class="req-dot w-4 h-4 rounded-full border border-outline-variant/40 flex items-center justify-center flex-shrink-0"></span>
                        <span>Minúscula (a-z)</span>
                    </div>
                    <div id="req-num"
                        class="flex items-center gap-1.5 text-xs text-on-surface-variant transition-colors">
                        <span
                            class="req-dot w-4 h-4 rounded-full border border-outline-variant/40 flex items-center justify-center flex-shrink-0"></span>
                        <span>Número (0-9)</span>
                    </div>
                    <div id="req-sym"
                        class="flex items-center gap-1.5 text-xs text-on-surface-variant transition-colors">
                        <span
                            class="req-dot w-4 h-4 rounded-full border border-outline-variant/40 flex items-center justify-center flex-shrink-0"></span>
                        <span>Símbolo (!@#...)</span>
                    </div>
                </div>
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
    (function() {
        const pwInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        if (!pwInput) return;

        const bars = [1, 2, 3, 4].map(i => document.getElementById('bar-' + i));
        const label = document.getElementById('strength-label');
        const matchMsg = document.getElementById('match-msg');
        const alertEl = document.getElementById('policy-alert');

        const rules = [{
                id: 'req-len',
                test: v => v.length >= 8
            },
            {
                id: 'req-upper',
                test: v => /[A-Z]/.test(v)
            },
            {
                id: 'req-lower',
                test: v => /[a-z]/.test(v)
            },
            {
                id: 'req-num',
                test: v => /[0-9]/.test(v)
            },
            {
                id: 'req-sym',
                test: v => /[^A-Za-z0-9]/.test(v)
            },
        ];

        const levels = [{
                label: 'Muy débil',
                color: 'text-error',
                barColor: 'bg-error'
            },
            {
                label: 'Débil',
                color: 'text-warning',
                barColor: 'bg-yellow-500'
            },
            {
                label: 'Moderada',
                color: 'text-yellow-500',
                barColor: 'bg-yellow-500'
            },
            {
                label: 'Moderada',
                color: 'text-yellow-500',
                barColor: 'bg-yellow-500'
            },
            {
                label: 'Segura',
                color: 'text-green-500',
                barColor: 'bg-green-500'
            },
        ];

        function evaluate() {
            const val = pwInput.value;
            let met = 0;

            rules.forEach(rule => {
                const ok = val.length > 0 && rule.test(val);
                if (ok) met++;
                const el = document.getElementById(rule.id);
                const dot = el.querySelector('.req-dot');
                if (ok) {
                    el.classList.replace('text-on-surface-variant', 'text-green-500');
                    dot.classList.add('bg-green-500', 'border-green-500');
                    dot.innerHTML =
                        '<span class="material-symbols-outlined text-white" style="font-size:10px;line-height:1">check</span>';
                } else {
                    el.classList.replace('text-green-500', 'text-on-surface-variant');
                    dot.classList.remove('bg-green-500', 'border-green-500');
                    dot.innerHTML = '';
                }
            });

            bars.forEach(b => {
                b.className =
                    'h-1 flex-1 rounded-full bg-outline-variant/30 transition-all duration-300';
            });

            if (val.length === 0) {
                label.classList.add('hidden');
                alertEl.classList.add('hidden');
                return;
            }

            const lvl = levels[met - 1];
            const barsToFill = Math.min(met, 4);
            for (let i = 0; i < barsToFill; i++) {
                bars[i].classList.replace('bg-outline-variant/30', lvl.barColor);
            }

            label.classList.remove('hidden');
            label.className = 'text-xs font-semibold ' + lvl.color;
            label.textContent = lvl.label;

            alertEl.classList.toggle('hidden', met === 5);
        }

        function checkMatch() {
            const a = pwInput.value,
                b = confirmInput.value;
            if (!b) {
                matchMsg.classList.add('hidden');
                return;
            }
            matchMsg.classList.remove('hidden');
            if (a === b) {
                matchMsg.className = 'text-xs font-medium text-green-500';
                matchMsg.textContent = '✓ Las contraseñas coinciden';
            } else {
                matchMsg.className = 'text-xs font-medium text-error';
                matchMsg.textContent = '✗ Las contraseñas no coinciden';
            }
        }

        pwInput.addEventListener('input', () => {
            evaluate();
            checkMatch();
        });
        confirmInput.addEventListener('input', checkMatch);

        // Toggle contraseña
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('.material-symbols-outlined');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.textContent = input.type === 'password' ? 'visibility' : 'visibility_off';
        }
        window.togglePassword = togglePassword;
    })();
</script>
