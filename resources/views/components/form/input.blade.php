@props(['id', 'type' => 'text', 'placeholder' => ''])

<input
    id="{{ $id }}"
    type="{{ $type }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' => 'w-full bg-surface-container-lowest border border-outline-variant/30 rounded-lg px-4 py-3.5 text-sm focus:outline-none focus:border-primary/30 focus:ring-4 focus:ring-primary/5 shadow-sm'
    ]) }}
/>
