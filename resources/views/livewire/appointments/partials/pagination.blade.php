{{-- resources/views/livewire/appointments/partials/pagination.blade.php --}}
{{-- Props: $paginator (LengthAwarePaginator) --}}
@if ($paginator->hasPages())
    <div class="border-t border-outline-variant/30 px-2 pt-4 pb-1">
        {{ $paginator->links() }}
    </div>
@endif
