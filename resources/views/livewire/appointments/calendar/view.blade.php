{{-- resources/views/livewire/appointments/calendar/view.blade.php --}}
<div id="fullcalendar-wrapper">
    <div id="fullcalendar" class="calendar-wrapper"></div>
</div>

<script>
    (function() {
        function mountCalendar(events) {
            const el = document.getElementById('fullcalendar');
            if (!el || typeof FullCalendar === 'undefined') return;

            if (window._calendarInstance) {
                window._calendarInstance.destroy();
                window._calendarInstance = null;
            }

            window._calendarInstance = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: false,
                events: events ?? [],
                height: 'auto',
                eventClick: (info) => {
                    const wireEl = el.closest('[wire\\:id]');
                    if (wireEl) {
                        Livewire.find(wireEl.getAttribute('wire:id'))
                            .call('viewAppointment', info.event.id);
                    }
                },
                eventContent: (arg) => ({
                    html: '<div class="fc-event-inner">' + arg.event.title + '</div>'
                }),
            });

            window._calendarInstance.render();
        }

        // Espera a que el div sea visible antes de montar
        window._mountCalendarWithEvents = function(events) {
            const el = document.getElementById('fullcalendar');
            if (!el) return;

            // Si ya es visible, montar directo
            if (el.offsetParent !== null) {
                mountCalendar(events);
                return;
            }

            // Si está oculto, observar hasta que sea visible
            const observer = new MutationObserver(() => {
                if (el.offsetParent !== null) {
                    observer.disconnect();
                    mountCalendar(events);
                }
            });
            observer.observe(document.body, {
                attributes: true,
                subtree: true,
                attributeFilter: ['style', 'class']
            });
        };

        // Montaje inicial
        window._mountCalendarWithEvents(@json($calendarEvents));

        // Actualización de eventos vía Livewire dispatch
        window.addEventListener('calendar-events-updated', (e) => {
            if (!window._calendarInstance) {
                window._mountCalendarWithEvents(e.detail.events);
                return;
            }
            window._calendarInstance.removeAllEvents();
            window._calendarInstance.addEventSource(e.detail.events);
        });
    })();
</script>
