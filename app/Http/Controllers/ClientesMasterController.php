<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientesMasterController extends Controller
{
    public function index(): View
    {
        return view('master.clientes.index');
    }

    public function togglePlan(User $user): RedirectResponse
    {
        $user->update([
            'subscription_tier' => $user->subscription_tier === 'premium' ? 'standard' : 'premium'
        ]);

        return back()->with('success', 'Plan actualizado correctamente.');
    }
}
