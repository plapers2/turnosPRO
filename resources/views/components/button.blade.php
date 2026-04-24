@props([
    'variant' => 'primary',
    'type' => 'submit',
])

@php
    $base = 'px-6 py-2.5 rounded-lg font-semibold text-sm flex items-center justify-center gap-2 transition';

    $variants = [
        'primary' => 'bg-primary text-white hover:bg-primary/90',
        'error' => 'bg-error text-white hover:bg-error/90',
        'secondary' => 'bg-primary-container text-white hover:bg-primary/80',
    ];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $base . ' ' . $variants[$variant]]) }}>
    {{ $slot }}
</button>
