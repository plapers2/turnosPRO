{{-- resources/views/livewire/appointments/calendar/view.blade.php --}}
{{-- wire:ignore: FullCalendar maneja su propio DOM. Livewire no debe re-renderizar este bloque. --}}

{{-- Leyenda de estados --}}
<div class="flex flex-wrap items-center gap-x-5 gap-y-2 px-1 mb-3">
    @foreach ([
        ['color' => '#BA7517', 'bg' => '#FAEEDA', 'label' => 'Pendiente'],
        ['color' => '#0F6E56', 'bg' => '#E1F5EE', 'label' => 'Confirmada'],
        ['color' => '#185FA5', 'bg' => '#E6F1FB', 'label' => 'Completada'],
        ['color' => '#A32D2D', 'bg' => '#FCEBEB', 'label' => 'Cancelada'],
    ] as $s)
        <span class="inline-flex items-center gap-1.5 text-xs text-on-surface-variant">
            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                  style="background:{{ $s['color'] }}"></span>
            {{ $s['label'] }}
        </span>
    @endforeach
</div>

{{-- Contenedor del calendario --}}
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm overflow-hidden">
    <div id="fullcalendar" class="p-4"></div>
</div>

<script>
(function () {
    var EVENTS       = @json($calendarEvents);
    var INITIAL_DATE = '{{ \Carbon\Carbon::parse($calendarMonth . '-01')->format('Y-m-d') }}';

    /* ── CSS personalizado para el toolbar y eventos ── */
    var style = document.createElement('style');
    style.textContent = [
        /* Toolbar */
        '.fc .fc-toolbar.fc-header-toolbar{margin-bottom:1rem;flex-wrap:wrap;gap:.5rem}',
        '.fc .fc-toolbar-title{font-size:15px!important;font-weight:600!important}',
        /* Botones */
        '.fc .fc-button{font-size:12px!important;font-weight:500!important;padding:5px 12px!important;border-radius:8px!important;border:1px solid rgba(0,0,0,.12)!important;background:#fff!important;color:#444!important;box-shadow:none!important;text-transform:capitalize!important}',
        '.fc .fc-button:hover{background:#f4f4f2!important}',
        '.fc .fc-button-primary:not(:disabled).fc-button-active{background:#1D9E75!important;border-color:#1D9E75!important;color:#fff!important}',
        '.fc .fc-button-group .fc-button{border-radius:0!important}',
        '.fc .fc-button-group .fc-button:first-child{border-radius:8px 0 0 8px!important}',
        '.fc .fc-button-group .fc-button:last-child{border-radius:0 8px 8px 0!important}',
        /* Cabecera días */
        '.fc .fc-col-header-cell-cushion{font-size:12px;font-weight:600;text-decoration:none;color:#888}',
        /* Números de día */
        '.fc .fc-daygrid-day-number{font-size:12px;text-decoration:none;color:#444;padding:4px 6px}',
        '.fc .fc-day-today{background:rgba(29,158,117,.06)!important}',
        '.fc .fc-day-today .fc-daygrid-day-number{color:#1D9E75;font-weight:700}',
        /* Eventos */
        '.fc-event{border:none!important;border-radius:6px!important;padding:2px 6px!important;font-size:11px!important;cursor:pointer!important}',
        '.fc-event:hover{filter:brightness(.93)}',
        /* Slots (vista semana/día) */
        '.fc .fc-timegrid-slot{height:2.5rem}',
        '.fc .fc-timegrid-slot-label{font-size:11px;color:#aaa}',
        /* Scroll */
        '.fc .fc-scroller{overflow-y:auto!important}',
    ].join('');
    document.head.appendChild(style);

    /* ── Monta o reconstruye el calendario ── */
    function mountCalendar() {
        var el = document.getElementById('fullcalendar');
        if (!el || typeof FullCalendar === 'undefined') return false;
        if (el.offsetParent === null && el.offsetHeight === 0) return false;

        if (window._calendarInstance) {
            window._calendarInstance.destroy();
            window._calendarInstance = null;
        }

        window._calendarInstance = new FullCalendar.Calendar(el, {
            /* ── Vistas ── */
            initialView:  'dayGridMonth',
            initialDate:  INITIAL_DATE,
            locale:       'es',

            /* ── Toolbar con las 3 vistas ── */
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,timeGridWeek,timeGridDay',
            },

            /* ── Textos en español ── */
            buttonText: {
                today:        'Hoy',
                month:        'Mes',
                week:         'Semana',
                day:          'Día',
            },

            /* ── Configuración general ── */
            height:         'auto',
            scrollTime:     '08:00:00',
            slotMinTime:    '06:00:00',
            slotMaxTime:    '22:00:00',
            allDaySlot:     false,
            nowIndicator:   true,
            eventMaxStack:  3,

            /* ── Eventos ── */
            events: EVENTS,

            /* ── Render de evento: color por estado ── */
            eventDidMount: function (info) {
                /* El color ya viene en el evento desde PHP, lo aplicamos al bg */
                var color = info.event.extendedProps.statusColor || info.event.backgroundColor;
                if (color) {
                    info.el.style.backgroundColor = color;
                    info.el.style.borderColor     = color;
                }
            },

            /* ── Click en evento: abre modal de Livewire ── */
            eventClick: function (info) {
                var wireEl = el.closest('[wire\\:id]');
                if (wireEl) {
                    Livewire.find(wireEl.getAttribute('wire:id'))
                            .call('viewAppointment', info.event.id);
                }
            },

            /* ── Contenido del evento ── */
            eventContent: function (arg) {
                var view  = arg.view.type;
                var title = arg.event.title;
                var extra = arg.event.extendedProps;

                if (view === 'dayGridMonth') {
                    return {
                        html: '<div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding:1px 2px">'
                            + title + '</div>'
                    };
                }

                /* Vista semana / día: más detalle */
                return {
                    html: '<div style="padding:2px 4px;line-height:1.3">'
                        + '<div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'
                        + title + '</div>'
                        + (extra.professional
                            ? '<div style="font-size:10px;opacity:.85">' + extra.professional + '</div>'
                            : '')
                        + (extra.services
                            ? '<div style="font-size:10px;opacity:.75">' + extra.services + '</div>'
                            : '')
                        + '</div>'
                };
            },
        });

        window._calendarInstance.render();
        return true;
    }

    /* ── Retry: resuelve carrera de timing en primera carga ── */
    function mountWithRetry() {
        if (mountCalendar()) return;
        var attempts = 0;
        var timer = setInterval(function () {
            attempts++;
            if (mountCalendar() || attempts >= 20) clearInterval(timer);
        }, 100);
    }

    /* ── Eventos del sistema ── */
    window.addEventListener('calendar-view-shown', function () {
        mountWithRetry();
    });

    window.addEventListener('calendar-events-updated', function (e) {
        EVENTS = e.detail.events;
        if (window._calendarInstance) {
            window._calendarInstance.removeAllEvents();
            window._calendarInstance.addEventSource(e.detail.events);
        } else {
            mountWithRetry();
        }
    });

    /* ── Intento inicial ── */
    mountWithRetry();
})();
</script>
