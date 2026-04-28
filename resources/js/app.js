import "./bootstrap";
import "./booking-calendar";
import Alpine from "alpinejs";
import Chart from "chart.js/auto";
import TomSelect from "tom-select";

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


// Inicializa Tom Select en todos los <select> con id="services"
// Usa document ready para compatibilidad con Livewire / navegación SPA
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('services');
    if (!el) return;

    new TomSelect(el, {
        plugins: ['remove_button', 'clear_button'],
        placeholder:  'Buscar servicio...',
        maxOptions:   null,       // sin límite — escala a cualquier cantidad
        closeAfterSelect: false,  // mantiene el dropdown abierto para seleccionar varios
        render: {
            // Cada tag/item seleccionado
            item: (data, escape) =>
                `<div class="ts-item">${escape(data.text)}</div>`,
            // Cada opción en el dropdown
            option: (data, escape) =>
                `<div class="ts-option">${escape(data.text)}</div>`,
            // Mensaje cuando no hay resultados
            no_results: () =>
                `<div class="ts-no-results">Sin resultados</div>`,
        },
    });
});

