{{-- resources/views/livewire/appointments/list/table.blade.php --}}
{{-- Props: $appointments (LengthAwarePaginator<Appointment>) --}}
<div
    class="hidden md:block bg-surface-container-lowest rounded-xl
            border border-outline-variant/20 shadow-sm overflow-hidden">
    <table class="w-full border-collapse text-sm">
        <thead>
            <tr class="border-b border-outline-variant/20">
                <th
                    class="px-4 py-3 text-left text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                    Cliente</th>
                <th
                    class="px-4 py-3 text-left text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                    Profesional</th>
                <th
                    class="px-4 py-3 text-left text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                    Servicio(s)</th>
                <th
                    class="px-4 py-3 text-left text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                    Fecha y hora</th>
                <th
                    class="px-4 py-3 text-left text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                    Duración</th>
                <th
                    class="px-4 py-3 text-left text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                    Estado</th>
                <th
                    class="px-4 py-3 text-left text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide">
                    Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($appointments as $appt)
                @include('livewire.appointments.list.table-row', ['appt' => $appt])
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-sm text-on-surface-variant italic">
                        No hay citas con los filtros aplicados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
