<div id="fullcalendar" class="calendar-wrapper"></div>

<script>
    (function() {
        var EVENTS = @json($calendarEvents);
        var INITIAL_DATE = '{{ \Carbon\Carbon::parse($calendarMonth . '-01')->format('Y-m-d') }}';

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

        window.addEventListener('calendar-month-changed', function(e) {
            if (window._calendarInstance) {
                window._calendarInstance.gotoDate(e.detail.month + '-01');
            }
        });

        if (!mountCalendar()) {
            window.addEventListener('calendar-view-shown', function handler() {
                mountCalendar();
                window.removeEventListener('calendar-view-shown', handler);
            });
        }

        window.addEventListener('calendar-events-updated', function(e) {
            EVENTS = e.detail.events;
            if (window._calendarInstance) {
                window._calendarInstance.removeAllEvents();
                window._calendarInstance.addEventSource(e.detail.events);
            } else {
                mountCalendar();
            }
        });
    })();
</script>
