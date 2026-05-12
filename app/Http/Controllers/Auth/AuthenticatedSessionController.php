<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Si tiene contraseña temporal, redirige a cambiarla antes de todo
        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        // Master va directo a su panel
        if ($user->hasRole('master')) {
            return redirect()->route('master.index');
        }

        // Clientes van al dashboard
        if ($user->hasRole('cliente')) {
            return redirect()->route('dashboard');
        }

        // Admin y empleado seleccionan empresa
        return redirect()->route('company.select');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
