// resources/js/appointments.js

function appointmentsManager(companyId) {
    return {
        view: "list",
        calendarInitialized: false,
        calendarInstance: null,
        companyId: companyId ?? null,

        // ── Inicialización ──────────────────────────────────
        init() {
            // Restaurar vista guardada
            const saved = localStorage.getItem("appt_view");
            if (saved && ["list", "calendar", "availability"].includes(saved)) {
                this.view = saved;
            }

            this.$watch("view", (val) => {
                localStorage.setItem("appt_view", val);
                if (typeof $wire !== "undefined") $wire.set("view", val);
                if (val === "calendar") {
                    this.$nextTick(() =>
                        window.dispatchEvent(
                            new CustomEvent("calendar-view-shown"),
                        ),
                    );
                }
            });

            document.addEventListener("livewire:initialized", () => {
                // Sincronizar vista restaurada al inicio
                const saved = localStorage.getItem("appt_view");
                if (
                    saved &&
                    ["list", "calendar", "availability"].includes(saved)
                ) {
                    const wireId = this.$el
                        .closest("[wire\\:id]")
                        ?.getAttribute("wire:id");
                    Livewire.find(wireId)?.set("view", saved);
                }

                // sincronizar Alpine para que $watch se dispare y calendar-view-shown se emita
                Livewire.hook("commit", ({ component, respond }) => {
                    respond(() => {
                        const newView = component.canonical?.view;
                        if (newView && newView !== this.view) {
                            this.view = newView;
                        }
                    });
                });

                Livewire.on("calendarEventsUpdated", ({ events }) => {
                    window.dispatchEvent(
                        new CustomEvent("calendar-events-updated", {
                            detail: { events },
                        }),
                    );
                });

                this.subscribeToRealtime();
            });
        },

        // ── Tiempo real ─────────────────────────────────────
        subscribeToRealtime() {
            if (!window.Echo || !this.companyId) {
                console.warn("[Reverb] Echo no disponible o companyId nulo", {
                    echo: !!window.Echo,
                    companyId: this.companyId,
                });
                return;
            }

            console.log(
                "[Reverb] Suscribiendo al canal appointments." + this.companyId,
            );

            window.Echo.channel("appointments." + this.companyId).listen(
                ".appointment.updated",
                (data) => {
                    console.log("[Reverb] Evento recibido:", data);
                    this.onRealtimeUpdate(data);
                },
            );
        },

        onRealtimeUpdate(data) {
            const wireId = this.$el
                .closest("[wire\\:id]")
                ?.getAttribute("wire:id");
            const component = wireId ? Livewire.find(wireId) : null;

            // 1. Refrescar lista y estadísticas
            component?.$refresh();

            // 2. Refrescar calendario si está activo
            if (this.view === "calendar") {
                component?.call("refreshCalendarEvents");
            }

            // 3. Toast
            this.showRealtimeNotification(data);
        },

        showRealtimeNotification(data) {
            const labels = {
                pending: {
                    text: "Nueva cita pendiente",
                },
                confirmed: { text: "Cita confirmada" },
                cancelled: { text: "Cita cancelada" },
                completed: { text: "Cita completada" },
            };

            const info = labels[data.status] ?? {
                text: "Cita actualizada",
                icon: "🔔",
            };

            console.log(data);
            window.dispatchEvent(
                new CustomEvent("notify", {
                    detail: {
                        type:
                            data.status === "cancelled" ? "warning" : "success",
                        message: `${info.text}: ${data.customer_name} — ${data.start_time}`,
                    },
                }),
            );
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
                    eventMinHeight: 28,

                    eventClick: (info) => {
                        const wireId = this.$el
                            .closest("[wire\\:id]")
                            ?.getAttribute("wire:id");
                        if (wireId)
                            Livewire.find(wireId)?.call(
                                "viewAppointment",
                                info.event.id,
                            );
                    },

                    eventContent: (arg) => {
                        const {
                            title,
                            start,
                            end,
                            extendedProps: props = {},
                        } = arg.event;
                        const durationMin =
                            start && end ? (end - start) / 60000 : 60;

                        if (durationMin <= 20) {
                            return {
                                html: `<div class="fc-event-compact"
                                            title="${this._esc(title)} | ${this._esc(props.professional)} | ${this._esc(props.services)}">
                                           <span class="fc-event-compact__dot">●</span>
                                           <span class="fc-event-compact__title">${this._escHtml(title)}</span>
                                       </div>`,
                            };
                        }
                        if (durationMin <= 40) {
                            return {
                                html: `<div class="fc-event-inner fc-event-inner--md">
                                           <div class="fc-event-inner__title">${this._escHtml(title)}</div>
                                           <div class="fc-event-inner__sub">${this._escHtml(props.professional ?? "")}</div>
                                       </div>`,
                            };
                        }
                        return {
                            html: `<div class="fc-event-inner fc-event-inner--lg">
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

        _escHtml: (s) =>
            String(s ?? "")
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;"),
        _esc: (s) =>
            String(s ?? "")
                .replace(/"/g, "&quot;")
                .replace(/\n/g, " "),
    };
}

window.appointmentsManager = appointmentsManager;
