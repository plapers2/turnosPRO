{{-- resources/views/livewire/appointments/manager.blade.php --}}
{{--
    Componente raíz: solo orquesta includes y estado Alpine global.
    Emits: viewChanged, calendarEventsUpdated, notify
--}}
<div x-data="appointmentsManager()" x-init="init()" class="appt-manager">
    {{-- Toast global --}}
    @include('livewire.appointments.partials.toast')

    {{-- Encabezado + toggle de vista --}}
    @include('livewire.appointments.partials.header', [
        'total' => $stats['total'],
    ])

    {{-- Tarjetas de estadísticas --}}
    @include('livewire.appointments.partials.stats-row', [
        'stats' => $stats,
    ])

    {{-- Barra de filtros --}}
    @include('livewire.appointments.partials.filters-bar', [
        'professionals' => $professionals,
    ])

    {{-- Indicador de carga --}}
    <div wire:loading class="loading-bar">
        <div class="loading-bar__progress"></div>
    </div>

    {{-- ① Vista Lista --}}
    <div x-show="view === 'list'" x-cloak>
        @include('livewire.appointments.list.table', [
            'appointments' => $appointments,
        ])
        @include('livewire.appointments.list.cards', [
            'appointments' => $appointments,
        ])
        @include('livewire.appointments.partials.pagination', [
            'paginator' => $appointments,
        ])
    </div>

    {{-- ② Vista Calendario --}}
    <div x-show="view === 'calendar'" x-cloak>
        @include('livewire.appointments.calendar.nav', [
            'calendarMonth' => $calendarMonth,
        ])
        @include('livewire.appointments.calendar.view', [
            'calendarEvents' => $calendarEvents,
        ])
    </div>

    {{-- Modal: detalle de cita --}}
    @if ($showModal && $selectedAppt)
        @include('livewire.appointments.modals.appointment-detail', [
            'appt' => $selectedAppt,
        ])
    @endif

    {{-- Modal: confirmar cancelación --}}
    @if ($showCancelConfirm)
        @include('livewire.appointments.modals.cancel-confirm')
    @endif

</div>

@push('styles')
    @vite('resources/css/components/appointments.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    @vite('resources/js/appointments.js')
@endpush
