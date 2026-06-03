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
            <div
                class="mb-4 flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.change.update') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="password" value="Nueva contraseña" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                    placeholder="Mínimo 8 caracteres" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password') ? [$errors->get('password')[0]] : []" class="mt-1" />
            </div>
            <div>
                <x-input-label for="password_confirmation" value="Confirmar contraseña" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                    class="mt-1 block w-full" placeholder="Repite la contraseña" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
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


            <button type="submit"
                class="w-full py-3 rounded-xl bg-primary text-sm font-semibold text-on-primary
                           shadow-sm hover:opacity-90 transition flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">check</span>
                Establecer contraseña
            </button>
        </form>
    </div>
</x-guest-layout>

<script>
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
