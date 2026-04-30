{{-- resources/views/livewire/appointments/list/table.blade.php --}}
{{-- Props: $appointments (LengthAwarePaginator<Appointment>) --}}
<div class="table-wrapper hidden-mobile">
    <table class="appt-table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Profesional</th>
                <th>Servicio(s)</th>
                <th>Fecha y hora</th>
                <th>Duración</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($appointments as $appt)
                @include('livewire.appointments.list.table-row', ['appt' => $appt])
            @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        No hay citas con los filtros aplicados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
