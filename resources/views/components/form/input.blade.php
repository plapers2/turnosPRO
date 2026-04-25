@props(['name', 'type', 'value', 'placeholder' => ''])

@php
    $hasError = $errors->has($name);
@endphp

<input name="{{ $name }}" type="{{ $type }}" placeholder="{{ $placeholder }}" value="{{ $value }}"
    {{ $attributes->merge([
        'class' =>
            '
                    form-input w-full rounded-lg border px-4 py-2.5 text-sm transition
                    ' .
            ($hasError
                ? 'border-red-500 focus:border-red-500 focus:ring-red-100'
                : 'border-outline-variant/30 focus:border-primary/40 focus:ring-primary/10'),
    ]) }} />

@error($name)
    <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
        <span class="material-symbols-outlined text-[14px]">error</span>
        {{ $message }}
    </p>
@enderror
