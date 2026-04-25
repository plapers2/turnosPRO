@props(['name', 'value', 'texto', 'rows' => 4, 'placeholder' => ''])
@php
    $hasError = $errors->has($name);
@endphp

<textarea name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' =>
            '
                        w-full rounded-lg border px-4 py-2.5 text-sm transition
                        ' .
            ($hasError
                ? 'border-red-500 focus:border-red-500 focus:ring-red-100'
                : 'border-outline-variant/30 focus:border-primary/40 focus:ring-primary/10'),
    ]) }}>{{$texto}}</textarea>

@error($name)
    <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
        <span class="material-symbols-outlined text-[14px]">error</span>
        {{ $message }}
    </p>
@enderror
