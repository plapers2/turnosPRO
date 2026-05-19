<x-app-layout>
    <main class="flex-1 flex flex-col min-h-0 overflow-y-auto bg-surface">

        <x-header-admin icono="send" titulo="Invitaciones" mensaje="Genera y gestiona enlaces de invitación para tus clientes" />

        <div class="px-8 pb-20 space-y-6">

            {{-- GENERAR NUEVA INVITACIÓN --}}
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide mb-4">
                    Generar nueva invitación
                </h3>

                <form method="POST" action="{{ route('invitations.store') }}" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <div class="flex flex-col gap-1.5 flex-1">
                        <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide">
                            Email del cliente <span class="font-normal normal-case">(opcional)</span>
                        </label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline-variant group-focus-within:text-primary transition-colors">
                                mail
                            </span>
                            <input type="email" name="email" value="{{ old('email', $invitation?->email ?? '') }}"
                                placeholder="cliente@correo.com"
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-outline-variant/30 bg-surface text-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                        </div>
                        @error('email')
                        <p class="text-xs text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition">
                            <span class="material-symbols-outlined text-[18px]">add_link</span>
                            Generar enlace
                        </button>
                    </div>
                </form>

                {{-- ENLACE GENERADO --}}
                @if(session('invite_link'))
                <div x-data="{ link: '{{ session('invite_link') }}' }" class="mt-4 p-4 rounded-lg bg-primary/5 border border-primary/20 flex flex-col sm:flex-row sm:items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-[20px] shrink-0">link</span>
                    <code class="text-xs break-all flex-1 select-all" x-text="link"></code>
                    <button
                        @click="navigator.clipboard.writeText(link).then(() => window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Enlace copiado al portapapeles', type: 'success' } })))"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-primary/90 transition shrink-0">
                        <span class="material-symbols-outlined text-[16px]">content_copy</span>
                        Copiar
                    </button>
                </div>
                @endif

                @if(session('success') && !session('invite_link'))
                <div class="mt-4 p-3 rounded-lg bg-green-50 border border-green-200 text-sm text-green-700">
                    {{ session('success') }}
                </div>
                @endif
            </div>

            {{-- TABLA DE INVITACIONES --}}
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-[0_10px_30px_rgba(95,94,90,0.05)] overflow-hidden">

                <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide">
                        Historial de invitaciones
                    </h3>
                    <span class="text-xs text-on-surface-variant">
                        {{ $invitations->total() }} {{ $invitations->total() === 1 ? 'invitación' : 'invitaciones' }}
                    </span>
                </div>

                {{-- Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-surface/50 text-on-surface-variant">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold">Email</th>
                                <th class="px-6 py-4 text-left font-semibold">Estado</th>
                                <th class="px-6 py-4 text-left font-semibold">Expira</th>
                                <th class="px-6 py-4 text-left font-semibold">Generado por</th>
                                <th class="px-6 py-4 text-left font-semibold">Creado</th>
                                <th class="px-6 py-4 text-left font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse ($invitations as $inv)
                            @php
                            $esRevocada = $inv->trashed();
                            $esExpirada = !$esRevocada && $inv->isExpired();
                            $esRegistrada = $inv->status === 'registered';

                            [$estadoLabel, $estadoClass] = match(true) {
                            $esRevocada => ['Revocada', 'bg-gray-100 text-gray-500'],
                            $esExpirada => ['Expirada', 'bg-orange-100 text-orange-600'],
                            $esRegistrada => ['Registrada', 'bg-green-100 text-green-700'],
                            $inv->status === 'sent' => ['Enviada', 'bg-primary/10 text-primary'],
                            default => ['Pendiente','bg-yellow-100 text-yellow-700'],
                            };
                            @endphp
                            <tr class="hover:bg-surface/40 transition {{ $esRevocada ? 'opacity-50' : '' }}">

                                <td class="px-6 py-4">
                                    <span class="text-on-surface">{{ $inv->email ?? '—' }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $estadoClass }}">
                                        {{ $estadoLabel }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-xs text-on-surface-variant whitespace-nowrap">
                                    {{ $inv->expires_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-xs text-on-surface-variant">
                                    {{ $inv->invitedBy->name ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-xs text-on-surface-variant whitespace-nowrap">
                                    {{ $inv->created_at->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">

                                        {{-- Copiar enlace (solo si usable) --}}
                                        @if(!$esRevocada && !$esRegistrada)
                                        @php $link = route('register.invite', $inv->token); @endphp
                                        <button
                                            onclick="navigator.clipboard.writeText('{{ $link }}').then(() => window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Enlace copiado', type: 'success' } })))"
                                            title="Copiar enlace"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-surface-container hover:bg-surface-container-high text-on-surface-variant transition">
                                            <span class="material-symbols-outlined text-[15px]">content_copy</span>
                                            Copiar
                                        </button>
                                        @endif

                                        {{-- Revocar --}}
                                        @if(!$esRevocada && !$esRegistrada)
                                        <form method="POST" action="{{ route('invitations.destroy', $inv->id) }}"
                                            onsubmit="return confirm('¿Revocar esta invitación?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-error/10 text-error hover:bg-error/20 transition">
                                                <span class="material-symbols-outlined text-[15px]">link_off</span>
                                                Revocar
                                            </button>
                                        </form>
                                        @endif

                                        {{-- Restaurar --}}
                                        @if($esRevocada)
                                        <form method="POST" action="{{ route('invitations.restore', $inv->id) }}">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-primary/10 text-primary hover:bg-primary/20 transition">
                                                <span class="material-symbols-outlined text-[15px]">restart_alt</span>
                                                Restaurar
                                            </button>
                                        </form>
                                        @endif

                                        {{-- Sin acciones para registrada --}}
                                        @if($esRegistrada)
                                        <span class="text-xs text-on-surface-variant italic">Utilizada</span>
                                        @endif

                                    </div>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-16">
                                    <div class="flex flex-col items-center gap-4">
                                        <span class="material-symbols-outlined text-4xl text-primary/30">mail</span>
                                        <p class="text-on-surface-variant text-sm">No hay invitaciones generadas aún</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile --}}
                <div class="md:hidden space-y-4 p-4">
                    @forelse ($invitations as $inv)
                    @php
                    $esRevocada = $inv->trashed();
                    $esExpirada = !$esRevocada && $inv->isExpired();
                    $esRegistrada = $inv->status === 'registered';

                    [$estadoLabel, $estadoClass] = match(true) {
                    $esRevocada => ['Revocada', 'bg-gray-100 text-gray-500'],
                    $esExpirada => ['Expirada', 'bg-orange-100 text-orange-600'],
                    $esRegistrada => ['Registrada', 'bg-green-100 text-green-700'],
                    $inv->status === 'sent' => ['Enviada', 'bg-primary/10 text-primary'],
                    default => ['Pendiente','bg-yellow-100 text-yellow-700'],
                    };
                    @endphp
                    <div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-4 shadow-sm {{ $esRevocada ? 'opacity-50' : '' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-on-surface-variant">{{ $inv->created_at->format('d/m/Y H:i') }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $estadoClass }}">
                                {{ $estadoLabel }}
                            </span>
                        </div>

                        <p class="text-sm font-semibold text-on-surface">{{ $inv->email ?? 'Sin email' }}</p>
                        <p class="text-xs text-on-surface-variant mt-1">
                            Generado por {{ $inv->invitedBy->name ?? '—' }}
                        </p>
                        <p class="text-xs text-on-surface-variant">
                            Expira: {{ $inv->expires_at?->format('d/m/Y H:i') ?? '—' }}
                        </p>

                        <div class="flex flex-wrap gap-2 mt-3">
                            @if(!$esRevocada && !$esRegistrada)
                            @php $link = route('register.invite', $inv->token); @endphp
                            <button
                                onclick="navigator.clipboard.writeText('{{ $link }}').then(() => window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Enlace copiado', type: 'success' } })))"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-surface-container hover:bg-surface-container-high text-on-surface-variant transition">
                                <span class="material-symbols-outlined text-[15px]">content_copy</span>
                                Copiar enlace
                            </button>
                            <form method="POST" action="{{ route('invitations.destroy', $inv->id) }}"
                                onsubmit="return confirm('¿Revocar esta invitación?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-error/10 text-error hover:bg-error/20 transition">
                                    <span class="material-symbols-outlined text-[15px]">link_off</span>
                                    Revocar
                                </button>
                            </form>
                            @endif

                            @if($esRevocada)
                            <form method="POST" action="{{ route('invitations.restore', $inv->id) }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary/10 text-primary hover:bg-primary/20 transition">
                                    <span class="material-symbols-outlined text-[15px]">restart_alt</span>
                                    Restaurar
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-sm text-on-surface-variant py-8">No hay invitaciones generadas</p>
                    @endforelse
                </div>

                {{-- PAGINACIÓN --}}
                <div class="px-6 py-4 border-t border-outline-variant/20">
                    {{ $invitations->links() }}
                </div>

            </div>
        </div>
    </main>
</x-app-layout>