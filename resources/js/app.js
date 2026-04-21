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

document.addEventListener("DOMContentLoaded", () => {
    const menuBtn = document.getElementById("menuBtn");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");

    const openSidebar = () => {
        sidebar.classList.remove("-translate-x-full");
        overlay.classList.remove("hidden");
    };

    const closeSidebar = () => {
        sidebar.classList.add("-translate-x-full");
        overlay.classList.add("hidden");
    };

    const toggleSidebar = () => {
        sidebar.classList.contains("-translate-x-full")
            ? openSidebar()
            : closeSidebar();
    };

    menuBtn.addEventListener("click", toggleSidebar);
    overlay.addEventListener("click", closeSidebar);
});
