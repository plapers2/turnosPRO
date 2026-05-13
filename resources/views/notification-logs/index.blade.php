<x-app-layout>
    <main class="flex-1 flex flex-col min-h-0 overflow-y-auto bg-surface">

        <!-- HEADER -->
        <x-header-admin icono="mail" titulo="Historial de Notificaciones" mensaje="Consulta los emails enviados por el sistema" />

        <div class="px-8 pb-20">

            <!-- FILTROS -->
            <form method="GET" action="{{ route('notification-logs.index') }}"
                class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 mb-6 shadow-sm">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Desde</label>
                        <input type="date" name="desde" value="{{ request('desde') }}"
                            class="px-3 py-2 rounded-lg border border-outline-variant/30 bg-surface text-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Hasta</label>
                        <input type="date" name="hasta" value="{{ request('hasta') }}"
                            class="px-3 py-2 rounded-lg border border-outline-variant/30 bg-surface text-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide">ID Cita</label>
                        <input type="number" name="appointment_id" value="{{ request('appointment_id') }}"
                            placeholder="Ej. 42"
                            class="px-3 py-2 rounded-lg border border-outline-variant/30 bg-surface text-sm text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Estado</label>
                        <select name="status"
                            class="px-3 py-2 rounded-lg border border-outline-variant/30 bg-surface text-sm text-on-surface focus:outline-none focus:border-primary transition">
                            <option value="">Todos</option>
                            <option value="sent" @selected(request('status')==='sent' )>Enviado</option>
                            <option value="error" @selected(request('status')==='error' )>Error</option>
                        </select>
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <a href="{{ route('notification-logs.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-surface-container hover:bg-surface-container-high transition">
                        Limpiar
                    </a>
                    <button type="submit"
                        class="px-5 py-2 rounded-lg text-sm font-semibold bg-primary text-white hover:bg-primary/90 transition">
                        Filtrar
                    </button>
                </div>
            </form>

            <!-- TABLA -->
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20
                shadow-[0_10px_30px_rgba(95,94,90,0.05)] overflow-hidden">

                <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-on-surface-variant uppercase tracking-wide">
                        Registros
                    </h3>
                    <span class="text-xs text-on-surface-variant">
                        {{ $logs->total() }} {{ $logs->total() === 1 ? 'registro' : 'registros' }}
                    </span>
                </div>

                <!-- Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-surface/50 text-on-surface-variant">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold">Fecha</th>
                                <th class="px-6 py-4 text-left font-semibold">Evento</th>
                                <th class="px-6 py-4 text-left font-semibold">Destinatario</th>
                                <th class="px-6 py-4 text-left font-semibold">Cita</th>
                                <th class="px-6 py-4 text-left font-semibold">Estado</th>
                                <th class="px-6 py-4 text-left font-semibold">Error</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse ($logs as $log)
                            <tr class="hover:bg-surface/40 transition">

                                <td class="px-6 py-4 text-xs text-on-surface-variant whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                    $eventoLabel = match($log->type) {
                                    'confirmation' => 'Cita confirmada',
                                    'admin_notification' => 'Nueva reserva',
                                    'cancelled_admin' => 'Cancelación por cliente',
                                    'cancelled_by_employee' => 'Cancelación por empleado',
                                    'confirmed_by_employee' => 'Confirmación por empleado',
                                    'completed' => 'Cita completada',
                                    default => ucfirst($log->type),
                                    };
                                    $eventoClass = match($log->type) {
                                    'confirmation' => 'bg-primary/10 text-primary',
                                    'admin_notification' => 'bg-secondary/10 text-secondary',
                                    'cancelled_admin' => 'bg-error/10 text-error',
                                    'cancelled_by_employee' => 'bg-orange-100 text-orange-700',
                                    'confirmed_by_employee' => 'bg-green-100 text-green-700',
                                    'completed' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-gray-100 text-gray-600',
                                    };
                                    $destinatario = match($log->type) {
                                    'confirmation' => 'Cliente',
                                    'admin_notification' => 'Admin',
                                    'cancelled_admin' => 'Admin',
                                    'cancelled_by_employee' => 'Cliente',
                                    'confirmed_by_employee' => 'Cliente',
                                    'completed' => 'Cliente',
                                    default => '—',
                                    };
                                    $destinatarioClass = match($log->type) {
                                    'confirmation' => 'bg-blue-100 text-blue-700',
                                    'admin_notification' => 'bg-amber-100 text-amber-700',
                                    'cancelled_admin' => 'bg-amber-100 text-amber-700',
                                    'cancelled_by_employee' => 'bg-blue-100 text-blue-700',
                                    'confirmed_by_employee' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-gray-100 text-gray-600',
                                    };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $eventoClass }}">
                                        {{ $eventoLabel }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold w-fit {{ $destinatarioClass }}">
                                            {{ $destinatario }}
                                        </span>
                                        <span class="text-xs text-on-surface-variant">{{ $log->recipient_email }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if ($log->appointment)
                                    <span class="text-xs text-on-surface-variant">
                                        #{{ $log->appointment_id }} —
                                        {{ $log->appointment->company->name ?? '—' }}
                                    </span>
                                    @else
                                    <span class="text-xs text-on-surface-variant italic">Eliminada</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $log->status === 'sent' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                        {{ $log->status === 'sent' ? 'Enviado' : 'Error' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-xs text-error max-w-xs truncate">
                                    {{ $log->error_message ?? '—' }}
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-16">
                                    <div class="flex flex-col items-center gap-4">
                                        <span class="material-symbols-outlined text-4xl text-primary/30">mail_off</span>
                                        <p class="text-on-surface-variant text-sm">No hay registros de notificaciones</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile -->
                <div class="md:hidden space-y-4 p-4">
                    @forelse ($logs as $log)
                    <div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-on-surface-variant">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $log->status === 'sent' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ $log->status === 'sent' ? 'Enviado' : 'Error' }}
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-on-surface-variant">
                            {{ match($log->type) {
                                'confirmation'       => 'Cita confirmada',
                                'admin_notification' => 'Nueva reserva',
                                'cancelled_admin'    => 'Cancelación por cliente',
                                default              => ucfirst($log->type),
                            } }}
                        </p>
                        <p class="text-sm font-semibold text-on-surface">{{ $log->recipient_email }}</p>
                        <p class="text-xs text-on-surface-variant mt-1">
                            Cita #{{ $log->appointment_id ?? '—' }}
                            @if($log->appointment) — {{ $log->appointment->company->name ?? '' }} @endif
                        </p>
                        @if ($log->error_message)
                        <p class="text-xs text-error mt-2">{{ $log->error_message }}</p>
                        @endif
                    </div>
                    @empty
                    <p class="text-center text-sm text-on-surface-variant py-8">No hay registros</p>
                    @endforelse
                </div>

                <!-- PAGINACIÓN -->
                <div class="px-6 py-4 border-t border-outline-variant/20">
                    {{ $logs->links() }}
                </div>

            </div>
        </div>
    </main>
</x-app-layout>
