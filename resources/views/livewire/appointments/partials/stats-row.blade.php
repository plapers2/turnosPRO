{{-- resources/views/livewire/appointments/partials/stats-row.blade.php --}}
{{-- Props: $stats = ['total', 'pending', 'confirmed', 'cancelled'] --}}
<div class="stats-row">
    <div class="stat-card stat-card--total">
        <span class="stat-card__label">Total</span>
        <span class="stat-card__value">{{ $stats['total'] }}</span>
    </div>
    <div class="stat-card stat-card--pending">
        <span class="stat-card__label">Pendientes</span>
        <span class="stat-card__value">{{ $stats['pending'] }}</span>
    </div>
    <div class="stat-card stat-card--confirmed">
        <span class="stat-card__label">Confirmadas</span>
        <span class="stat-card__value">{{ $stats['confirmed'] }}</span>
    </div>
    <div class="stat-card stat-card--cancelled">
        <span class="stat-card__label">Canceladas</span>
        <span class="stat-card__value">{{ $stats['cancelled'] }}</span>
    </div>
</div>
