{{-- resources/views/components/form/field.blade.php --}}
@props(['label', 'for'])

<div>
    <x-form.label :for="$for">
        {{ $label }}
    </x-form.label>

    {{ $slot }}
</div>
