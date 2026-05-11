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

                    // Altura mínima para que ningún evento quede microscópico
                    eventMinHeight: 28,

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

                    eventContent: (arg) => {
                        const { title, start, end, extendedProps } = arg.event;
                        const props = extendedProps ?? {};

                        // Calcular duración en minutos (si end está disponible)
                        const durationMin =
                            start && end ? (end - start) / 60000 : 60;

                        // Citas muy cortas (≤ 20 min): una sola línea con tooltip
                        if (durationMin <= 20) {
                            return {
                                html: `
                                    <div class="fc-event-compact"
                                         title="${this._escAttr(title)} | ${this._escAttr(props.professional)} | ${this._escAttr(props.services)}">
                                        <span class="fc-event-compact__dot">●</span>
                                        <span class="fc-event-compact__title">${this._escHtml(title)}</span>
                                    </div>`,
                            };
                        }

                        // Citas de duración media (21–40 min): título + profesional
                        if (durationMin <= 40) {
                            return {
                                html: `
                                    <div class="fc-event-inner fc-event-inner--md">
                                        <div class="fc-event-inner__title">${this._escHtml(title)}</div>
                                        <div class="fc-event-inner__sub">${this._escHtml(props.professional ?? "")}</div>
                                    </div>`,
                            };
                        }

                        // Citas largas (> 40 min): título + profesional + servicios
                        return {
                            html: `
                                <div class="fc-event-inner fc-event-inner--lg">
                                    <div class="fc-event-inner__title">${this._escHtml(title)}</div>
                                    <div class="fc-event-inner__sub">${this._escHtml(props.professional ?? "")}</div>
                                    <div class="fc-event-inner__sub">${this._escHtml(props.services ?? "")}</div>
                                </div>`,
                        };
                    },
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

        // ── Helpers de escape ───────────────────────────────
        _escHtml(str) {
            return String(str ?? "")
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;");
        },
        _escAttr(str) {
            return String(str ?? "")
                .replace(/"/g, "&quot;")
                .replace(/\n/g, " ");
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
