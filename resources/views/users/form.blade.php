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
                <x-form.input name="telefono" id="telefono" type="number" :value="old('telefono', $user->phone)"
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
                    @if ($role->name != 'cliente' && $role->name != 'master')
                        <option {{ old('role', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}
                            value="{{ $role->name }}">
                            {{ ucfirst($role->name) }}
                        </option>
                    @endif
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
            <label for="archivo" class="text-xs font-medium text-on-surface-variant">PNG o JPG hasta 10MB</label>
            <input type="file" id="archivo" name="archivo" accept="image/png,image/jpg,image/jpeg"
                class="w-full text-sm text-on-surface-variant file:mr-3 file:py-1.5 file:px-3
            file:rounded-lg file:border-0 file:text-xs file:font-semibold cursor-pointer
            file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition" />
            @error('archivo')
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
    document.getElementById('archivo').addEventListener('change', function(e) {
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
</script>
