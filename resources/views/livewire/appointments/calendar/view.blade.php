{{-- resources/views/livewire/appointments/calendar/view.blade.php --}}
{{-- wire:ignore protege todo este bloque de re-renders de Livewire      --}}
<div wire:ignore>

    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 shadow-sm overflow-hidden">
        <div id="fullcalendar" class="p-4"></div>
    </div>

    <script>
        (function() {
            var EVENTS = @json($calendarEvents);
            var INITIAL_DATE = '{{ \Carbon\Carbon::parse($calendarMonth . '-01')->format('Y-m-d') }}';

            /* ── Estilos ── */
            if (!document.getElementById('fc-custom-style')) {
                var style = document.createElement('style');
                style.id = 'fc-custom-style';
                style.textContent = [
                    '.fc .fc-toolbar-title{display:none}',
                    '.fc .fc-toolbar.fc-header-toolbar{margin-bottom:1rem;justify-content:flex-end}',
                    '.fc .fc-button{font-size:12px!important;font-weight:500!important;padding:5px 12px!important;border-radius:8px!important;border:1px solid rgba(0,0,0,.12)!important;background:#fff!important;color:#555!important;box-shadow:none!important;text-transform:capitalize!important}',
                    '.fc .fc-button:hover{background:#f4f4f2!important}',
                    '.fc .fc-button-primary:not(:disabled).fc-button-active{background:#1D9E75!important;border-color:#0F6E56!important;color:#fff!important}',
                    '.fc .fc-button-group .fc-button{border-radius:0!important}',
                    '.fc .fc-button-group .fc-button:first-child{border-radius:8px 0 0 8px!important}',
                    '.fc .fc-button-group .fc-button:last-child{border-radius:0 8px 8px 0!important}',
                    '.fc .fc-col-header-cell-cushion{font-size:12px;font-weight:600;text-decoration:none;color:#888}',
                    '.fc .fc-daygrid-day-number{font-size:12px;text-decoration:none;color:#444;padding:4px 6px}',
                    '.fc .fc-day-today{background:rgba(29,158,117,.06)!important}',
                    '.fc .fc-day-today .fc-daygrid-day-number{color:#1D9E75;font-weight:700}',
                    '.fc-daygrid-event{border-radius:6px!important;padding:1px 5px!important;font-size:11px!important;cursor:pointer!important;border:none!important}',
                    '.fc-daygrid-event:hover{filter:brightness(.92)}',
                    '.fc-event .fc-event-title{color:#fff!important;font-weight:500}',
                    '.fc-event .fc-event-time{color:rgba(255,255,255,.85)!important;font-size:10px}',
                    '.fc-timegrid-event{border-radius:6px!important;border:none!important;cursor:pointer!important}',
                    '.fc-timegrid-event:hover{filter:brightness(.92)}',
                    '.fc .fc-timegrid-slot{height:2.5rem}',
                    '.fc .fc-timegrid-slot-label{font-size:11px;color:#aaa}',
                    '.fc .fc-timegrid-now-indicator-line{border-color:#1D9E75}',
                    '.fc .fc-timegrid-now-indicator-arrow{border-top-color:#1D9E75;border-bottom-color:#1D9E75}',
                ].join('');
                document.head.appendChild(style);
            }

            var STATUS_COLORS = {
                pending: '#BA7517',
                confirmed: '#1D9E75',
                completed: '#378ADD',
                cancelled: '#E24B4A',
            };

            /* ── Título del nav según vista ── */
            function syncNavTitle() {
                var cal = window._calendarInstance;
                var titleEl = document.getElementById('cal-title');
                if (!cal || !titleEl) return;

                var view = cal.view;
                var viewType = view.type;
                var date = cal.getDate();
                var locale = 'es-ES';

                if (viewType === 'dayGridMonth') {
                    titleEl.textContent = date.toLocaleDateString(locale, {
                        month: 'long',
                        year: 'numeric'
                    });
                } else if (viewType === 'timeGridWeek') {
                    var start = new Date(view.currentStart);
                    var end = new Date(view.currentEnd);
                    end.setDate(end.getDate() - 1);
                    titleEl.textContent = start.toLocaleDateString(locale, {
                            day: 'numeric',
                            month: 'short'
                        }) +
                        ' – ' + end.toLocaleDateString(locale, {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                } else if (viewType === 'timeGridDay') {
                    titleEl.textContent = date.toLocaleDateString(locale, {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                }
            }

            /* ── Bind de botones del nav ──
               Usa delegación sobre document para sobrevivir re-renders de Livewire
               (los botones están FUERA del wire:ignore y pueden ser reemplazados) */
            function bindNavButtons() {
                document.addEventListener('click', function(e) {
                    var cal = window._calendarInstance;
                    if (!cal) return;

                    if (e.target.closest('#cal-prev')) {
                        cal.prev();
                        syncNavTitle();
                        syncLivewireIfMonth(cal);
                    }

                    if (e.target.closest('#cal-next')) {
                        cal.next();
                        syncNavTitle();
                        syncLivewireIfMonth(cal);
                    }
                });
            }

            /* En vista mes notifica a Livewire para refrescar eventos del servidor */
            function syncLivewireIfMonth(cal) {
                if (cal.view.type !== 'dayGridMonth') return;
                var month = cal.getDate().toISOString().slice(0, 7);
                var wireEl = document.getElementById('fullcalendar').closest('[wire\\:id]');
                if (wireEl) Livewire.find(wireEl.getAttribute('wire:id')).call('goToMonth', month);
            }

            /* ── Monta el calendario ── */
            function mountCalendar() {
                var el = document.getElementById('fullcalendar');
                if (!el || typeof FullCalendar === 'undefined') return false;
                if (el.offsetParent === null && el.offsetHeight === 0) return false;

                if (window._calendarInstance) {
                    window._calendarInstance.destroy();
                    window._calendarInstance = null;
                }

                window._calendarInstance = new FullCalendar.Calendar(el, {
                    initialView: 'dayGridMonth',
                    initialDate: INITIAL_DATE,
                    locale: 'es',

                    headerToolbar: {
                        left: '',
                        center: '',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay',
                    },

                    buttonText: {
                        month: 'Mes',
                        week: 'Semana',
                        day: 'Día',
                    },

                    height: 'auto',
                    scrollTime: '08:00:00',
                    slotMinTime: '06:00:00',
                    slotMaxTime: '22:00:00',
                    allDaySlot: false,
                    nowIndicator: true,
                    eventMaxStack: 3,
                    events: EVENTS,

                    /* Sincroniza título del nav en cada cambio de vista o fecha */
                    datesSet: function() {
                        syncNavTitle();
                    },

                    eventDidMount: function(info) {
                        var color = STATUS_COLORS[info.event.extendedProps.status] || '#BA7517';
                        info.el.style.backgroundColor = color;
                        info.el.style.borderColor = color;
                        var inner = info.el.querySelector('.fc-event-main');
                        if (inner) inner.style.color = '#fff';
                    },

                    eventContent: function(arg) {
                        var isMonth = arg.view.type === 'dayGridMonth';
                        var e = arg.event;
                        var ext = e.extendedProps;

                        if (isMonth) {
                            return {
                                html: '<div class="fc-event-main" style="padding:1px 3px;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' +
                                    e.title + '</div>'
                            };
                        }

                        return {
                            html: '<div style="padding:3px 5px;line-height:1.35;color:#fff">' +
                                '<div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' +
                                e.title + '</div>' +
                                (ext.professional ? '<div style="font-size:10px;opacity:.85">' + ext
                                    .professional + '</div>' : '') +
                                (ext.services ? '<div style="font-size:10px;opacity:.75">' + ext.services +
                                    '</div>' : '') +
                                '</div>'
                        };
                    },

                    eventClick: function(info) {
                        var wireEl = document.getElementById('fullcalendar').closest('[wire\\:id]');
                        if (wireEl) {
                            Livewire.find(wireEl.getAttribute('wire:id'))
                                .call('viewAppointment', info.event.id);
                        }
                    },
                });

                window._calendarInstance.render();
                syncNavTitle();
                return true;
            }

            /* ── Retry ── */
            function mountWithRetry() {
                if (mountCalendar()) return;
                var attempts = 0;
                var timer = setInterval(function() {
                    attempts++;
                    if (mountCalendar() || attempts >= 20) clearInterval(timer);
                }, 100);
            }

            /* ── Eventos del sistema ── */
            window.addEventListener('calendar-events-updated', function(e) {
                EVENTS = e.detail.events;
                if (window._calendarInstance) {
                    window._calendarInstance.removeAllEvents();
                    window._calendarInstance.addEventSource(e.detail.events);
                } else {
                    mountWithRetry();
                }
            });

            window.addEventListener('calendar-view-shown', function() {
                mountWithRetry();
            });

            /* ── Init ── */
            bindNavButtons();
            mountWithRetry();
        })();
    </script>

</div>
