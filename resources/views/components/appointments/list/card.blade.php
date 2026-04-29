{{-- resources/views/livewire/appointments/list/cards.blade.php --}}
{{-- Props: $appointments (LengthAwarePaginator<Appointment>) --}}
<div class="cards-grid show-mobile">
    @forelse ($appointments as $appt)
        @include('livewire.appointments.list.card', ['appt' => $appt])
    @empty
        <div class="empty-state">No hay citas con los filtros aplicados.</div>
    @endforelse
</div>
