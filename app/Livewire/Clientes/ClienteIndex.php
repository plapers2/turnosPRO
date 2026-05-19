<?php

namespace App\Livewire\Clientes;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ClienteIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $subscription_tier = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingSubscriptionTier()
    {
        $this->resetPage();
    }

    public function togglePlan(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update([
            'subscription_tier' => $user->subscription_tier === 'premium' ? 'standard' : 'premium',
        ]);

        $this->dispatch('subscription_tier-updated');
    }

    public function render()
    {
        $clientes = User::role('cliente')
            ->withTrashed()
            ->when(
                $this->search,
                fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->when($this->subscription_tier, fn($q) => $q->where('subscription_tier', $this->subscription_tier))
            ->latest()
            ->paginate(15);

        return view('livewire.clientes.cliente-index', compact('clientes'));
    }
}
