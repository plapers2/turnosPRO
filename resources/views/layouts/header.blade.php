<div class="sticky top-0 z-40 w-full flex items-center h-[60px] px-5 gap-3"
    style="background: rgba(253,253,253,0.95); backdrop-filter: blur(12px); border-bottom: 1px solid #e8ddd5;">

    {{-- Botón menú móvil --}}
    <button id="menuBtn"
        class="md:hidden w-[34px] h-[34px] rounded-[10px] flex items-center justify-center
               border transition-colors"
        style="background: #f6f3ee; border-color: #d6c3b3; color: #a08070;">
        <span class="material-symbols-outlined text-[20px]">menu</span>
    </button>

    {{-- Breadcrumb --}}
    <nav class="hidden md:flex items-center gap-1.5 flex-1">
        @php
        $translations = [
        'professional-availability' => 'Disponibilidad de Profesionales',
        'settings' => 'Configuración',
        'dashboard' => 'Dashboard',
        'appointment-manager' => 'Citas',
        'appointment' => 'Citas',
        'appointments' => 'Citas',
        'users' => 'Profesionales',
        'services' => 'Servicios',
        'customers' => 'Clientes',
        'companies' => 'Empresa',
        'type-companies' => 'Tipos de empresa',
        'opening-hours' => 'Horarios de atención',
        'notification-logs' => 'Notificaciones',
        'profile' => 'Perfil',
        'new-for-client' => 'Nueva cita para el cliente',
        'new' => 'Nueva cita',
        'my-appointments' => 'Mis citas',

        // Acciones
        'create' => 'Crear',
        'edit' => 'Editar',
        'show' => 'Detalle',
        'index' => 'Listado',
        ];

        $combinedTranslations = [
        'appointment/index' => 'Reservar cita',
        'appointment/history' => 'Historial de citas',
        'appointments/export' => 'Exportar citas',
        'profile/settings' => 'Configuración de perfil',
        ];

        $rawSegments = collect(request()->segments())->filter(fn($s) => !is_numeric($s));

        $combined = $rawSegments->values()->take(2)->implode('/');

        $segments = isset($combinedTranslations[$combined])
        ? collect([$combinedTranslations[$combined]])
        : $rawSegments->map(fn($s) => $translations[$s] ?? ucfirst(str_replace('-', ' ', $s)));
        @endphp

        {{-- Home --}}
        <a href="{{ auth()->user()->hasRole('master') ? route('master.index') : route('dashboard') }}"
            class="w-[30px] h-[30px] rounded-[8px] flex items-center justify-center
                   border border-transparent transition-all"
            style="background: #f6f3ee; color: #a08070;"
            onmouseover="this.style.background='#f1ede8'; this.style.borderColor='#e8ddd5'; this.style.color='#7a5a48'"
            onmouseout="this.style.background='#f6f3ee'; this.style.borderColor='transparent'; this.style.color='#a08070'">
            <span class="material-symbols-outlined text-[15px]">home</span>
        </a>

        @foreach ($segments as $segment)
        <span style="color: #d6c3b3; font-size: 13px; font-weight: 300;">/</span>
        @if ($loop->last)
        <span class="text-[12px] font-semibold px-[10px] py-[3px] rounded-full"
            style="background: #f6f3ee; color: #2d1f15;">{{ $segment }}</span>
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

        {{-- Empresa activa — con switcher (Si tiene mas de 1 empresa) --}}
        @if ($activeCompany)
        @php
        $userCompanies = auth()->user()->companies;
        @endphp

        @if ($userCompanies->count() > 1)
        {{-- Dropdown switcher --}}
        <div class="relative hidden sm:block" x-data="{ open: false }">
            <button @click="open = !open"
                class="flex items-center gap-2 pl-[5px] pr-3 py-[5px] rounded-[12px] border transition-all"
                :style="open
                    ? 'background:#f6f3ee; border-color:#d6c3b3;'
                    : 'background:#fff; border-color:#e8ddd5;'"
                onmouseover="this.style.background='#f6f3ee'; this.style.borderColor='#d6c3b3'"
                @mouseleave="if(!open){ $el.style.background='#fff'; $el.style.borderColor='#e8ddd5'; }">

                @if ($activeCompany->logo)
                <img src="{{ asset('storage/' . $activeCompany->logo) }}"
                    class="w-[26px] h-[26px] rounded-[7px] object-cover flex-shrink-0" />
                @else
                <div class="w-[26px] h-[26px] rounded-[7px] flex items-center justify-center flex-shrink-0"
                    style="background: #ffdcbe;">
                    <span class="material-symbols-outlined text-[13px]" style="color: #663a00;">business</span>
                </div>
                @endif

                <div class="flex flex-col leading-tight text-left">
                    <span class="text-[9px] font-bold uppercase tracking-widest" style="color: #a08070;">Empresa activa</span>
                    <span class="text-[12px] font-semibold max-w-[110px] truncate" style="color: #2d1f15;">
                        {{ $activeCompany->name }}
                    </span>
                </div>

                <span class="material-symbols-outlined text-[14px] transition-transform duration-200"
                    style="color: #b09080;" :style="open ? 'transform:rotate(180deg)' : ''">expand_more</span>
            </button>

            {{-- Dropdown --}}
            <div x-show="open" @click.outside="open = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                class="absolute left-0 top-[calc(100%+8px)] w-56 z-50 rounded-[16px] overflow-hidden"
                style="background:#fff; border:1px solid #d6c3b3; box-shadow:0 8px 32px rgba(80,40,10,0.10);">

                <div class="px-4 py-[10px]" style="border-bottom:1px solid #f0e8e1;">
                    <p class="text-[9px] font-bold uppercase tracking-widest m-0" style="color:#a08070;">
                        Cambiar empresa
                    </p>
                </div>

                <div class="py-[5px]">
                    @foreach ($userCompanies as $company)
                    @php $isActive = $company->id === $activeCompany->id; @endphp
                    <form method="POST" action="{{ route('company.select.store') }}">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ $company->id }}">
                        <button type="submit" @click="open = false"
                            class="w-full flex items-center gap-[10px] px-4 py-[9px] text-left border-0 cursor-pointer transition-colors"
                            style="background: {{ $isActive ? '#faf0ea' : 'transparent' }}; color: #5a3e30;"
                            onmouseover="this.style.background='#faf0ea'"
                            onmouseout="this.style.background='{{ $isActive ? '#faf0ea' : 'transparent' }}'">

                            @if ($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}"
                                class="w-[28px] h-[28px] rounded-[8px] object-cover flex-shrink-0" />
                            @else
                            <div class="w-[28px] h-[28px] rounded-[8px] flex items-center justify-center flex-shrink-0"
                                style="background: #ffdcbe;">
                                <span class="material-symbols-outlined text-[13px]" style="color: #663a00;">business</span>
                            </div>
                            @endif

                            <span class="text-[12.5px] font-medium truncate flex-1">{{ $company->name }}</span>

                            @if ($isActive)
                            <span class="material-symbols-outlined text-[14px] flex-shrink-0" style="color: #9c7e6b;">check</span>
                            @endif
                        </button>
                    </form>
                    @endforeach
                </div>
            </div>
        </div>

        @else
        {{-- Solo una empresa — estático --}}
        <div class="hidden sm:flex items-center gap-2 pl-[5px] pr-3 py-[5px] rounded-[12px] border cursor-default"
            style="background:#fff; border-color:#e8ddd5;">
            @if ($activeCompany->logo)
            <img src="{{ asset('storage/' . $activeCompany->logo) }}"
                class="w-[26px] h-[26px] rounded-[7px] object-cover flex-shrink-0" />
            @else
            <div class="w-[26px] h-[26px] rounded-[7px] flex items-center justify-center flex-shrink-0"
                style="background: #ffdcbe;">
                <span class="material-symbols-outlined text-[13px]" style="color: #663a00;">business</span>
            </div>
            @endif
            <div class="flex flex-col leading-tight">
                <span class="text-[9px] font-bold uppercase tracking-widest" style="color: #a08070;">Empresa activa</span>
                <span class="text-[12px] font-semibold max-w-[110px] truncate" style="color: #2d1f15;">{{ $activeCompany->name }}</span>
            </div>
        </div>
        @endif

        <div class="hidden sm:block w-px h-5" style="background: #e8ddd5;"></div>
        @endif

        {{-- Notificaciones --}}
        @can('ver historial de notificaciones')
        <a href="{{ route('notification-logs.index') }}"
            class="relative w-[34px] h-[34px] rounded-[10px] flex items-center justify-center
                       border border-transparent transition-all"
            style="color: #a08070;"
            onmouseover="this.style.background='#f6f3ee'; this.style.borderColor='#e8ddd5'; this.style.color='#7a5a48'"
            onmouseout="this.style.background='transparent'; this.style.borderColor='transparent'; this.style.color='#a08070'">
            <span class="material-symbols-outlined text-[19px]">notifications</span>
            <span class="absolute top-[7px] right-[7px] w-[7px] h-[7px] rounded-full"
                style="background: #c0513a; border: 2px solid #fcf9f3;"></span>
        </a>
        @endcan

        {{-- Avatar + Dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="flex items-center gap-[8px] pl-1 pr-[10px] py-1 rounded-[12px]
                       border transition-all"
                :style="open
                    ?
                    'background:#f6f3ee; border-color:#d6c3b3;' :
                    'background:transparent; border-color:transparent;'"
                onmouseover="this.style.background='#f6f3ee'; this.style.borderColor='#d6c3b3'"
                @mouseleave="if(!open){ $el.style.background='transparent'; $el.style.borderColor='transparent'; }">

                {{-- Avatar --}}
                @if (auth()->user()->image)
                <img src="{{ asset('storage/' . auth()->user()->image) }}"
                    class="w-[32px] h-[32px] rounded-[10px] object-cover flex-shrink-0" />
                @else
                <div class="w-[32px] h-[32px] rounded-[10px] flex items-center justify-center
                                text-[12px] font-bold flex-shrink-0"
                    style="background: #ffdcbe; color: #663a00; letter-spacing: 0.5px;">
                    {{ $initials }}
                </div>
                @endif

                <div class="hidden sm:flex flex-col leading-tight text-left">
                    <span class="text-[13px] font-semibold" style="color: #2d1f15;">{{ auth()->user()->name }}</span>
                    <span
                        class="text-[9px] font-bold uppercase tracking-[0.05em] px-[6px] py-px rounded-full self-start mt-[1px]"
                        style="background: #fce8d8; color: #9c5a30;">{{ ucfirst($userRole) }}</span>
                </div>

                <span class="material-symbols-outlined text-[14px] transition-transform duration-200"
                    style="color: #b09080;" :style="open ? 'transform:rotate(180deg)' : ''">expand_more</span>
            </button>

            {{-- Dropdown --}}
            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                class="absolute right-0 top-[calc(100%+8px)] w-60 overflow-hidden z-50 rounded-[16px]"
                style="background: #fff; border: 1px solid #d6c3b3;
                        box-shadow: 0 8px 32px rgba(80,40,10,0.10);">

                {{-- Header usuario --}}
                <div class="px-4 py-[13px] flex items-center gap-3"
                    style="background: #faf7f4; border-bottom: 1px solid #f0e8e1;">
                    @if (auth()->user()->image)
                    <img src="{{ asset('storage/' . auth()->user()->image) }}"
                        class="w-[40px] h-[40px] rounded-[11px] object-cover flex-shrink-0" />
                    @else
                    <div class="w-[40px] h-[40px] rounded-[11px] flex items-center justify-center
                                    text-[14px] font-bold flex-shrink-0"
                        style="background: #ffdcbe; color: #663a00;">
                        {{ $initials }}
                    </div>
                    @endif
                    <div class="flex flex-col min-w-0">
                        <p class="text-[13px] font-semibold truncate m-0" style="color: #2d1f15;">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-[11px] truncate m-0 mt-px" style="color: #a08070;">{{ auth()->user()->email }}
                        </p>
                        <span
                            class="inline-flex items-center gap-1 self-start mt-[5px]
                                     text-[9px] font-bold uppercase tracking-[0.05em]
                                     px-[7px] py-px rounded-full"
                            style="background: #fce8d8; color: #9c5a30;">
                            <span class="material-symbols-outlined text-[10px]">verified</span>
                            {{ ucfirst($userRole) }}
                        </span>
                    </div>
                </div>

                {{-- Empresa activa --}}
                @if ($activeCompany)
                <div class="px-4 py-[10px] flex items-center gap-[10px]" style="border-bottom: 1px solid #f0e8e1;">
                    @if ($activeCompany->logo)
                    <img src="{{ asset('storage/' . $activeCompany->logo) }}"
                        class="w-[32px] h-[32px] rounded-[9px] object-cover flex-shrink-0" />
                    @else
                    <div class="w-[32px] h-[32px] rounded-[9px] flex items-center justify-center flex-shrink-0"
                        style="background: #ffdcbe;">
                        <span class="material-symbols-outlined text-[14px]"
                            style="color: #663a00;">business</span>
                    </div>
                    @endif
                    <div class="flex flex-col min-w-0">
                        <span class="text-[9px] font-bold uppercase tracking-widest" style="color: #a08070;">Empresa
                            activa</span>
                        <span class="text-[12px] font-semibold truncate"
                            style="color: #2d1f15;">{{ $activeCompany->name }}</span>
                        @if ($activeCompany->typeCompany)
                        <span class="text-[10.5px]"
                            style="color: #a08070;">{{ $activeCompany->typeCompany->name }}</span>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Links --}}
                <div class="py-[5px]">
                    <a href="{{ route('profile.settings') }}"
                        class="flex items-center gap-[10px] px-4 py-[9px] text-[13px] font-medium
                               no-underline transition-colors"
                        style="color: #5a3e30;" onmouseover="this.style.background='#faf0ea'"
                        onmouseout="this.style.background='transparent'">
                        <div class="w-[28px] h-[28px] rounded-[8px] flex items-center justify-center flex-shrink-0"
                            style="background: #f0e8e1;">
                            <span class="material-symbols-outlined text-[15px]"
                                style="color: #9c7e6b;">manage_accounts</span>
                        </div>
                        Editar perfil
                    </a>

                    <div style="height: 1px; background: #f0e8e1; margin: 2px 0;"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-[10px] px-4 py-[9px]
                                   text-[13px] font-medium border-0 cursor-pointer text-left
                                   bg-transparent transition-colors"
                            style="color: #b04030;" onmouseover="this.style.background='#fff5f4'"
                            onmouseout="this.style.background='transparent'">
                            <div class="w-[28px] h-[28px] rounded-[8px] flex items-center justify-center flex-shrink-0"
                                style="background: #fdecea;">
                                <span class="material-symbols-outlined text-[15px]"
                                    style="color: #b04030;">logout</span>
                            </div>
                            Cerrar sesión
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>