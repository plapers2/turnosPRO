<!-- TopNavBar Component -->
<div class="sticky top-0 z-40 w-full px-6 py-3 flex items-center justify-between"
    style="background: linear-gradient(90deg, #f6f3ee 0%, #f1ede8 100%);
            border-bottom: 1px solid #d6c3b3;">

    <!-- Botón menú móvil -->
    <button id="menuBtn" class="md:hidden p-2 rounded-xl transition-colors" style="color: #524438;"
        onmouseenter="this.style.background='#ebe8e2'" onmouseleave="this.style.background='transparent'">
        <span class="material-symbols-outlined">menu</span>
    </button>

    <!-- Breadcrumb -->
    <nav class="hidden md:flex items-center gap-1.5 text-sm">
        @php
            $translations = [
                'dashboard' => 'Dashboard',
                'appointment-manager' => 'Citas',
                'appointment' => 'Citas',
                'appointments' => 'Citas',
                'users' => 'Profesionales',
                'services' => 'Servicios',
                'customers' => 'Clientes',
                'companies' => 'Empresa',
                'type-companies' => 'Tipos de Empresa',
                'opening-hours' => 'Horarios de atención',
                'notification-logs' => 'Notificaciones',
                'profile' => 'Perfil',
            ];

            $combinedTranslations = [
                'appointment/index' => 'Reservar Cita',
                'appointment/history' => 'Historial de Citas',
                'appointments/export' => 'Exportar Citas',
                'profile/settings' => 'Configuración de Perfil',
                // Agrega aquí tus rutas de dos segmentos
            ];

            $rawSegments = collect(request()->segments())->filter(fn($s) => !is_numeric($s));
            $combined = $rawSegments->values()->take(2)->implode('/');

            $segments = isset($combinedTranslations[$combined])
                ? collect([$combinedTranslations[$combined]])
                : $rawSegments->map(fn($s) => $translations[$s] ?? ucfirst(str_replace('-', ' ', $s)));
        @endphp

        <a href="{{ route('dashboard') }}" style="color: #a0714f;">
            <span class="material-symbols-outlined text-[16px]">home</span>
        </a>

        @foreach ($segments as $segment)
            <span style="color: #d6c3b3;">/</span>
            @if ($loop->last)
                <span class="font-semibold" style="color: #1c1c19;">{{ $segment }}</span>
            @else
                <span style="color: #847467;">{{ $segment }}</span>
            @endif
        @endforeach
    </nav>

    <!-- Acciones derecha -->
    <div class="flex items-center gap-2">

        @php
            $nameParts = explode(' ', trim(auth()->user()->name));
            $initials = strtoupper(substr($nameParts[0], 0, 1));
            if (count($nameParts) > 1) {
                $initials .= strtoupper(substr(end($nameParts), 0, 1));
            }
            $userRole = auth()->user()->getRoleNames()->first() ?? 'Usuario';
            $companyId = session('active_company_id');
            $activeCompany = $companyId ? \App\Models\Company::find($companyId) : null;
        @endphp

        <!-- Empresa activa -->
        @if ($activeCompany)
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl"
                style="background: #ffffff; border: 1px solid #d6c3b3;">

                @if ($activeCompany->logo)
                    <img src="{{ asset('storage/' . $activeCompany->logo) }}" alt="{{ $activeCompany->name }}"
                        class="w-5 h-5 rounded object-cover" />
                @else
                    <div class="w-5 h-5 rounded flex items-center justify-center"
                        style="background: linear-gradient(135deg, #854f0b, #663a00);">
                        <span class="material-symbols-outlined text-[13px]" style="color: #ffdcbe;">business</span>
                    </div>
                @endif

                <div class="flex flex-col leading-tight">
                    <span class="text-[10px] font-medium uppercase tracking-widest" style="color: #847467;">
                        Empresa activa
                    </span>
                    <span class="text-xs font-semibold truncate max-w-[120px]" style="color: #1c1c19;">
                        {{ $activeCompany->name }}
                    </span>
                </div>
            </div>

            <!-- Divisor -->
            <div class="hidden sm:block w-px h-6 mx-1" style="background: #d6c3b3;"></div>
        @endif

        <!-- Avatar + Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <div class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl cursor-pointer transition-all duration-200"
                @click="open = !open" onmouseenter="this.style.background='#ebe8e2'"
                onmouseleave="this.style.background='transparent'">

                <!-- Avatar -->
                @if (auth()->user()->image)
                    <img alt="Avatar" class="w-8 h-8 rounded-full object-cover" style="box-shadow: 0 0 0 2px #d6c3b3;"
                        src="{{ asset('storage/' . auth()->user()->image) }}" />
                @else
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold tracking-wide select-none"
                        style="background: linear-gradient(135deg, #854f0b 0%, #663a00 60%, #4a2a00 100%);
                               color: #ffdcbe;
                               box-shadow: 0 0 0 2px #d6c3b3, 0 2px 8px rgba(102,58,0,0.25);
                               flex-shrink: 0;">
                        {{ $initials }}
                    </div>
                @endif

                <!-- Nombre + Rol -->
                <div class="hidden sm:flex flex-col leading-tight">
                    <span class="text-sm font-semibold" style="color: #1c1c19;">
                        {{ auth()->user()->name }}
                    </span>
                    <div class="flex items-center gap-1">
                        <span
                            class="inline-flex items-center px-1.5 rounded-full text-[10px] font-semibold tracking-wide"
                            style="background: #ffdcbe; color: #663a00; line-height: 1.6;">
                            {{ ucfirst($userRole) }}
                        </span>
                        <span class="material-symbols-outlined text-[13px]" style="color: #847467;">expand_more</span>
                    </div>
                </div>
            </div>

            <!-- Dropdown -->
            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                class="absolute right-0 top-12 w-64 rounded-2xl overflow-hidden z-50"
                style="background: #ffffff;
                       border: 1px solid #d6c3b3;
                       box-shadow: 0 8px 24px rgba(102,58,0,0.10), 0 2px 6px rgba(102,58,0,0.06);">

                <!-- Header dropdown -->
                <div class="px-4 py-3" style="border-bottom: 1px solid #ebe8e2;">
                    <div class="flex items-center gap-3">
                        @if (auth()->user()->image)
                            <img alt="Avatar" class="w-10 h-10 rounded-full object-cover"
                                style="box-shadow: 0 0 0 2px #d6c3b3;"
                                src="{{ asset('storage/' . auth()->user()->image) }}" />
                        @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold select-none"
                                style="background: linear-gradient(135deg, #854f0b 0%, #663a00 60%, #4a2a00 100%);
                                       color: #ffdcbe;
                                       box-shadow: 0 0 0 2px #d6c3b3, 0 2px 8px rgba(102,58,0,0.20);
                                       flex-shrink: 0;">
                                {{ $initials }}
                            </div>
                        @endif
                        <div class="flex flex-col min-w-0 gap-0.5">
                            <p class="text-sm font-semibold truncate" style="color: #1c1c19;">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-xs truncate" style="color: #847467;">
                                {{ auth()->user()->email }}
                            </p>
                            <span
                                class="inline-flex items-center self-start px-2 py-0.5 rounded-full text-[10px] font-semibold tracking-wide mt-0.5"
                                style="background: linear-gradient(90deg, #ffdcbe, #ffb870); color: #663a00;">
                                <span class="material-symbols-outlined text-[10px] mr-1">verified</span>
                                {{ ucfirst($userRole) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Empresa activa en dropdown -->
                @if ($activeCompany)
                    <div class="px-4 py-2.5 flex items-center gap-3"
                        style="background: #faf7f2; border-bottom: 1px solid #ebe8e2;">
                        @if ($activeCompany->logo)
                            <img src="{{ asset('storage/' . $activeCompany->logo) }}" alt="{{ $activeCompany->name }}"
                                class="w-8 h-8 rounded-lg object-cover"
                                style="box-shadow: 0 1px 4px rgba(102,58,0,0.15);" />
                        @else
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                style="background: linear-gradient(135deg, #854f0b, #663a00);
                                   box-shadow: 0 1px 4px rgba(102,58,0,0.20);">
                                <span class="material-symbols-outlined text-[16px]"
                                    style="color: #ffdcbe;">business</span>
                            </div>
                        @endif
                        <div class="flex flex-col min-w-0">
                            <span class="text-[10px] font-semibold uppercase tracking-widest" style="color: #847467;">
                                Empresa activa
                            </span>
                            <span class="text-sm font-semibold truncate" style="color: #1c1c19;">
                                {{ $activeCompany->name }}
                            </span>
                            @if ($activeCompany->typeCompany)
                                <span class="text-[10px]" style="color: #847467;">
                                    {{ $activeCompany->typeCompany->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Editar perfil -->
                <a href="{{ route('profile.settings') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors" style="color: #1c1c19;"
                    onmouseenter="this.style.background='#f6f3ee'" onmouseleave="this.style.background='transparent'">
                    <span class="material-symbols-outlined text-[17px]" style="color: #847467;">manage_accounts</span>
                    Editar perfil
                </a>

                <!-- Cerrar sesión -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2.5 mb-1 text-sm transition-colors"
                        style="color: #ba1a1a;" onmouseenter="this.style.background='#fff0f0'"
                        onmouseleave="this.style.background='transparent'">
                        <span class="material-symbols-outlined text-[17px]">logout</span>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
