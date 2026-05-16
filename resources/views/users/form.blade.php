<!-- COLUMNA PRINCIPAL -->
<div class="lg:col-span-2 space-y-8">

    <!-- CARD INFO -->
    <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">

        <div>
            <h2 class="text-lg font-semibold text-primary mb-1">
                Información del usuario
            </h2>
            <p class="text-sm text-on-surface-variant">
                Datos básicos del profesional
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <x-form.field label="Nombre" for="name">
                <x-form.input name="name" id="name" type="text" :value="old('name', $user->name ?? '')" placeholder="Ej. Juan Pérez"
                    class="focus:ring-primary/10 focus:border-primary/40" />
            </x-form.field>

            <x-form.field label="Teléfono" for="phone">
                <x-form.input name="phone" id="phone" type="number" :value="old('phone', $user->phone)"
                    placeholder="Ej. 3001234567" class="focus:ring-primary/10 focus:border-primary/40" />
            </x-form.field>
        </div>

        <x-form.field label="Correo electrónico" for="email">
            <x-form.input name="email" id="email" type="email" :value="old('email', $user->email)" placeholder="ejemplo@email.com"
                class="focus:ring-primary/10 focus:border-primary/40" />
        </x-form.field>
    </div>

    <!-- CARD SEGURIDAD -->
    @if (!isset($user->id) || !$user->password || $errors->hasAny(['password', 'password_confirmation']))
        <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">

            <div>
                <h2 class="text-lg font-semibold text-primary mb-1">Seguridad</h2>
                <p class="text-sm text-on-surface-variant">
                    Mínimo 8 caracteres, mayúscula, minúscula, número y símbolo especial
                </p>
            </div>

            {{-- Aviso explicativo cuando hay error --}}
            @if ($errors->hasAny(['password', 'password_confirmation']))
                <div
                    class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-sm text-amber-700">
                    <span class="material-symbols-outlined text-base mt-0.5 flex-shrink-0">lock_reset</span>
                    <span>Por seguridad, debes volver a ingresar tu contraseña.</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="space-y-2">
                    <x-form.field label="Contraseña" for="password">
                        <x-form.input name="password" id="password" type="password" placeholder="•••••••••"
                            class="focus:ring-secondary/10 focus:border-secondary/40" />
                    </x-form.field>

                    {{-- Barras de fortaleza --}}
                    <div class="flex gap-1.5">
                        <div id="bar-1"
                            class="h-1 flex-1 rounded-full bg-outline-variant/30 transition-all duration-300"></div>
                        <div id="bar-2"
                            class="h-1 flex-1 rounded-full bg-outline-variant/30 transition-all duration-300"></div>
                        <div id="bar-3"
                            class="h-1 flex-1 rounded-full bg-outline-variant/30 transition-all duration-300"></div>
                        <div id="bar-4"
                            class="h-1 flex-1 rounded-full bg-outline-variant/30 transition-all duration-300"></div>
                    </div>
                    <p id="strength-label" class="text-xs font-semibold text-on-surface-variant hidden"></p>

                    {{-- Checklist de requisitos --}}
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

                <div class="space-y-2">
                    <x-form.field label="Confirmar contraseña" for="password_confirmation">
                        <x-form.input name="password_confirmation" id="password_confirmation" type="password"
                            placeholder="•••••••••" class="focus:ring-secondary/10 focus:border-secondary/40" />
                    </x-form.field>

                    {{-- Estado coincidencia --}}
                    <p id="match-msg" class="text-xs font-medium hidden"></p>
                </div>

            </div>

            {{-- Alerta política --}}
            <div id="policy-alert"
                class="hidden flex items-start gap-3 bg-error/5 border border-error/20 rounded-lg px-4 py-3 text-sm text-error">
                <span class="material-symbols-outlined text-base mt-0.5 flex-shrink-0">info</span>
                <span>La contraseña no cumple con la política de seguridad. Completa todos los requisitos antes
                    de guardar.</span>
            </div>

        </div>
    @endif

    {{-- CARD DISPONIBILIDAD --}}
    <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6" x-data
        x-cloak>

        <div>
            <h2 class="text-lg font-semibold text-primary mb-1">Disponibilidad semanal</h2>
            <p class="text-sm text-on-surface-variant">
                Selecciona los días activos y define el horario de atención
            </p>
        </div>
        @include('components.form.disponibilidad', [
            'disponibilidades' => $user->professionalAvailabilities ?? collect(),
            'horariosEmpresa' => $horariosEmpresa,
        ])
    </div>


