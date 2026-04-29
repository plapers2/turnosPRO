{{-- resources/views/livewire/appointments/partials/toast.blade.php --}}
<div x-data="{ show: false, type: 'success', message: '' }"
    @notify.window="
        type    = $event.detail.type;
        message = $event.detail.message;
        show    = true;
        setTimeout(() => show = false, 3500);
    "
    x-show="show" x-transition:enter="toast-enter" x-transition:leave="toast-leave" class="toast"
    :class="'toast--' + type" style="display:none">
    <span x-text="message"></span>
</div>
