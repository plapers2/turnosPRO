<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Fortify\TwoFactorAuthenticatable;

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
        $request->authenticate(); // login normal de Breeze

        $user = $request->user() ?? Auth::user();

        // Si el usuario tiene 2FA activo, redirigir al desafío
        if ($user && in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
            if ($user->two_factor_confirmed_at && !session('auth.two_factor_confirmed')) {
                Auth::logout(); // desloguear temporalmente
                session(['login.id' => $user->getKey()]);
                return redirect()->route('two-factor.login');
            }
        }

        $request->session()->regenerate();

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
