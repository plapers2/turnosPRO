import Swal from "sweetalert2";

const serviceCard = document.querySelector("#serviceCard");

serviceCard.addEventListener("click", (e) => {
    e.preventDefault();

    if (e.target.classList.contains("btnEliminar")) {
        const id = e.target.dataset.id;
        console.log(id);

        Swal.fire({
            title: "Eliminar servicio",
            text: "¿Estas seguro de realizar esta acción?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#2d1600",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, eliminar",
            cancelButtonText: "No, cancelar",
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/services/${id}`, {
                        method: "DELETE",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                    });

                    if (response.ok) {
                        Swal.fire(
                            "Eliminado",
                            "El servicio fue eliminado",
                            "success",
                        ).then(() => {
                            // Opcional: eliminar del DOM o recargar
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            "Error",
                            "No se pudo eliminar" + response,
                            "error",
                        );
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire("Error", "Ocurrió un problema", "error");
                }
            }
        });
    }
});
