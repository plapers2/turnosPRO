{{-- resources/views/livewire/appointments/partials/pagination.blade.php --}}
{{-- Props: $paginator (LengthAwarePaginator) --}}
@if ($paginator->hasPages())
<div class="border-t border-outline-variant/30 px-2 pt-4 pb-1">
    <div class="flex items-center justify-between gap-2 text-sm">

        {{-- Anterior --}}
        <button class="px-3 py-1.5 rounded-lg border border-outline-variant/30
            bg-surface-container text-on-surface text-xs font-medium
            disabled:opacity-40 disabled:cursor-not-allowed
            hover:bg-surface-container-high transition-colors"  wire:click="previousPage('page')" wire:loading.attr="disabled" @disabled($paginator->onFirstPage())
            ...>
            ← Anterior
        </button>

        {{-- Info páginas --}}
        <span class="text-xs text-on-surface-variant">
            Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}
            &nbsp;·&nbsp;
            {{ $paginator->total() }} registros
        </span>

        {{-- Siguiente --}}
        <button class="px-3 py-1.5 rounded-lg border border-outline-variant/30
            bg-surface-container text-on-surface text-xs font-medium
            disabled:opacity-40 disabled:cursor-not-allowed
            hover:bg-surface-container-high transition-colors" wire:click="nextPage('page')" wire:loading.attr="disabled" @disabled($paginator->onLastPage())
            ...>
            Siguiente →
        </button>

    </div>
</div>
@endif