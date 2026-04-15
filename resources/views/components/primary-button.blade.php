<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full bg-gradient-to-br from-primary to-primary-container text-on-primary font-bold py-4 rounded-lg shadow-sm hover:opacity-95 active:scale-[0.98] transition-all flex items-center justify-center gap-2']) }}>
    {{ $slot }}
</button>
