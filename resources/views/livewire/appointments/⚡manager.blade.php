{{-- resources/views/livewire/appointments/manager.blade.php --}}
<div x-data="{
    view: '{{ $view }}',
    init() {
        const saved = localStorage.getItem('appt_view');
        if (saved && ['list', 'calendar'].includes(saved)) {
            this.view = saved;
        }
        this.$watch('view', (val) => {
            localStorage.setItem('appt_view', val);
            $wire.set('view', val);
            if (val === 'calendar') {
                this.$nextTick(() => {
                    window.dispatchEvent(new CustomEvent('calendar-view-shown'));
                });
            }
        });
        if (this.view === 'calendar') {
            this.$nextTick(() => {
                window.dispatchEvent(new CustomEvent('calendar-view-shown'));
            });
        }
    }
}" class="appt-manager">

    @include('livewire.appointments.partials.toast')
    @include('livewire.appointments.partials.header', ['total' => $stats['total']])
    @include('livewire.appointments.partials.stats-row', ['stats' => $stats])
    @include('livewire.appointments.partials.filters-bar', ['professionals' => $professionals])

    <div wire:loading class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- Vista Lista --}}
    <div x-show="view === 'list'">
        @include('livewire.appointments.list.table', ['appointments' => $appointments])
        @include('livewire.appointments.list.cards', ['appointments' => $appointments])
        @include('livewire.appointments.partials.pagination', ['paginator' => $appointments])
    </div>

    {{-- Vista Calendario --}}
    {{-- ⚠️ Sin wire:ignore aquí: ya está dentro de calendar/view.blade.php --}}
    <div x-show="view === 'calendar'">
        @include('livewire.appointments.calendar.nav', ['calendarMonth' => $calendarMonth])
        @include('livewire.appointments.calendar.view', ['calendarEvents' => $calendarEvents])
    </div>

    @if ($showModal && $selectedAppt)
        @include('livewire.appointments.modals.appointment-detail', ['appt' => $selectedAppt])
    @endif

    @if ($showConfirmConfirm)
        @include('livewire.appointments.modals.confirm-confirm')
    @endif

    @if ($showCancelConfirm)
        @include('livewire.appointments.modals.cancel-confirm')
    @endif

    @if ($showCompleteConfirm)
        @include('livewire.appointments.modals.complete-confirm')
    @endif

</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {

            // Livewire emitió eventos nuevos → los pasa al calendario via evento JS
            // Esto NO causa re-render, solo mueve datos
            Livewire.on('calendarEventsUpdated', ({
                events
            }) => {
                window.dispatchEvent(new CustomEvent('calendar-events-updated', {
                    detail: {
                        events
                    }
                }));
            });

            // El JS del calendario hace dispatch('calendarEventClicked')
            // Livewire lo recibe aquí y llama viewAppointment en el componente PHP
            Livewire.on('calendarEventClicked', ({
                id
            }) => {
                // El componente PHP maneja esto con #[On('calendarEventClicked')]
                // No hace falta código adicional aquí
            });

        });
    </script>
@endpush
