@props(['id'])

<div class="relative">
    <select
        id="{{ $id }}"
        {{ $attributes->merge([
            'class' => 'appearance-none w-full bg-surface-container-lowest border border-outline-variant/30 rounded-lg pl-4 pr-10 py-3.5 text-sm focus:outline-none focus:border-primary/30 focus:ring-4 focus:ring-primary/5 shadow-sm cursor-pointer'
        ]) }}
    >
        {{ $slot }}
    </select>

    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
        expand_more
    </span>
</div>
