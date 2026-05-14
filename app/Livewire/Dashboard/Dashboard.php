<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        if ($user->hasRole('cliente')) {
            return view('livewire.dashboard.cliente-dashboard')
                ->layout('layouts.app');
        }

        if ($user->hasRole('empleado')) {
            return view('livewire.dashboard.empleado-dashboard')
                ->layout('layouts.app');
        }

        // admin (y cualquier otro rol privilegiado)
        return view('livewire.dashboard.admin-dashboard')
            ->layout('layouts.app');
    }
}
