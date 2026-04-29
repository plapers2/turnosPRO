@props(['disabled' => false])
<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'w-full bg-surface-container-highest border-none rounded-lg py-4 pl-12 pr-4
        focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest
        transition-all text-on-surface placeholder:text-outline
        caret-primary']) }}>
