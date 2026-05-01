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
    <div x-show="view === 'calendar'">
        @include('livewire.appointments.calendar.nav', ['calendarMonth' => $calendarMonth])
        <div wire:ignore>
            @include('livewire.appointments.calendar.view', ['calendarEvents' => $calendarEvents])
        </div>
    </div>

    @if ($showModal && $selectedAppt)
        @include('livewire.appointments.modals.appointment-detail', ['appt' => $selectedAppt])
    @endif

    @if ($showCancelConfirm)
        @include('livewire.appointments.modals.cancel-confirm')
    @endif

</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('calendarEventsUpdated', ({
                events
            }) => {
                window.dispatchEvent(
                    new CustomEvent('calendar-events-updated', {
                        detail: {
                            events
                        }
                    })
                );
            });

            Livewire.on('calendarMonthChanged', ({
                month
            }) => {
                window.dispatchEvent(
                    new CustomEvent('calendar-month-changed', {
                        detail: {
                            month
                        }
                    })
                );
            });
        });
    </script>
@endpush
