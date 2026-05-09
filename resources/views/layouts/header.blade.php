<div class="sticky top-0 z-40 w-full flex items-center h-[60px] px-5 gap-3"
     style="background: rgba(250,247,244,0.98); backdrop-filter: blur(12px); border-bottom: 1px solid #e8ddd5;">

    {{-- Botón menú móvil --}}
    <button id="menuBtn"
        class="md:hidden w-[34px] h-[34px] rounded-[10px] flex items-center justify-center transition-colors"
        style="background: #f0e8e1; border: 1px solid #e4d5c9; color: #9c7e6b;">
        <span class="material-symbols-outlined text-[20px]">menu</span>
    </button>

    {{-- Breadcrumb --}}
    <nav class="hidden md:flex items-center gap-2 flex-1">
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
            ];
            $rawSegments = collect(request()->segments())->filter(fn($s) => !is_numeric($s));
            $combined = $rawSegments->values()->take(2)->implode('/');
            $segments = isset($combinedTranslations[$combined])
                ? collect([$combinedTranslations[$combined]])
                : $rawSegments->map(fn($s) => $translations[$s] ?? ucfirst(str_replace('-', ' ', $s)));
        @endphp

        <a href="{{ route('dashboard') }}"
            class="w-[30px] h-[30px] rounded-[8px] flex items-center justify-center transition-all"
            style="background: #f0e8e1; color: #9c7e6b;"
            onmouseover="this.style.background='#e4d5c9'; this.style.color='#6b4e3d'"
            onmouseout="this.style.background='#f0e8e1'; this.style.color='#9c7e6b'">
            <span class="material-symbols-outlined text-[16px]">home</span>
        </a>

        @foreach ($segments as $segment)
            <span style="color: #c9b8ac; font-size: 13px; font-weight: 300;">/</span>
            @if ($loop->last)
                <span class="text-[12.5px] font-semibold px-[10px] py-[3px] rounded-full"
                      style="background: #f0e8e1; color: #3d2b1f;">{{ $segment }}</span>
            @else
                <span class="text-[12.5px]" style="color: #a08070;">{{ $segment }}</span>
            @endif
        @endforeach
    </nav>

    {{-- Lado derecho --}}
    <div class="flex items-center gap-2 ml-auto">

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

        {{-- Empresa activa --}}
        @if ($activeCompany)
            <div class="hidden sm:flex items-center gap-2 px-[6px] pr-3 py-[5px] rounded-[12px] transition-all"
                 style="background: #fff; border: 1px solid #e8ddd5; cursor: default;"
                 onmouseover="this.style.background='#faf0ea'; this.style.borderColor='#d6c3b3'"
                 onmouseout="this.style.background='#fff'; this.style.borderColor='#e8ddd5'">
                @if ($activeCompany->logo)
                    <img src="{{ asset('storage/' . $activeCompany->logo) }}"
                         class="w-[26px] h-[26px] rounded-[7px] object-cover" />
                @else
                    <div class="w-[26px] h-[26px] rounded-[7px] flex items-center justify-center"
                         style="background: linear-gradient(135deg, #c8a98a, #a07050);">
                        <span class="material-symbols-outlined text-[12px] text-white">business</span>
                    </div>
                @endif
                <div class="flex flex-col leading-tight">
                    <span class="text-[9px] font-semibold uppercase tracking-widest" style="color: #a08070;">Empresa activa</span>
                    <span class="text-[12px] font-semibold max-w-[110px] truncate" style="color: #3d2b1f;">
                        {{ $activeCompany->name }}
                    </span>
                </div>
            </div>
            <div class="hidden sm:block w-px h-6" style="background: #e4d5c9;"></div>
        @endif

        {{-- Notificaciones --}}
        @can('ver historial de notificaciones')
            <a href="{{ route('notification-logs.index') }}"
                class="relative w-[34px] h-[34px] rounded-[10px] flex items-center justify-center transition-all"
                style="color: #9c7e6b; border: 1px solid transparent;"
                onmouseover="this.style.background='#f0e8e1'; this.style.borderColor='#e4d5c9'; this.style.color='#6b4e3d'"
                onmouseout="this.style.background='transparent'; this.style.borderColor='transparent'; this.style.color='#9c7e6b'">
                <span class="material-symbols-outlined text-[19px]">notifications</span>
                {{-- Punto rojo si hay notificaciones sin leer --}}
                <span class="absolute top-[7px] right-[7px] w-[7px] h-[7px] rounded-full"
                      style="background: #c0513a; border: 2px solid #faf7f4;"></span>
            </a>
        @endcan

        {{-- Avatar + Dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" :class="open ? 'nb-open' : ''"
                class="flex items-center gap-[9px] pl-1 pr-[10px] py-1 rounded-[12px] transition-all"
                style="border: 1px solid transparent;"
                :style="open ? 'background:#f0e8e1; border-color:#e4d5c9;' : ''"
                onmouseover="this.style.background='#f0e8e1'; this.style.borderColor='#e4d5c9'"
                onmouseout="if(!this.classList.contains('nb-open')){ this.style.background='transparent'; this.style.borderColor='transparent'; }">

                @if (auth()->user()->image)
                    <img src="{{ asset('storage/' . auth()->user()->image) }}"
                        class="w-[32px] h-[32px] rounded-[10px] object-cover flex-shrink-0" />
                @else
                    <div class="w-[32px] h-[32px] rounded-[10px] flex items-center justify-center text-[12px] font-bold text-white flex-shrink-0"
                         style="background: linear-gradient(135deg, #b08060, #7a5040); letter-spacing: 0.5px;">
                        {{ $initials }}
                    </div>
                @endif

                <div class="hidden sm:flex flex-col leading-tight text-left">
                    <span class="text-[13px] font-semibold" style="color: #3d2b1f;">{{ auth()->user()->name }}</span>
                    <span class="text-[9.5px] font-bold uppercase tracking-[0.04em] px-[7px] py-px rounded-full self-start mt-[1px]"
                          style="color: #9c5a30; background: #fce8d8;">{{ ucfirst($userRole) }}</span>
                </div>

                <span class="material-symbols-outlined text-[14px] transition-transform duration-200"
                      style="color: #b09080;"
                      :style="open ? 'transform:rotate(180deg)' : ''">expand_more</span>
            </button>

            {{-- Dropdown --}}
            <div x-show="open" @click.outside="open = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                class="absolute right-0 top-[calc(100%+8px)] w-64 overflow-hidden z-50"
                style="background: #fff; border: 1px solid #e4d5c9; border-radius: 16px;
                       box-shadow: 0 12px 40px rgba(80,40,10,0.12);">

                {{-- Header --}}
                <div class="px-4 py-[14px] flex items-center gap-3"
                     style="border-bottom: 1px solid #f0e8e1; background: #faf7f4;">
                    @if (auth()->user()->image)
                        <img src="{{ asset('storage/' . auth()->user()->image) }}"
                            class="w-[42px] h-[42px] rounded-[12px] object-cover flex-shrink-0" />
                    @else
                        <div class="w-[42px] h-[42px] rounded-[12px] flex items-center justify-center text-[15px] font-bold text-white flex-shrink-0"
                             style="background: linear-gradient(135deg, #b08060, #7a5040); letter-spacing: 0.5px;">
                            {{ $initials }}
                        </div>
                    @endif
                    <div class="flex flex-col min-w-0 gap-[2px]">
                        <p class="text-sm font-semibold truncate m-0" style="color: #2d1f15;">{{ auth()->user()->name }}</p>
                        <p class="text-[11.5px] truncate m-0" style="color: #9c7e6b;">{{ auth()->user()->email }}</p>
                        <span class="inline-flex items-center gap-1 self-start px-2 py-px rounded-full text-[9px] font-bold uppercase tracking-[0.05em] mt-1"
                              style="color: #9c5a30; background: #fce8d8;">
                            <span class="material-symbols-outlined text-[10px]">verified</span>
                            {{ ucfirst($userRole) }}
                        </span>
                    </div>
                </div>

                {{-- Empresa activa --}}
                @if ($activeCompany)
                    <div class="px-4 py-[10px] flex items-center gap-[10px]"
                         style="border-bottom: 1px solid #f0e8e1;">
                        @if ($activeCompany->logo)
                            <img src="{{ asset('storage/' . $activeCompany->logo) }}"
                                class="w-[34px] h-[34px] rounded-[9px] object-cover flex-shrink-0" />
                        @else
                            <div class="w-[34px] h-[34px] rounded-[9px] flex items-center justify-center flex-shrink-0"
                                 style="background: linear-gradient(135deg, #c8a98a, #a07050);">
                                <span class="material-symbols-outlined text-[14px] text-white">business</span>
                            </div>
                        @endif
                        <div class="flex flex-col min-w-0">
                            <span class="text-[9px] font-bold uppercase tracking-widest" style="color: #a08070;">Empresa activa</span>
                            <span class="text-[12.5px] font-semibold truncate" style="color: #3d2b1f;">{{ $activeCompany->name }}</span>
                            @if ($activeCompany->typeCompany)
                                <span class="text-[10.5px]" style="color: #a08070;">{{ $activeCompany->typeCompany->name }}</span>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Links --}}
                <div class="py-[6px]">
                    <a href="{{ route('profile.settings') }}"
                        class="flex items-center gap-[10px] px-4 py-[10px] text-[13px] font-medium transition-colors"
                        style="color: #5a3e30; text-decoration: none;"
                        onmouseover="this.style.background='#faf0ea'"
                        onmouseout="this.style.background='transparent'">
                        <div class="w-[30px] h-[30px] rounded-[8px] flex items-center justify-center flex-shrink-0 transition-all"
                             style="background: #f0e8e1;">
                            <span class="material-symbols-outlined text-[15px]" style="color: #9c7e6b;">manage_accounts</span>
                        </div>
                        Editar perfil
                    </a>

                    <div style="height: 1px; background: #f0e8e1; margin: 2px 0;"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-[10px] px-4 py-[10px] text-[13px] font-medium transition-colors"
                            style="color: #b04030; background: none; border: none; cursor: pointer; text-align: left;"
                            onmouseover="this.style.background='#fff5f4'"
                            onmouseout="this.style.background='transparent'">
                            <div class="w-[30px] h-[30px] rounded-[8px] flex items-center justify-center flex-shrink-0"
                                 style="background: #fdecea;">
                                <span class="material-symbols-outlined text-[15px]" style="color: #b04030;">logout</span>
                            </div>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
