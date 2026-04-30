{{-- resources/views/livewire/appointments/partials/pagination.blade.php --}}
{{-- Props: $paginator (LengthAwarePaginator) --}}
<div class="pagination-wrapper">
    {{ $paginator->links() }}
</div>
