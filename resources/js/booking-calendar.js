import { Calendar } from "@fullcalendar/core";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import esLocale from "@fullcalendar/core/locales/es";
import dayGridPlugin from "@fullcalendar/daygrid";

// Variables globales del blade — las lee del DOM para no depender de window
let citasOcupadas = {};
let disponiblesPorFecha = {};
let slotSeleccionado = null;
let totalProfesionales = 0;
const calendarEl = document.getElementById("calendar");
if (calendarEl) {
    const totalDuration = parseInt(calendarEl.dataset.duration);
    const companyId = parseInt(calendarEl.dataset.company);
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const DIAS_MAP = {
        Sunday: 0,
        Monday: 1,
        Tuesday: 2,
        Wednesday: 3,
        Thursday: 4,
        Friday: 5,
        Saturday: 6,
    };

    let selectedEvent = null;
    let calendar = null;
    let horariosPorDia = {};
    let paintTimer = null;

    async function init() {
        await cargarHorarios();

        const hoy = new Date();
        const dow = hoy.getDay();
        const inicioSemana = new Date(hoy);
        inicioSemana.setDate(hoy.getDate() - dow);
        const finSemana = new Date(inicioSemana);
        finSemana.setDate(inicioSemana.getDate() + 7);
        await cargarCitasOcupadas(
            formatDate(inicioSemana),
            formatDate(finSemana),
        );

        const { globalMin, globalMax } = getGlobalRange();
        let fetchId = 0;
        calendar = new Calendar(calendarEl, {
            plugins: [timeGridPlugin, interactionPlugin],
            locale: esLocale,
            initialView: "timeGridWeek",
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "timeGridWeek,timeGridDay",
            },
            buttonText: { today: "Hoy", week: "Semana", day: "Día" },
            slotDuration: "00:30:00",
            slotMinTime: globalMin,
            slotMaxTime: globalMax,
            allDaySlot: false,
            nowIndicator: true,
            selectable: false,
            selectMirror: false,
            validRange: {
                start: new Date(new Date().setDate(new Date().getDate() + 1)),
            },
            selectAllow: (info) => slotPermitido(info.start),
            dateClick: (info) => {
                if (slotPermitido(info.date)) onSlotSelected(info.date);
            },
            datesSet: async (info) => {
                const myId = ++fetchId;
                const start = info.startStr.substring(0, 10);
                const end = info.endStr.substring(0, 10);
                clearTimeout(paintTimer);
                await cargarCitasOcupadas(start, end);
                if (myId === fetchId) paintSlots();
            },
            events: [],
            eventColor: "#6366f1",
            height: "auto",
        });

        calendar.render();

        new MutationObserver((mutations) => {
            const soloOverlays = mutations.every((m) =>
                [...m.addedNodes, ...m.removedNodes].every((n) =>
                    n.classList?.contains("slot-overlay"),
                ),
            );
            if (!soloOverlays) schedulePaint();
        }).observe(calendarEl, { childList: true, subtree: true });
    }

    function schedulePaint() {
        clearTimeout(paintTimer);
        paintTimer = setTimeout(paintSlots, 150);
    }

    function paintSlots() {
        // Limpiar overlays anteriores
        document
            .querySelectorAll("#calendar .slot-overlay")
            .forEach((o) => o.remove());

        // Columnas de días — cada una tiene data-date
        const columnas = document.querySelectorAll(
            "#calendar .fc-timegrid-col[data-date]",
        );
        if (!columnas.length) return;

        // Filas de horas — cada tr tiene un td con data-time
        const filas = document.querySelectorAll(
            "#calendar .fc-timegrid-slots tr",
        );
        if (filas.length === 0) return;

        // Altura de cada slot en píxeles
        const primeraFila = filas[0];

        columnas.forEach((col) => {
            const fechaAttr = col.getAttribute("data-date");
            if (!fechaAttr) return;
            col.style.position = "relative";
            col.style.overflow = "hidden";
            const dow = new Date(fechaAttr + "T12:00:00").getDay();

            const tramos = horariosPorDia[dow];
            if (!tramos?.length) {
                col.appendChild(crearOverlay("0%", "100%"));
                return;
            }

            const { globalMin, globalMax } = getGlobalRange();
            const minGlobalMin = timeToMinutes(globalMin.substring(0, 5));
            const minGlobalMax = timeToMinutes(globalMax.substring(0, 5));
            const total = minGlobalMax - minGlobalMin;

            const tramosOrdenados = [...tramos].sort(
                (a, b) => timeToMinutes(a.inicio) - timeToMinutes(b.inicio),
            );

            // Precalcular qué minutos están ocupados y si son amarillos o rojos
            const fechaStr = fechaAttr;
            const ocupados = citasOcupadas[fechaStr] ?? [];
            const parciales = disponiblesPorFecha[fechaStr] ?? [];

            let cursor = minGlobalMin;
            tramosOrdenados.forEach((t) => {
                const ini = timeToMinutes(t.inicio);
                const fin = timeToMinutes(t.fin);

                if (ini > cursor) {
                    const top = ((cursor - minGlobalMin) / total) * 100;
                    const h = ((ini - cursor) / total) * 100;
                    col.appendChild(crearOverlay(`${top}%`, `${h}%`));
                }

                // Pintar el tramo verde por segmentos, saltando los ocupados
                let segCursor = ini;
                const ocupadosEnTramo = ocupados
                    .filter(
                        (c) =>
                            timeToMinutes(c.inicio) >= ini &&
                            timeToMinutes(c.fin) <= fin,
                    )
                    .sort(
                        (a, b) =>
                            timeToMinutes(a.inicio) - timeToMinutes(b.inicio),
                    );

                ocupadosEnTramo.forEach((c) => {
                    const minIni = timeToMinutes(c.inicio);
                    const minFin = timeToMinutes(c.fin);

                    // Verde antes del ocupado
                    if (minIni > segCursor) {
                        const top = ((segCursor - minGlobalMin) / total) * 100;
                        const h = ((minIni - segCursor) / total) * 100;
                        col.appendChild(
                            crearOverlayDisponible(`${top}%`, `${h}%`),
                        );
                    }

                    // Amarillo o rojo según si hay disponibles
                    const cubierto = parciales.some(
                        (s) =>
                            s.disponibles > 0 &&
                            timeToMinutes(s.inicio) <= minIni &&
                            timeToMinutes(s.inicio) + totalDuration >= minFin,
                    );
                    const top = ((minIni - minGlobalMin) / total) * 100;
                    const h = ((minFin - minIni) / total) * 100;
                    col.appendChild(
                        cubierto
                            ? crearOverlayOcupado(`${top}%`, `${h}%`)
                            : crearOverlay(`${top}%`, `${h}%`),
                    );

                    segCursor = Math.max(segCursor, minFin);
                });

                // Verde restante después del último ocupado
                if (segCursor < fin) {
                    const top = ((segCursor - minGlobalMin) / total) * 100;
                    const h = ((fin - segCursor) / total) * 100;
                    col.appendChild(crearOverlayDisponible(`${top}%`, `${h}%`));
                }

                cursor = Math.max(cursor, fin);
            });

            if (cursor < minGlobalMax) {
                const top = ((cursor - minGlobalMin) / total) * 100;
                const h = ((minGlobalMax - cursor) / total) * 100;
                col.appendChild(crearOverlay(`${top}%`, `${h}%`));
            }
            if (cursor < minGlobalMax) {
                const top = ((cursor - minGlobalMin) / total) * 100;
                const h = ((minGlobalMax - cursor) / total) * 100;
                col.appendChild(crearOverlay(`${top}%`, `${h}%`));
            }

            // ← AGREGAR ESTO
            parciales.forEach((slot) => {
                const slotIni = timeToMinutes(slot.inicio);
                const slotFin = timeToMinutes(slot.fin);

                const enTramo = tramosOrdenados.some(
                    (t) =>
                        slotIni >= timeToMinutes(t.inicio) &&
                        slotFin <= timeToMinutes(t.fin),
                );
                if (!enTramo) return;
                if (slot.disponibles === 0) return;

                const topPct = ((slotIni - minGlobalMin) / total) * 100;
                const label = document.createElement("div");
                label.className = "slot-overlay";
                label.style.cssText = `
                    position:absolute;
                    left:0;right:0;
                    top:${topPct}%;
                    height:20px;
                    margin-top:8px;
                    pointer-events:none;
                    z-index:3;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                `;
                label.innerHTML = `<span style="
                    font-size:10px;
                    font-weight:600;
                    color:rgba(21,128,61,0.8);
                    background:rgba(34,197,94,0.15);
                    padding:2px 8px;
                    border-radius:99px;
                    border: 1px solid rgba(34,197,94,0.3);
                ">${slot.disponibles} disponible${slot.disponibles > 1 ? "s" : ""}</span>`;

                if (slotSeleccionado) {
                    const fechaSel = formatDate(slotSeleccionado);
                    const iniSel =
                        slotSeleccionado.getHours() * 60 +
                        slotSeleccionado.getMinutes();
                    const finSel = iniSel + totalDuration;
                    const esFechaAfectada = fechaAttr === fechaSel;
                    const slotAfectado = slotIni < finSel && slotFin > iniSel;
                    if (esFechaAfectada && slotAfectado) return;
                }

                col.appendChild(label);
            });
        });
    }

    function crearOverlay(top, height) {
        const overlay = document.createElement("div");
        overlay.className = "slot-overlay";
        overlay.style.cssText = `
        position:absolute;
        left:0;right:0;
        top:${top};
        height:${height};
        pointer-events:none;
        z-index:2;
        background-color:rgba(239,68,68,0.10);
        background-image:repeating-linear-gradient(
            45deg,transparent,transparent 5px,
            rgba(239,68,68,0.07) 5px,rgba(239,68,68,0.07) 10px);
    `;
        return overlay;
    }
    function crearOverlayOcupado(top, height) {
        const overlay = document.createElement("div");
        overlay.className = "slot-overlay";
        overlay.style.cssText = `
        position:absolute;
        left:0;right:0;
        top:${top};
        height:${height};
        pointer-events:none;
        z-index:2;
        background-color:rgba(234,179,8,0.10);
        background-image:repeating-linear-gradient(
            45deg,transparent,transparent 5px,
            rgba(234,179,8,0.07) 5px,rgba(234,179,8,0.07) 10px);
    `;
        return overlay;
    }
    function crearOverlayDisponible(top, height) {
        const overlay = document.createElement("div");
        overlay.className = "slot-overlay";
        overlay.style.cssText = `
        position:absolute;
        left:0;right:0;
        top:${top};
        height:${height};
        pointer-events:none;
        z-index:2;
        background-color:rgba(34,197,94,0.08);
        background-image:repeating-linear-gradient(
            45deg,transparent,transparent 5px,
            rgba(34,197,94,0.05) 5px,rgba(34,197,94,0.05) 10px);
    `;
        return overlay;
    }

    function slotPermitido(date) {
        if (date <= new Date()) return false;

        const tramos = horariosPorDia[date.getDay()];
        if (!tramos?.length) return false;

        const slotMin = date.getHours() * 60 + date.getMinutes();
        const dentroDeHorario = tramos.some(
            (t) =>
                slotMin >= timeToMinutes(t.inicio) &&
                slotMin + totalDuration <= timeToMinutes(t.fin),
        );
        if (!dentroDeHorario) return false;

        const fechaStr = formatDate(date);
        const slotKey = `${String(date.getHours()).padStart(2, "0")}:${String(date.getMinutes()).padStart(2, "0")}`;
        const disponibles = disponiblesPorFecha[fechaStr] ?? [];
        const tieneDisponible = disponibles.some(
            (s) => s.inicio === slotKey && s.disponibles > 0,
        );

        return tieneDisponible;
    }

    async function cargarHorarios() {
        try {
            const res = await fetch(
                `/booking/horarios-empresa?company_id=${companyId}`,
                {
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                },
            );
            const data = await res.json();
            data.horarios.forEach((h) => {
                const dow = DIAS_MAP[h.day_of_week];
                if (dow !== undefined) {
                    if (!horariosPorDia[dow]) horariosPorDia[dow] = [];
                    horariosPorDia[dow].push({
                        inicio: h.hora_inicio.substring(0, 5),
                        fin: h.hora_fin.substring(0, 5),
                    });
                }
            });
        } catch (e) {
            console.error("Error cargando horarios:", e);
        }
    }
    async function cargarCitasOcupadas(start, end) {
        const tempCitas = {}; // ← solo locales, sin tocar los globales
        const tempDisp = {};
        try {
            const res = await fetch(
                `/booking/citas-ocupadas?company_id=${companyId}&start=${start}&end=${end}&duration=${totalDuration}`,
                {
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                },
            );
            const data = await res.json();

            totalProfesionales = data.totalProfesionales;

            data.citas.forEach((c) => {
                if (!tempCitas[c.fecha]) tempCitas[c.fecha] = [];
                tempCitas[c.fecha].push({ inicio: c.inicio, fin: c.fin });
            });

            data.disponibles.forEach((c) => {
                if (!tempDisp[c.fecha]) tempDisp[c.fecha] = [];
                tempDisp[c.fecha].push({
                    inicio: c.inicio,
                    fin: c.fin,
                    disponibles: c.disponibles,
                });
            });

            // Solo asignar globalmente cuando todo está listo
            citasOcupadas = tempCitas;
            disponiblesPorFecha = tempDisp;
        } catch (e) {
            console.error("Error cargando citas ocupadas:", e);
        }
    }

    function getGlobalRange() {
        const todos = Object.values(horariosPorDia).flat();
        if (!todos.length)
            return { globalMin: "08:00:00", globalMax: "20:00:00" };
        return {
            globalMin: todos.map((h) => h.inicio).sort()[0] + ":00",
            globalMax:
                todos
                    .map((h) => h.fin)
                    .sort()
                    .reverse()[0] + ":00",
        };
    }

    function onSlotSelected(startDate) {
        const fecha = formatDate(startDate);
        const hora = formatTime(startDate);
        const finDate = new Date(startDate.getTime() + totalDuration * 60000);
        const horaFin = formatTime(finDate);

        document.getElementById("fecha").value = fecha;
        document.getElementById("hora").value = hora;
        document.getElementById("end_time").value =
            `${formatDate(finDate)} ${horaFin}:00`;

        const diasSemana = ["dom", "lun", "mar", "mié", "jue", "vie", "sáb"];
        const meses = [
            "ene",
            "feb",
            "mar",
            "abr",
            "may",
            "jun",
            "jul",
            "ago",
            "sep",
            "oct",
            "nov",
            "dic",
        ];
        const fechaLabel = `${diasSemana[startDate.getDay()]} ${startDate.getDate()} ${meses[startDate.getMonth()]}`;

        document.getElementById("slotFechaTexto").textContent = fechaLabel;
        document.getElementById("slotHoraTexto").textContent = hora;
        document.getElementById("slotFinTexto").textContent = horaFin;
        document
            .getElementById("slotSeleccionado")
            .classList.replace("hidden", "flex");
        document.getElementById("resumenFecha").textContent =
            fechaLabel.charAt(0).toUpperCase() + fechaLabel.slice(1);
        document.getElementById("resumenHora").textContent =
            `${hora} – ${horaFin}`;
        document.getElementById("resumenSlot").classList.remove("hidden");

        if (selectedEvent) selectedEvent.remove();
        selectedEvent = calendar.addEvent({
            title: `Tu cita (${totalDuration} min)`,
            start: startDate,
            end: finDate,
            classNames: ["cita-preview"],
            backgroundColor: "#6366f1",
            borderColor: "transparent",
        });
        actualizarBadgesSeleccion(startDate);
        buscarProfesionales(fecha, hora);
    }

    async function buscarProfesionales(fecha, hora) {
        [
            "profesionalPlaceholder",
            "sinDisponibilidad",
            "profesionalesGrid",
        ].forEach((id) => document.getElementById(id).classList.add("hidden"));

        const loading = document.getElementById("profesionalLoading");
        loading.classList.remove("hidden");
        loading.classList.add("flex");
        document.getElementById("user_id").value = "";
        updateConfirmButton();

        try {
            const res = await fetch(
                `/booking/profesionales-disponibles?company_id=${companyId}&fecha=${fecha}&hora=${hora}&duration=${totalDuration}`,
                {
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                },
            );
            const data = await res.json();
            loading.classList.add("hidden");
            loading.classList.remove("flex");

            if (!data.profesionales?.length) {
                document
                    .getElementById("sinDisponibilidad")
                    .classList.remove("hidden");
                document
                    .getElementById("sinDisponibilidad")
                    .classList.add("flex");
                return;
            }
            renderProfesionales(data.profesionales);
        } catch (e) {
            loading.classList.add("hidden");
            document
                .getElementById("sinDisponibilidad")
                .classList.remove("hidden");
            document.getElementById("sinDisponibilidad").classList.add("flex");
        }
    }

    function renderProfesionales(profesionales) {
        const grid = document.getElementById("profesionalesGrid");
        grid.innerHTML = "";

        profesionales.forEach((prof) => {
            const card = document.createElement("label");
            card.className =
                "profesional-card flex items-center gap-3 p-3 rounded-xl border-2 border-outline-variant/20 cursor-pointer transition-all hover:border-primary/40";
            card.innerHTML = `
            <input type="radio" name="_profesional_radio" value="${prof.id}" class="sr-only profesional-radio" />
            <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0 bg-primary/10 flex items-center justify-center border border-outline-variant/20">
                ${
                    prof.image
                        ? `<img src="${prof.image}" alt="${prof.name}" class="w-full h-full object-cover" />`
                        : `<span class="text-sm font-bold text-primary/50">${prof.name.substring(0, 2).toUpperCase()}</span>`
                }
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-on-surface line-clamp-1">${prof.name}</p>
                <p class="text-xs text-on-surface-variant">${prof.phone || ""}</p>
            </div>`;
            grid.appendChild(card);
        });

        grid.classList.remove("hidden");
        grid.classList.add("grid");

        grid.querySelectorAll(".profesional-radio").forEach((radio) => {
            radio.addEventListener("change", () => {
                grid.querySelectorAll(".profesional-card").forEach((c) => {
                    c.classList.remove("border-primary", "bg-primary/5");
                    c.classList.add("border-outline-variant/20");
                });
                radio
                    .closest(".profesional-card")
                    .classList.add("border-primary", "bg-primary/5");
                radio
                    .closest(".profesional-card")
                    .classList.remove("border-outline-variant/20");
                document.getElementById("user_id").value = radio.value;
                updateConfirmButton();
            });
        });
    }

    function updateConfirmButton() {
        const ok =
            document.getElementById("fecha").value &&
            document.getElementById("hora").value &&
            document.getElementById("user_id").value;
        const btn = document.getElementById("btnConfirmar");
        btn.disabled = !ok;
        btn.className = ok
            ? "w-full py-3 rounded-lg text-sm font-semibold text-white transition shadow-sm bg-primary hover:bg-primary/90 cursor-pointer shadow-md"
            : "w-full py-3 rounded-lg text-sm font-semibold text-white transition shadow-sm bg-primary/40 cursor-not-allowed";
    }

    function formatDate(d) {
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(d.getDate()).padStart(2, "0")}`;
    }
    function formatTime(d) {
        return `${String(d.getHours()).padStart(2, "0")}:${String(d.getMinutes()).padStart(2, "0")}`;
    }
    function timeToMinutes(t) {
        const [h, m] = t.split(":").map(Number);
        return h * 60 + m;
    }
    function actualizarBadgesSeleccion(startDate) {
        slotSeleccionado = startDate;
        paintSlots();
    }
    document.addEventListener("DOMContentLoaded", init);
}