</div>

<!-- SIDEBAR -->
<div class="space-y-8">

    <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">

        <div>
            <h2 class="text-lg font-semibold text-primary mb-1">Servicios asignados</h2>
            <p class="text-sm text-on-surface-variant">
                Busca y selecciona los servicios que este profesional puede gestionar
            </p>
        </div>

        <select name="services[]" id="services" multiple placeholder="Buscar servicio..." autocomplete="off">
            @forelse ($services as $service)
                @php
                    $selected = in_array($service->id, old('services', $user->services->pluck('id')->toArray() ?? []));
                @endphp
                <option value="{{ $service->id }}" {{ $selected ? 'selected' : '' }}>
                    {{ $service->name }}
                </option>
            @empty
                <option disabled>No hay servicios registrados.</option>
            @endforelse
        </select>

        @error('services')
            <p class="text-xs text-error mt-1">{{ $message }}</p>
        @enderror

    </div>

    <!-- IMAGEN -->
    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
        <h3 class="text-sm font-semibold text-primary">Foto de perfil</h3>

        <div class="flex justify-center">
            <div
                class="w-24 h-24 rounded-xl overflow-hidden border-2 border-outline-variant/20 bg-primary/10 flex items-center justify-center">
                @if (isset($user) && $user->image)
                    <img id="preview" src="{{ asset('storage/' . $user->image) }}"
                        class="w-full h-full object-cover" />
                @else
                    <img id="preview" src="" class="w-full h-full object-cover hidden" />
                    <span id="initials" class="material-symbols-outlined text-3xl text-primary/30">
                        person
                    </span>
                @endif
            </div>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="image" class="text-xs font-medium text-on-surface-variant">PNG o JPG hasta 10MB</label>
            <input type="file" id="image" name="image" accept="image/png,image/jpg,image/jpeg"
                class="w-full text-sm text-on-surface-variant file:mr-3 file:py-1.5 file:px-3
            file:rounded-lg file:border-0 file:text-xs file:font-semibold cursor-pointer
            file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition" />
            @error('image')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- INFO -->
    <div class="bg-primary-container/10 rounded-xl p-6 border border-primary/10 space-y-3">
        <h3 class="text-sm font-semibold text-primary">
            Recomendaciones
        </h3>

        <ul class="text-sm text-on-surface-variant space-y-2">
            <li>• Usa una foto clara del profesional</li>
            <li>• Verifica que el correo sea válido</li>
            <li>• Completa el teléfono correctamente</li>
        </ul>
    </div>


    <!-- BOTONES -->
    <div class="flex justify-end gap-4 pt-4">

        <button type="submit"
            class="px-6 py-2.5 rounded-lg text-sm font-semibold
                       bg-primary text-white hover:bg-primary/90 transition shadow-md hover:shadow-lg">
            Guardar usuario
        </button>

        <a href="{{ route('users.index') }}"
            class="px-5 py-2.5 rounded-lg text-sm font-semibold
                       bg-surface-container hover:bg-surface-container-high transition">
            Cancelar
        </a>

    </div>

</div>
<script>
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
    (function() {
        const pwInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
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

            // met va de 1 a 5, levels tiene índices 0-4
            const lvl = levels[met - 1];

            // bars solo tiene 4 elementos (índices 0-3), nunca acceder a bars[4]
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
