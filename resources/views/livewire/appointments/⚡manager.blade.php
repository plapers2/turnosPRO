{{-- resources/views/livewire/appointments/manager.blade.php --}}
@php
    $company_id = session('active_company_id');
    $todayFreeSlots = collect($availabilityDays)->firstWhere('date', now()->toDateString())['free_slots'] ?? null;
@endphp


<div x-data="appointmentsManager({{ $company_id }})" x-init="view = '{{ $view }}'" class="appt-manager">
    @include('livewire.appointments.partials.header', ['total' => $stats['total']])

    @if ($todayFreeSlots > 0 && $view !== 'availability')
        <div
            class="flex items-center justify-between gap-3
                bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-2.5 text-sm my-3">
            <div class="flex items-center gap-2 text-emerald-700 font-medium">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7
                         a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Hoy tienes <strong>{{ $todayFreeSlots }} espacios libres</strong>disponibles para agendar.
            </div>
            <button wire:click="$set('view', 'availability')"
                class="flex-shrink-0 text-xs font-semibold text-emerald-700
                   underline underline-offset-2 hover:text-emerald-900 transition-colors">
                Ver disponibilidad →
            </button>
        </div>
    @endif

    @if ($view !== 'availability')
        @include('livewire.appointments.partials.appointements-for-confirmed')

        @include('livewire.appointments.partials.stats-row', ['stats' => $stats])
        @include('livewire.appointments.partials.filters-bar', ['professionals' => $professionals]).
    @endif


    <div wire:loading class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- Vista Lista --}}
    {{-- Vista Lista --}}
    <div x-show="view === 'list'">
        @include('livewire.appointments.list.table', ['appointments' => $appointments])
        @include('livewire.appointments.list.cards', ['appointments' => $appointments])
        @include('livewire.appointments.partials.pagination', ['paginator' => $appointments])
    </div>

    {{-- Vista Calendario --}}
    <div x-show="view === 'calendar'">
        @include('livewire.appointments.calendar.nav', ['calendarMonth' => $calendarMonth])
        @include('livewire.appointments.calendar.view', ['calendarEvents' => $calendarEvents])
    </div>

    {{-- Vista Disponibilidad --}}
    <div x-show="view === 'availability'">
        @include('livewire.appointments.partials.availability', [
            'availabilityView' => $availabilityView,
            'availabilityDays' => $availabilityDays,
            'availabilitySummary' => $availabilitySummary,
            'professionals' => $professionals,
            'isAdmin' => $isAdmin,
            'slotMinutes' => $slotMinutes,
        ])
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
