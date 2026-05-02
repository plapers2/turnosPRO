// resources/js/appointments.js
// Lógica Alpine del gestor de citas + persistencia de vista

/**
 * Alpine component: appointmentsManager()
 * Gestiona:
 *  - Estado de vista (list | calendar)
 *  - Instancia de FullCalendar
 *  - Persistencia en localStorage
 */
function appointmentsManager() {
    return {
        view: "list",
        calendarInitialized: false,
        calendarInstance: null,

        // ── Inicialización ──────────────────────────────────
        init() {
            // Restaurar vista guardada
            const saved = localStorage.getItem("appt_view");
            if (saved && ["list", "calendar"].includes(saved)) {
                this.view = saved;
                // Sincronizar con Livewire
                this.$nextTick(() => {
                    if (window.Livewire) {
                        Livewire.find(
                            this.$el
                                .closest("[wire\\:id]")
                                ?.getAttribute("wire:id"),
                        )?.set("view", saved);
                    }
                });
            }

            // Observar cambios de vista para persistir
            this.$watch("view", (val) => {
                localStorage.setItem("appt_view", val);

                if (val === "calendar") {
                    this.$nextTick(() => this.initCalendar());
                }
            });

            // Escuchar eventos Livewire después de inicializar
            document.addEventListener("livewire:initialized", () => {
                Livewire.on("calendarEventsUpdated", ({ events }) => {
                    window.dispatchEvent(
                        new CustomEvent("calendar-events-updated", {
                            detail: { events },
                        }),
                    );
                });
            });
        },

        // ── FullCalendar ────────────────────────────────────
        initCalendar(events = []) {
            if (this.calendarInitialized) return;

            this.$nextTick(() => {
                const el = document.getElementById("fullcalendar");
                if (!el || typeof FullCalendar === "undefined") return;

                this.calendarInstance = new FullCalendar.Calendar(el, {
                    initialView: "dayGridMonth",
                    locale: "es",
                    headerToolbar: false,
                    events: events,
                    height: "auto",

                    eventClick: (info) => {
                        const wireId = this.$el
                            .closest("[wire\\:id]")
                            ?.getAttribute("wire:id");
                        if (wireId) {
                            Livewire.find(wireId)?.call(
                                "viewAppointment",
                                info.event.id,
                            );
                        }
                    },

                    eventContent: (arg) => ({
                        html: `<div class="fc-event-inner">${arg.event.title}</div>`,
                    }),
                });

                this.calendarInstance.render();
                this.calendarInitialized = true;
            });
        },

        refreshCalendar(events) {
            if (!this.calendarInstance) return;
            this.calendarInstance.removeAllEvents();
            this.calendarInstance.addEventSource(events);
        },
    };
}

// Registrar en Alpine si ya está disponible, o esperar al evento
if (typeof Alpine !== "undefined") {
    Alpine.data("appointmentsManager", appointmentsManager);
} else {
    document.addEventListener("alpine:init", () => {
        Alpine.data("appointmentsManager", appointmentsManager);
    });
}
