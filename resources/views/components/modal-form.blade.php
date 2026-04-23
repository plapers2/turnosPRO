<dialog id="{{ $id }}" class="modal">
    <div class="modal-box">

        <!-- Botón cerrar arriba -->
        <form method="dialog">
            <button class="absolute right-4 top-4">✕</button>
        </form>

        <h3 class="text-2xl font-bold mb-4">
            {{ $title }}
        </h3>

        {{ $slot }}

        <div class="modal-action">
            {{ $actions ?? '' }}
            <form method="dialog">
                <button class="absolute right-4 top-4">✕</button>
            </form>
        </div>

    </div>
</dialog>
