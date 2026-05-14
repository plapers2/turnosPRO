<x-app-layout>
    <main class="flex-1 flex flex-col relative h-full overflow-y-auto bg-surface">

        <!-- HEADER -->
        <header class="relative bg-[#fcf9f3]/80 backdrop-blur-md border border-outline-variant/20
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
                        <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-primary mb-1">Información personal</h2>
                                <p class="text-sm text-on-surface-variant">Datos básicos de tu cuenta</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex flex-col gap-1.5">
                                    <label for="name" class="text-sm font-medium text-on-surface">Nombre completo</label>
                                    <input id="name" name="name" type="text"
                                        value="{{ old('name', $cliente?->name) }}"
                                        placeholder="Ej. Juan Pérez"
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
                                        value="{{ old('phone', $cliente?->phone) }}"
                                        placeholder="Ej. 3001234567"
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
                                <input type="email" value="{{ $cliente?->email }}" disabled
                                    class="w-full px-4 py-2.5 rounded-lg border border-outline-variant/20
                                    bg-surface-container text-sm text-on-surface-variant cursor-not-allowed" />
                                <p class="text-xs text-on-surface-variant">El correo no es editable.</p>
                            </div>
                        </div>

                        <!-- CARD SEGURIDAD -->
                        <div class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 shadow-sm space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold text-primary mb-1">Seguridad</h2>
                                <p class="text-sm text-on-surface-variant">
                                    Completa estos campos solo si deseas cambiar tu contraseña
                                </p>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="current_password" class="text-sm font-medium text-on-surface">Contraseña actual</label>
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
                                <div class="flex flex-col gap-1.5">
                                    <label for="new_password" class="text-sm font-medium text-on-surface">Nueva contraseña</label>
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
                                    @error('new_password')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex flex-col gap-1.5">
                                    <label for="new_password_confirmation" class="text-sm font-medium text-on-surface">Confirmar nueva contraseña</label>
                                    <div class="relative">
                                        <input id="new_password_confirmation" name="new_password_confirmation" type="password"
                                            placeholder="•••••••••"
                                            class="w-full px-4 py-2.5 pr-10 rounded-lg border border-outline-variant/30
                    bg-surface text-sm text-on-surface
                    focus:outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition" />
                                        <button type="button" onclick="togglePassword('new_password_confirmation', this)"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>
                                    </div>
                                    @error('new_password_confirmation')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- SIDEBAR -->
                    <div class="space-y-8">

                        <!-- MENSAJES -->
                        @if(session('success'))
                        <div class="flex items-start gap-3 px-4 py-3.5 rounded-xl bg-green-50 border border-green-200 text-green-800">
                            <span class="material-symbols-outlined text-green-500 text-[20px] mt-0.5">check_circle</span>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="flex items-start gap-3 px-4 py-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800">
                            <span class="material-symbols-outlined text-red-500 text-[20px] mt-0.5">warning</span>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                        @endif
                        <!-- FOTO DE PERFIL -->
                        <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 shadow-sm space-y-4">
                            <h3 class="text-sm font-semibold text-primary">Foto de perfil</h3>

                            <!-- Preview actual -->
                            <div class="flex justify-center">
                                <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-outline-variant/20 bg-primary/10 flex items-center justify-center">
                                    @if($cliente->image)
                                    <img id="preview" src="{{ asset('storage/' . $cliente->image) }}"
                                        class="w-full h-full object-cover" />
                                    @else
                                    <img id="preview" src="" class="w-full h-full object-cover hidden" />
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
                                <input type="file" id="image" name="image" accept="image/png,image/jpg,image/jpeg"
                                    class="w-full text-sm text-on-surface-variant file:mr-3 file:py-1.5 file:px-3
            file:rounded-lg file:border-0 file:text-xs file:font-semibold
            file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition" />
                                @error('image')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <!-- INFO -->
                        <div class="bg-primary-container/10 rounded-xl p-6 border border-primary/10 space-y-3">
                            <h3 class="text-sm font-semibold text-primary">Recomendaciones</h3>
                            <ul class="text-sm text-on-surface-variant space-y-2">
                                <li>• El correo electrónico no es editable</li>
                                <li>• La contraseña debe tener mínimo 8 caracteres</li>
                                <li>• Deja los campos de contraseña vacíos si no deseas cambiarla</li>
                            </ul>
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
</script>