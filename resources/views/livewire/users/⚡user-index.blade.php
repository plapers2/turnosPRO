<div>

    {{-- ══════════════════════════════
         LOADING BAR
    ══════════════════════════════ --}}
    <div wire:loading.delay class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>
    {{-- ══════════════════════════════
         ESTADÍSTICAS
    ══════════════════════════════ --}}
    <div class="grid grid-cols-2 gap-3 px-4 pt-5 sm:px-8 lg:grid-cols-4">

        <div
            class="flex items-center gap-3 rounded-xl border border-outline-variant/50 bg-surface-container-lowest px-4 py-3">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-primary-container text-on-primary-container">
                <span class="material-symbols-outlined text-[17px]">workspace_premium</span>
            </div>
            <div class="flex flex-col gap-0.5">
                <span class="text-[17px] font-semibold leading-none text-on-surface">{{ $users->total() }}</span>
                <span class="text-[11px] font-normal text-on-surface-variant">Total</span>
            </div>
        </div>

        <div
            class="flex items-center gap-3 rounded-xl border border-outline-variant/50 bg-surface-container-lowest px-4 py-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-green-100 text-green-700">
                <span class="material-symbols-outlined text-[17px]">check_circle</span>
            </div>
            <div class="flex flex-col gap-0.5">
                <span class="text-[17px] font-semibold leading-none text-on-surface">{{ $activeCount }}</span>
                <span class="text-[11px] font-normal text-on-surface-variant">Activos</span>
            </div>
        </div>

        <div
            class="flex items-center gap-3 rounded-xl border border-outline-variant/50 bg-surface-container-lowest px-4 py-3">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-error-container/60 text-on-error-container">
                <span class="material-symbols-outlined text-[17px]">cancel</span>
            </div>
            <div class="flex flex-col gap-0.5">
                <span class="text-[17px] font-semibold leading-none text-on-surface">{{ $inactiveCount }}</span>
                <span class="text-[11px] font-normal text-on-surface-variant">Inactivos</span>
            </div>
        </div>

        <div
            class="flex items-center gap-3 rounded-xl border border-outline-variant/50 bg-surface-container-lowest px-4 py-3">
            <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-secondary-container/50 text-on-secondary-container">
                <span class="material-symbols-outlined text-[17px]">badge</span>
            </div>
            <div class="flex flex-col gap-0.5">
                <span class="text-[17px] font-semibold leading-none text-on-surface">{{ $roles->count() - 1 }}</span>
                <span class="text-[11px] font-normal text-on-surface-variant">Roles</span>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════
         FILTROS
    ══════════════════════════════ --}}
    <div class="flex flex-col gap-3 px-4 pt-5 sm:px-8 lg:flex-row lg:items-center">

        {{-- Buscador --}}
        <div class="relative w-full lg:max-w-[280px]">
            <span
                class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2
                         -translate-y-1/2 text-[17px] text-on-surface-variant">
                search
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar usuario..."
                class="w-full rounded-xl border border-outline-variant/60 bg-surface-container-lowest
                       py-2.5 pl-9 pr-4 text-[13px] text-on-surface placeholder:text-on-surface-variant/50
                       focus:outline-none focus:ring-2 focus:ring-primary/30 transition-shadow" />
        </div>

        {{-- Filtro de rol --}}
        <div class="relative w-full lg:max-w-[200px]">
            <span
                class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2
                         -translate-y-1/2 text-[17px] text-on-surface-variant">
                shield_person
            </span>
            <select wire:model.live="role"
                class="w-full appearance-none rounded-xl border border-outline-variant/60
                       bg-surface-container-lowest py-2.5 pl-9 pr-8
                       text-[13px] text-on-surface
                       focus:outline-none focus:ring-2 focus:ring-primary/30 transition-shadow">
                <option value="">Todos los roles</option>
                @foreach ($roles as $r)
                    @if ($r->name != 'cliente')
                        <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                    @endif
                @endforeach
            </select>
            <span
                class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2
                         -translate-y-1/2 text-[17px] text-on-surface-variant">
                expand_more
            </span>
        </div>

        {{-- Tabs de estado --}}
        <div
            class="flex gap-1 rounded-xl border border-outline-variant/60
                    bg-surface-container-lowest p-1 shrink-0 self-start lg:self-auto">
            <button wire:click="$set('status', '')"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5
                       text-[12.5px] font-semibold transition-all duration-150
                       {{ $status === ''
                           ? 'bg-on-primary-fixed-variant text-primary-fixed shadow-sm'
                           : 'text-on-surface-variant hover:bg-surface-container' }}">
                Todos
            </button>
            <button wire:click="$set('status', 'active')"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5
                       text-[12.5px] font-semibold transition-all duration-150
                       {{ $status === 'active'
                           ? 'bg-on-primary-fixed-variant text-primary-fixed shadow-sm'
                           : 'text-on-surface-variant hover:bg-surface-container' }}">
                Activos
            </button>
            <button wire:click="$set('status', 'inactive')"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5
                       text-[12.5px] font-semibold transition-all duration-150
                       {{ $status === 'inactive'
                           ? 'bg-on-primary-fixed-variant text-primary-fixed shadow-sm'
                           : 'text-on-surface-variant hover:bg-surface-container' }}">
                Inactivos
            </button>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════
         VISTA MÓVIL — CARDS (solo < md)
    ══════════════════════════════════════════════ --}}
    <div wire:loading.class="opacity-50 pointer-events-none" wire:target="search, status, role"
        class="flex flex-col gap-3 px-4 pt-5 pb-8 transition-opacity duration-200 md:hidden">

        @forelse($users as $user)
            <article
                class="flex flex-col overflow-hidden rounded-2xl border bg-surface-container-lowest
                       shadow-[0_2px_12px_rgba(0,0,0,0.04)]
                       {{ $user->trashed() ? 'border-dashed border-outline-variant/50 opacity-75' : 'border-outline-variant/50' }}">
                {{-- Franja + Avatar --}}
                <div class="relative h-[60px] w-full shrink-0 overflow-hidden"
                    style="background: linear-gradient(135deg, #854f0b 0%, #663a00 60%, #9f4121 100%);">
                    <div class="absolute inset-0 opacity-10"
                        style="background-image: radial-gradient(circle at 80% 50%, #fff 1px, transparent 1px);
                                background-size: 18px 18px;">
                    </div>
                    <span
                        class="absolute right-3 top-3 rounded-full px-2.5 py-[3px]
                                 text-[11px] font-semibold tracking-wide backdrop-blur-sm
                                 {{ $user->trashed() ? 'bg-error-container/90 text-on-error-container' : 'bg-green-100/90 text-green-800' }}">
                        {{ $user->trashed() ? 'Inactivo' : 'Activo' }}
                    </span>
                    <div class="absolute -bottom-6 left-4">
                        @if ($user->profile_photo_path)
                            <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}"
                                class="h-12 w-12 rounded-full border-4 border-surface-container-lowest object-cover shadow-md
                                       {{ $user->trashed() ? 'grayscale opacity-70' : '' }}">
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-full border-4
                                        border-surface-container-lowest shadow-md text-[16px] font-bold"
                                style="background-color: #ffdcbe; color: #663a00;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strstr($user->name, ' '), 1, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Cuerpo --}}
                <div class="flex flex-col gap-2 px-4 pb-3 pt-9">
                    <div>
                        <h3 class="font-headline text-[14px] font-semibold text-on-surface leading-snug">
                            {{ $user->name }}
                        </h3>
                        <p class="text-[12px] text-on-surface-variant truncate">{{ $user->email }}</p>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        @forelse($user->roles as $userRole)
                            <span
                                class="inline-flex items-center gap-1 rounded-lg
                                         bg-secondary-container/50 px-2 py-[3px]
                                         text-[11px] font-semibold text-on-secondary-container">
                                <span class="material-symbols-outlined text-[11px]">shield_person</span>
                                {{ ucfirst($userRole->name) }}
                            </span>
                        @empty
                            <span
                                class="inline-flex items-center gap-1 rounded-lg
                                         bg-surface-container px-2 py-[3px]
                                         text-[11px] font-medium text-on-surface-variant">
                                Sin rol
                            </span>
                        @endforelse
                    </div>

                    {{-- Acciones --}}
                    <div class="flex items-center gap-1 border-t border-outline-variant/30 pt-2 mt-1">
                        @if ($user->trashed())
                            <button wire:click="restoreUser({{ $user->id }})" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-transparent
                                       px-3 py-1.5 text-[12px] font-semibold text-green-700
                                       transition-all hover:border-green-200/70 hover:bg-green-50 disabled:opacity-50">
                                <span wire:loading.remove wire:target="restoreUser({{ $user->id }})">
                                    <span class="material-symbols-outlined text-[14px] leading-none">restore</span>
                                </span>
                                <span wire:loading wire:target="restoreUser({{ $user->id }})">
                                    <span
                                        class="block h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                                </span>
                                Restaurar
                            </button>
                        @else
                            <a href="{{ route('users.show', $user) }}"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-transparent
                                      px-3 py-1.5 text-[12px] font-semibold text-on-surface-variant
                                      transition-all hover:border-outline-variant/40 hover:bg-surface-container">
                                <span class="material-symbols-outlined text-[14px] leading-none">visibility</span>
                                Ver
                            </a>
                            <a href="{{ route('users.edit', $user) }}"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-transparent
                                      px-3 py-1.5 text-[12px] font-semibold text-primary/80
                                      transition-all hover:border-primary/20 hover:bg-primary-container/40">
                                <span class="material-symbols-outlined text-[14px] leading-none">edit</span>
                                Editar
                            </a>
                            <button wire:click="confirmDelete({{ $user->id }})"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-transparent
                                       px-3 py-1.5 text-[12px] font-semibold text-error/80
                                       transition-all hover:border-error/20 hover:bg-error-container/40">
                                <span class="material-symbols-outlined text-[14px] leading-none">delete</span>
                                Eliminar
                            </button>
                        @endif
                    </div>
                </div>

            </article>
        @empty
            <div
                class="flex flex-col items-center justify-center gap-4 rounded-2xl border border-dashed
                        border-outline-variant/50 bg-surface-container-lowest px-8 py-16 text-center">
                <div
                    class="flex h-14 w-14 items-center justify-center rounded-full bg-surface-container text-on-surface-variant">
                    <span class="material-symbols-outlined text-[26px]">manage_accounts</span>
                </div>
                <div class="flex flex-col gap-1">
                    <h3 class="font-headline text-[17px] font-semibold text-on-surface">Sin resultados</h3>
                    <p class="max-w-[260px] text-[13px] leading-relaxed text-on-surface-variant">
                        No se encontraron usuarios. Intenta con otro término o cambia el filtro.
                    </p>
                </div>
            </div>
        @endforelse

    </div>

    {{-- ══════════════════════════════════════════════
         VISTA DESKTOP — TABLA (solo >= md)
    ══════════════════════════════════════════════ --}}
    <div wire:loading.class="opacity-50 pointer-events-none" wire:target="search, status, role"
        class="hidden md:block px-8 pt-5 pb-8 transition-opacity duration-200">
        <div
            class="overflow-hidden rounded-2xl border border-outline-variant/50
                    bg-surface-container-lowest shadow-[0_2px_12px_rgba(0,0,0,0.04)]">

            <table class="w-full border-collapse">

                {{-- Cabecera --}}
                <thead>
                    <tr class="border-b border-outline-variant/40" style="background-color: #f6f3ee;">
                        <th
                            class="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-widest text-on-surface-variant">
                            Usuario
                        </th>
                        <th
                            class="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-widest text-on-surface-variant">
                            Email
                        </th>
                        <th
                            class="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-widest text-on-surface-variant">
                            Rol
                        </th>
                        <th
                            class="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-widest text-on-surface-variant">
                            Estado
                        </th>
                        <th
                            class="px-5 py-3.5 text-right text-[11px] font-semibold uppercase tracking-widest text-on-surface-variant">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-outline-variant/30">

                    @forelse($users as $user)
                        <tr wire:key="user-{{ $user->id }}"
                            class="group transition-colors duration-150 hover:bg-surface-container-low
                                   {{ $user->trashed() ? 'opacity-60' : '' }}">

                            {{-- Usuario --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if ($user->profile_photo_path)
                                        <img src="{{ Storage::url($user->profile_photo_path) }}"
                                            alt="{{ $user->name }}"
                                            class="h-9 w-9 rounded-full object-cover border-2 border-outline-variant/40 shrink-0
                                                   {{ $user->trashed() ? 'grayscale' : '' }}">
                                    @else
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                                    border-2 border-outline-variant/40 text-[13px] font-bold"
                                            style="background-color: #ffdcbe; color: #663a00;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strstr($user->name, ' '), 1, 1)) }}
                                        </div>
                                    @endif
                                    <span class="text-[13px] font-semibold text-on-surface">{{ $user->name }}</span>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-5 py-3.5">
                                <span class="text-[13px] text-on-surface-variant">{{ $user->email }}</span>
                            </td>

                            {{-- Rol --}}
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($user->roles as $userRole)
                                        <span
                                            class="inline-flex items-center gap-1 rounded-lg
                                                     bg-secondary-container/50 px-2.5 py-[4px]
                                                     text-[11.5px] font-semibold text-on-secondary-container">
                                            <span class="material-symbols-outlined text-[12px]">shield_person</span>
                                            {{ ucfirst($userRole->name) }}
                                        </span>
                                    @empty
                                        <span
                                            class="inline-flex items-center gap-1 rounded-lg
                                                     bg-surface-container px-2.5 py-[4px]
                                                     text-[11.5px] font-medium text-on-surface-variant">
                                            Sin rol
                                        </span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Estado --}}
                            <td class="px-5 py-3.5">
                                @if ($user->trashed())
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full
                                                 bg-error-container/60 px-2.5 py-[4px]
                                                 text-[11.5px] font-semibold text-on-error-container">
                                        <span class="h-1.5 w-1.5 rounded-full bg-error"></span>
                                        Inactivo
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full
                                                 bg-green-100 px-2.5 py-[4px]
                                                 text-[11.5px] font-semibold text-green-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                        Activo
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($user->trashed())
                                        <button wire:click="restoreUser({{ $user->id }})"
                                            wire:loading.attr="disabled"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-transparent
                                                   px-3 py-1.5 text-[12px] font-semibold text-green-700
                                                   transition-all hover:border-green-200/70 hover:bg-green-50 disabled:opacity-50">
                                            <span wire:loading.remove wire:target="restoreUser({{ $user->id }})">
                                                <span
                                                    class="material-symbols-outlined text-[14px] leading-none">restore</span>
                                            </span>
                                            <span wire:loading wire:target="restoreUser({{ $user->id }})">
                                                <span
                                                    class="block h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                                            </span>
                                            Restaurar
                                        </button>
                                    @else
                                        <a href="{{ route('users.show', $user) }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-transparent
                                                   px-3 py-1.5 text-[12px] font-semibold text-on-surface-variant
                                                   transition-all hover:border-outline-variant/40 hover:bg-surface-container">
                                            <span
                                                class="material-symbols-outlined text-[14px] leading-none">visibility</span>
                                            Ver
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-transparent
                                                   px-3 py-1.5 text-[12px] font-semibold text-primary/80
                                                   transition-all hover:border-primary/20 hover:bg-primary-container/40">
                                            <span
                                                class="material-symbols-outlined text-[14px] leading-none">edit</span>
                                            Editar
                                        </a>
                                        <button wire:click="confirmDelete({{ $user->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-transparent
                                                   px-3 py-1.5 text-[12px] font-semibold text-error/80
                                                   transition-all hover:border-error/20 hover:bg-error-container/40">
                                            <span
                                                class="material-symbols-outlined text-[14px] leading-none">delete</span>
                                            Eliminar
                                        </button>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="flex flex-col items-center justify-center gap-4 px-8 py-20 text-center">
                                    <div
                                        class="flex h-14 w-14 items-center justify-center
                                                rounded-full bg-surface-container text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[26px]">manage_accounts</span>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <h3 class="font-headline text-[17px] font-semibold text-on-surface">Sin
                                            resultados</h3>
                                        <p class="max-w-[260px] text-[13px] leading-relaxed text-on-surface-variant">
                                            No se encontraron usuarios. Intenta con otro término o cambia el filtro.
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>

        </div>
    </div>

    {{-- ══════════════════════════════
         PAGINACIÓN
    ══════════════════════════════ --}}
    @if ($users->hasPages())
        <div class="border-t border-outline-variant/40 px-4 py-5 sm:px-8">
            {{ $users->links() }}
        </div>
    @endif

    {{-- ══════════════════════════════
         SWEETALERT
    ══════════════════════════════ --}}
    @script
        <script>
            $wire.on('confirm-delete', ({
                id
            }) => {
                Swal.fire({
                    title: '¿Eliminar usuario?',
                    text: 'Podrás restaurarlo después si lo necesitas.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ba1a1a',
                    cancelButtonColor: '#847467',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    background: '#fcf9f3',
                    color: '#1c1c19',
                    customClass: {
                        popup: 'rounded-xl shadow-lg',
                        confirmButton: 'px-4 py-2 rounded-lg font-semibold',
                        cancelButton: 'px-4 py-2 rounded-lg',
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.deleteUser(id);

                        $wire.on('user-deleted', () => {
                            Swal.fire({
                                title: 'Eliminado',
                                text: 'El usuario fue eliminado correctamente.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                background: '#fcf9f3',
                                color: '#1c1c19',
                            });
                        });

                        $wire.on('delete-error', ({
                            message
                        }) => {
                            Swal.fire({
                                title: 'No se puede eliminar',
                                text: message,
                                icon: 'error',
                                confirmButtonText: 'Entendido',
                                confirmButtonColor: '#ba1a1a',
                                background: '#fcf9f3',
                                color: '#1c1c19',
                            });
                        });
                    }
                });
            });
        </script>
    @endscript

</div>
