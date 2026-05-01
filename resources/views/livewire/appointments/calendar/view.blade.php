<div id="fullcalendar" class="calendar-wrapper"></div>

<script>
    (function() {
        var EVENTS = @json($calendarEvents);
        var INITIAL_DATE = '{{ \Carbon\Carbon::parse($calendarMonth . '-01')->format('Y-m-d') }}';

        /* ── Intenta montar el calendario ───────────────────────────────────
           Devuelve true si lo logra, false si algo aún no está listo.       */
        function mountCalendar() {
            var el = document.getElementById('fullcalendar');
            if (!el || typeof FullCalendar === 'undefined') return false;

            // El div está oculto por Alpine (x-show), no montar todavía
            if (el.offsetParent === null && el.offsetHeight === 0) return false;

            if (window._calendarInstance) {
                window._calendarInstance.destroy();
                window._calendarInstance = null;
            }

            window._calendarInstance = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                initialDate: INITIAL_DATE,
                locale: 'es',
                headerToolbar: false,
                events: EVENTS,
                height: 'auto',
                eventClick: function(info) {
                    var wireEl = el.closest('[wire\\:id]');
                    if (wireEl) {
                        Livewire.find(wireEl.getAttribute('wire:id'))
                            .call('viewAppointment', info.event.id);
                    }
                },
                eventContent: function(arg) {
                    return {
                        html: '<div class="fc-event-inner">' + arg.event.title + '</div>'
                    };
                },
            });

            window._calendarInstance.render();
            return true;
        }

        /* ── Retry con back-off: resuelve la carrera de timing en la primera
           carga. Intenta hasta 20 veces cada 100 ms (= 2 s máximo).
           En cuanto monta, cancela el intervalo.                            */
        function mountWithRetry() {
            if (mountCalendar()) return;

            var attempts = 0;
            var timer = setInterval(function() {
                attempts++;
                if (mountCalendar() || attempts >= 20) {
                    clearInterval(timer);
                }
            }, 100);
        }

        /* ── Evento emitido por Alpine cuando el panel se hace visible ──── */
        window.addEventListener('calendar-view-shown', function() {
            // Usar retry en vez de un intento único
            mountWithRetry();
        });

        /* ── Navegación de mes ──────────────────────────────────────────── */
        window.addEventListener('calendar-month-changed', function(e) {
            if (window._calendarInstance) {
                window._calendarInstance.gotoDate(e.detail.month + '-01');
            }
        });

        /* ── Actualización de eventos vía Livewire ──────────────────────── */
        window.addEventListener('calendar-events-updated', function(e) {
            EVENTS = e.detail.events;
            if (window._calendarInstance) {
                window._calendarInstance.removeAllEvents();
                window._calendarInstance.addEventSource(e.detail.events);
            } else {
                mountWithRetry();
            }
        });

        /* ── Intento inicial: si la vista arranca ya en "calendar" ─────── */
        mountWithRetry();
    })();
</script>
