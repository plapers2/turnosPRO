@props([
    'for' => null,
])

<label for="{{ $for }}"
    {{ $attributes->merge([
        'class' => 'block font-label text-sm font-semibold text-on-surface mb-2',
    ]) }}>
    {{ $slot }}
</label>
