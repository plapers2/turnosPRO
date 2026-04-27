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
                <x-form.input name="nombre" id="nombre" type="text" :value="old('nombre', $user->name ?? '')" placeholder="Ej. Juan Pérez"
                    class="focus:ring-primary/10 focus:border-primary/40" />
            </x-form.field>

            <x-form.field label="Teléfono" for="phone">
                <x-form.input name="telefono" id="telefono" type="text" :value="old('telefono', $user->phone)"
                    placeholder="Ej. 3001234567" class="focus:ring-primary/10 focus:border-primary/40" />
            </x-form.field>
        </div>

        <x-form.field label="Correo electrónico" for="email">
            <x-form.input name="email" id="email" type="email" :value="old('email', $user->email)" placeholder="ejemplo@email.com"
                class="focus:ring-primary/10 focus:border-primary/40" />
        </x-form.field>

        <x-form.field label="Rol del Usuario" for="roles_id">
            <x-form.select name="role" id="role">
                @forelse ($roles as $role)
                    <option {{ old('role', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}
                        value="{{ $role->name }}">
                        {{ ucfirst($role->name) }}
                    </option>
                @empty
                    <option value="">No hay ningún rol en el sistema</option>
                @endforelse
            </x-form.select>

        </x-form.field>

    </div>

    <!-- CARD SEGURIDAD -->
    @if (!$user->password)
        <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">

            <h2 class="text-lg font-semibold text-primary">
                Seguridad
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <x-form.field label="Contraseña" for="password">
                    <x-form.input name="password" id="password" type="password" placeholder="•••••••••"
                        class="focus:ring-secondary/10 focus:border-secondary/40" />
                </x-form.field>

                <x-form.field label="Confirmar contraseña" for="password_confirmation">
                    <x-form.input name="password_confirmation" id="password_confirmation" type="password"
                        placeholder="•••••••••" class="focus:ring-secondary/10 focus:border-secondary/40" />
                </x-form.field>

            </div>

        </div>
    @endif

    {{-- CARD DISPONIBILIDAD --}}
    {{-- CARD DISPONIBILIDAD — error general si no se seleccionó ningún día --}}
    <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6" x-data
        x-cloak>

        <div>
            <h2 class="text-lg font-semibold text-primary mb-1">Disponibilidad semanal</h2>
            <p class="text-sm text-on-surface-variant">
                Selecciona los días activos y define el horario de atención
            </p>
        </div>

        @if (
            $errors->hasAny([
                'disponibilidad',
                'disponibilidad.*',
                'disponibilidad.*.hora_inicio',
                'disponibilidad.*.hora_fin',
                'disponibilidad.*.dia_semana',
            ]))
            <div class="flex items-start gap-2 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-red-700">Revisa los horarios</p>
                    @foreach ($errors->get('disponibilidad.*') as $fieldErrors)
                        @foreach ($fieldErrors as $msg)
                            <p class="text-xs text-red-600 mt-0.5">{{ $msg }}</p>
                        @endforeach
                    @endforeach
                    @error('disponibilidad')
                        <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif

        @include('components.form.disponibilidad', [
            'disponibilidades' => $user->disponibilidades ?? collect(),
            'horariosEmpresa' => $horariosEmpresa,
        ])
    </div>

    <!-- BOTONES -->
    <div class="flex justify-end gap-4 pt-4">

        <a href="{{ route('users.index') }}"
            class="px-5 py-2.5 rounded-lg text-sm font-semibold
                       bg-surface-container hover:bg-surface-container-high transition">
            Cancelar
        </a>

        <button type="submit"
            class="px-6 py-2.5 rounded-lg text-sm font-semibold
                       bg-primary text-white hover:bg-primary/90 transition shadow-md hover:shadow-lg">
            Guardar usuario
        </button>

    </div>

</div>

<!-- SIDEBAR -->
<div class="space-y-8">

    <!-- IMAGEN -->
    <div class="bg-surface-container rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">

        <h3 class="text-md font-semibold text-primary">
            Foto de perfil
        </h3>

        <x-form.input-file name="archivo" id="archivo" />

        <img id="preview" src="{{ $user->image ? asset('storage/' . $user->image) : '' }}"
            class="{{ isset($user) ? '' : 'hidden' }} w-full h-40 object-cover rounded-lg border border-outline-variant/20">
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

</div>
<script>
    const input = document.getElementById('archivo');
    const preview = document.getElementById('preview');
    const placeholder = document.getElementById('placeholder');

    input.addEventListener('change', e => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = () => {
            preview.src = reader.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
</script>
