{{-- resources/views/livewire/appointments/list/table.blade.php --}}
<div
    class="hidden md:block bg-surface-container-lowest rounded-2xl
            border border-outline-variant/40 shadow-[0_1px_6px_rgba(95,94,90,0.06)] overflow-hidden">
    <table class="w-full border-collapse text-sm">
        <thead>
            <tr class="border-b border-outline-variant/30 bg-surface-container/60">
                @foreach (['Cliente', 'Profesional', 'Servicio(s)', 'Fecha y hora', 'Duración', 'Estado', 'Acciones'] as $col)
                    <th
                        class="px-4 py-3 text-left text-[10.5px] font-semibold
                               text-on-surface-variant uppercase tracking-wider">
                        {{ $col }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($appointments as $appt)
                @include('livewire.appointments.list.table-row', ['appt' => $appt])
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div
                                class="w-12 h-12 rounded-full bg-surface-container
                                        flex items-center justify-center text-on-surface-variant/50">
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                                    <rect x="1" y="3" width="20" height="17" rx="3" stroke="currentColor"
                                        stroke-width="1.5" />
                                    <path d="M1 8h20M7 1v3M15 1v3" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <p class="text-[13px] text-on-surface-variant">No hay citas con los filtros aplicados.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
