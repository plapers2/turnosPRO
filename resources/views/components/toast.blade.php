<div
    x-data="{ show: false, message: '', type: 'success' }"
    x-show="show"
    x-transition
    x-init="
        window.addEventListener('notify', e => {
            message = e.detail.message;
            type = e.detail.type;
            show = true;

            setTimeout(() => show = false, 4000);
        });
    "
    class="fixed top-6 right-6 z-50"
>
    <div
        :class="{
            'bg-green-500 text-white': type === 'success',
            'bg-red-500 text-white': type === 'error'
        }"
        class="px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 text-sm"
    >
        <span class="material-symbols-outlined text-[18px]"
            x-text="type === 'success' ? 'check_circle' : 'error'"></span>

        <span x-text="message"></span>
    </div>
</div>
