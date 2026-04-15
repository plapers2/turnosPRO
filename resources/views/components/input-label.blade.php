@props(['value'])

<label
    {{ $attributes->merge(['class' => 'text-[0.75rem] font-semibold uppercase tracking-wider text-on-surface-variant px-1']) }}>
    {{ $value ?? $slot }}
</label>
