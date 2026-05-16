<x-app-layout>
    <main class="flex-1 flex flex-col min-h-0 overflow-y-auto bg-surface">

        <!-- HEADER -->
        <header
            class="relative bg-[#fcf9f3]/80 backdrop-blur-md border border-outline-variant/20
            rounded-xl mx-8 mt-10 mb-4 px-6 py-5 flex flex-col lg:flex-row
            items-start lg:items-center justify-between gap-4 shadow-[0_8px_20px_rgba(95,94,90,0.04)]">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">manage_accounts</span>
                    </div>
                    <h2 class="text-xl font-bold text-primary tracking-tight">
                        Editar perfil
                    </h2>
                </div>
                <p class="text-sm text-on-surface-variant ml-13">
                    Actualiza tu información personal
                </p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-on-surface-variant
                hover:text-primary transition-colors px-3 py-2 rounded-lg hover:bg-primary/5">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Volver al dashboard
            </a>
        </header>

        <!-- CANVAS -->
        <div class="px-8 pb-12">
            <form method="POST" action="{{ route('profile.settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- COLUMNA PRINCIPAL -->
                    <div class="lg:col-span-2 space-y-8">

                        <!-- CARD INFO -->
                        <div
                            class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-primary mb-1">Información personal</h2>
                                <p class="text-sm text-on-surface-variant">Datos básicos de tu cuenta</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col gap-1.5">
                                    <label for="name" class="text-sm font-medium text-on-surface">Nombre
                                        completo</label>
                                    <input id="name" name="name" type="text"
                                        value="{{ old('name', $cliente?->name) }}" placeholder="Ej. Juan Pérez"
                                        class="w-full px-4 py-2.5 rounded-lg border border-outline-variant/30
                                        bg-surface text-sm text-on-surface
                                        focus:outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition" />
                                    @error('name')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex flex-col gap-1.5">
                                    <label for="phone" class="text-sm font-medium text-on-surface">Teléfono</label>
                                    <input id="phone" name="phone" type="number"
                                        value="{{ old('phone', $cliente?->phone) }}" placeholder="Ej. 3001234567"
                                        class="w-full px-4 py-2.5 rounded-lg border border-outline-variant/30
                                        bg-surface text-sm text-on-surface
                                        focus:outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition" />
                                    @error('phone')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-sm font-medium text-on-surface">Correo electrónico</label>
                                <input name="email" type="email" value="{{ $cliente?->email }}"
                                    class="w-full px-4 py-2.5 rounded-lg border border-outline-variant/30
                                    bg-surface-container text-sm text-on-surface-variant" />
                                @error('email')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- CARD SEGURIDAD -->
                        <div
                            class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-primary mb-1">Seguridad</h2>
                                <p class="text-sm text-on-surface-variant">
                                    Completa estos campos solo si deseas cambiar tu contraseña
                                </p>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="current_password" class="text-sm font-medium text-on-surface">Contraseña
                                    actual</label>
                                <div class="relative">
                                    <input id="current_password" name="current_password" type="password"
                                        placeholder="•••••••••"
                                        class="w-full px-4 py-2.5 pr-10 rounded-lg border border-outline-variant/30
                bg-surface text-sm text-on-surface
                focus:outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition" />
                                    <button type="button" onclick="togglePassword('current_password', this)"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </button>
                                </div>
                                @error('current_password')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <!-- Nueva contraseña -->
                                <div class="space-y-2">
                                    <label for="new_password" class="text-sm font-medium text-on-surface">Nueva
                                        contraseña</label>
                                    <div class="relative">
                                        <input id="new_password" name="new_password" type="password"
                                            placeholder="•••••••••"
                                            class="w-full px-4 py-2.5 pr-10 rounded-lg border border-outline-variant/30
                    bg-surface text-sm text-on-surface
                    focus:outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition" />
                                        <button type="button" onclick="togglePassword('new_password', this)"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>
                                    </div>

                                    <!-- Barras de fortaleza -->
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
                                    <p id="strength-label" class="text-xs font-semibold text-on-surface-variant hidden">
                                    </p>

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

                                    @error('new_password')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirmar contraseña -->
                                <div class="space-y-2">
                                    <label for="new_password_confirmation"
                                        class="text-sm font-medium text-on-surface">Confirmar nueva contraseña</label>
                                    <div class="relative">
                                        <input id="new_password_confirmation" name="new_password_confirmation"
                                            type="password" placeholder="•••••••••"
                                            class="w-full px-4 py-2.5 pr-10 rounded-lg border border-outline-variant/30
                    bg-surface text-sm text-on-surface
                    focus:outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition" />
                                        <button type="button"
                                            onclick="togglePassword('new_password_confirmation', this)"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>
                                    </div>
                                    <p id="match-msg" class="text-xs font-medium hidden"></p>

                                    @error('new_password_confirmation')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>

                            <!-- Alerta política -->
                            <div id="policy-alert"
                                class="hidden flex items-start gap-3 bg-error/5 border border-error/20 rounded-lg px-4 py-3 text-sm text-error">
                                <span class="material-symbols-outlined text-base mt-0.5 flex-shrink-0">info</span>
                                <span>La contraseña no cumple con la política de seguridad. Completa todos los
                                    requisitos antes de guardar.</span>
                            </div>
                        </div>

                    </div>

                    <!-- SIDEBAR -->
                    <div class="space-y-8">


                        <!-- 2FA -->
                        <a href="{{ route('two-factor.setup') }}"
                            class="group flex items-center justify-between gap-3 p-4 rounded-xl border transition-all cursor-pointer
    {{ auth()->user()->two_factor_confirmed_at
        ? 'bg-green-50 border-green-200 hover:bg-green-100'
        : 'bg-red-50 border-red-200 hover:bg-red-100' }}">

                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
            {{ auth()->user()->two_factor_confirmed_at ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}">
                                    <span class="material-symbols-outlined text-[18px]"
                                        style="font-variation-settings:'FILL' 1">
                                        {{ auth()->user()->two_factor_confirmed_at ? 'verified_user' : 'gpp_bad' }}
                                    </span>
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-semibold
                {{ auth()->user()->two_factor_confirmed_at ? 'text-green-800' : 'text-red-800' }}">
                                        Autenticación 2FA
                                    </p>
                                    <p
                                        class="text-xs mt-0.5
                {{ auth()->user()->two_factor_confirmed_at ? 'text-green-700' : 'text-red-700' }}">
                                        {{ auth()->user()->two_factor_confirmed_at ? 'Activa · Cuenta protegida' : 'Inactiva · Solo contraseña' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full
            {{ auth()->user()->two_factor_confirmed_at ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                    {{ auth()->user()->two_factor_confirmed_at ? 'ON' : 'OFF' }}
                                </span>
                                <span
                                    class="material-symbols-outlined text-[16px] transition-transform group-hover:translate-x-0.5
            {{ auth()->user()->two_factor_confirmed_at ? 'text-green-600' : 'text-red-500' }}">
                                    chevron_right
                                </span>
                            </div>

                        </a>

                        @if (session('error'))
                            <div
                                class="flex items-start gap-3 px-4 py-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800">
                                <span class="material-symbols-outlined text-red-500 text-[20px] mt-0.5">warning</span>
                                <p class="text-sm">{{ session('error') }}</p>
                            </div>
                        @endif

                        <!-- FOTO DE PERFIL -->
                        <div
                            class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
                            <h3 class="text-sm font-semibold text-primary">Foto de perfil</h3>

                            <!-- Preview actual -->
                            <div class="flex justify-center">
                                <div
                                    class="w-24 h-24 rounded-full overflow-hidden border-2 border-outline-variant/20 bg-primary/10 flex items-center justify-center">
                                    @if ($cliente->image)
                                        <img id="preview" src="{{ asset('storage/' . $cliente->image) }}"
                                            class="w-full h-full object-cover" />
                                    @else
                                        <img id="preview" src=""
                                            class="w-full h-full object-cover hidden" />
                                        <span id="initials" class="text-2xl font-bold text-primary/50">
                                            {{ strtoupper(substr($cliente->name, 0, 2)) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Input -->
                            <div class="flex flex-col gap-1.5">
                                <label for="image" class="text-xs font-medium text-on-surface-variant">
                                    PNG o JPG hasta 10MB
                                </label>
                                <input type="file" id="image" name="image"
                                    accept="image/png,image/jpg,image/jpeg"
                                    class="w-full text-sm text-on-surface-variant file:mr-3 file:py-1.5 file:px-3
            file:rounded-lg file:border-0 file:text-xs file:font-semibold
            file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition" />
                                @error('image')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>


                        <!-- BOTONES -->
                        <div class="flex flex-col gap-3">
                            <button type="submit"
                                class="w-full px-6 py-2.5 rounded-lg text-sm font-semibold
                                bg-primary text-white hover:bg-primary/90 transition shadow-md hover:shadow-lg">
                                Guardar cambios
                            </button>
                            <a href="{{ route('dashboard') }}"
                                class="w-full text-center px-5 py-2.5 rounded-lg text-sm font-semibold
                                bg-surface-container hover:bg-surface-container-high transition">
                                Cancelar
                            </a>
                        </div>

                    </div>
                </div>
            </form>
        </div>

    </main>
</x-app-layout>
<script>
    // Preview de imagen
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = () => {
            const preview = document.getElementById('preview');
            const initials = document.getElementById('initials');
            preview.src = reader.result;
            preview.classList.remove('hidden');
            if (initials) initials.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });

    // Toggle contraseña
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('.material-symbols-outlined');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }


    // Password strength & match (nueva contraseña)
    (function() {
        const pwInput = document.getElementById('new_password');
        const confirmInput = document.getElementById('new_password_confirmation');
        if (!pwInput) return;

        const bars = [1, 2, 3, 4].map(i => document.getElementById('bar-' + i));
        const label = document.getElementById('strength-label');
        const matchMsg = document.getElementById('match-msg');
        const alert = document.getElementById('policy-alert');

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
                b.className = 'h-1 flex-1 rounded-full bg-outline-variant/30 transition-all duration-300';
            });

            if (val.length === 0) {
                label.classList.add('hidden');
                alert.classList.add('hidden');
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

            alert.classList.toggle('hidden', met === 5);
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
    })();
</script>
