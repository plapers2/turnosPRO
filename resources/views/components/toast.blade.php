<div x-data="{ show: false, message: '', type: 'success' }" x-show="show" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2" x-init="window.addEventListener('notify', e => {
        message = e.detail.message;
        type = e.detail.type;
        show = true;
        setTimeout(() => show = false, 4000);
    });"
    :class="{
        'toast--success': type === 'success',
        'toast--warning': type === 'warning',
        'toast--error': type === 'error'
    }"
    class="toast">
    <span class="material-symbols-outlined" style="font-size:18px; vertical-align:middle; margin-right:6px;"
        x-text="type === 'success' ? 'check_circle' : type === 'warning' ? 'warning' : 'error'">
    </span>
    <span x-text="message"></span>
</div>
