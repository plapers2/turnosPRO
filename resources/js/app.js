import "./bootstrap";
import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import Alpine from "alpinejs";
import Chart from "chart.js/auto";

window.Alpine = Alpine;
Alpine.start();
document.addEventListener("DOMContentLoaded", function () {
    let calendarEl = document.getElementById("calendar");

    if (calendarEl) {
        let calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, interactionPlugin],
            initialView: "dayGridMonth",
        });

        calendar.render();
    }
});
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("myChart");

    if (ctx) {
        new Chart(ctx, {
            type: "bar", // tipo de gráfico
            data: {
                labels: ["Enero", "Febrero", "Marzo"],
                datasets: [
                    {
                        label: "Ventas",
                        data: [10, 20, 15],
                        borderWidth: 1,
                    },
                ],
            },
        });
    }
});
