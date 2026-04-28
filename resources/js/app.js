import "./bootstrap";
import "./booking-calendar";
import Alpine from "alpinejs";
import Chart from "chart.js/auto";

window.Alpine = Alpine;

Alpine.start();

document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("myChart");

    if (ctx) {
        new Chart(ctx, {
            type: "bar",
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

    if (!menuBtn || !sidebar || !overlay) return;

    const openSidebar = () => {
        sidebar.classList.remove("-translate-x-full");
        overlay.classList.remove("hidden");
    };

    const closeSidebar = () => {
        sidebar.classList.add("-translate-x-full");
        overlay.classList.add("hidden");
    };

    menuBtn.addEventListener("click", () => {
        sidebar.classList.contains("-translate-x-full")
            ? openSidebar()
            : closeSidebar();
    });

    overlay.addEventListener("click", closeSidebar);
});
