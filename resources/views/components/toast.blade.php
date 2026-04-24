<div x-data="toastSystem()" x-on:notify.window="add($event.detail)"
    class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 w-96">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show" x-transition:enter="transform ease-out duration-200"
            x-transition:enter-start="translate-x-6 opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="rounded-lg shadow-md border px-4 py-3 flex items-start justify-between gap-4 bg-white"
            :class="toast.border">
            <!-- Contenido -->
            <div class="flex flex-col text-sm">
                <span class="font-semibold" x-text="toast.title"></span>
                <span class="text-gray-600" x-text="toast.message"></span>
            </div>

            <!-- Cerrar -->
            <button @click="remove(toast.id)" class="text-gray-400 hover:text-gray-600">
                ✕
            </button>
        </div>
    </template>
</div>

<script>
    function toastSystem() {
        return {
            toasts: [],

            add({
                type = 'success',
                message = ''
            }) {
                const id = Date.now()

                const types = {
                    success: {
                        title: 'Operación exitosa',
                        border: 'border-l-4 border-green-500'
                    },
                    error: {
                        title: 'Error',
                        border: 'border-l-4 border-red-500'
                    },
                    warning: {
                        title: 'Advertencia',
                        border: 'border-l-4 border-yellow-500'
                    },
                    info: {
                        title: 'Información',
                        border: 'border-l-4 border-blue-500'
                    }
                }

                const toast = {
                    id,
                    show: true,
                    message,
                    title: types[type].title,
                    border: types[type].border
                }

                this.toasts.push(toast)

                setTimeout(() => {
                    this.remove(id)
                }, 4000)
            },

            remove(id) {
                const toast = this.toasts.find(t => t.id === id)
                if (!toast) return

                toast.show = false

                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id)
                }, 200)
            }
        }
    }
</script>
