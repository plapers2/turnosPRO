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
        $request->authenticate();

        $user = $request->user() ?? Auth::user();

        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        if ($user && in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
            if ($user->two_factor_confirmed_at && !session('auth.two_factor_confirmed')) {
                Auth::logout();
                session(['login.id' => $user->getKey()]);
                return redirect()->route('two-factor.login');
            }
        }

        $request->session()->regenerate();

        // ── Vincular empresa si viene de invitación ───────────────────────
        if (session('invitation_token')) {
            $token = session()->pull('invitation_token');
            $invitation = \App\Models\CompanyInvitation::where('token', $token)
                ->whereNull('deleted_at')
                ->first();

            if ($invitation && $invitation->isUsable()) {
                if (!$user->companies()->where('company_id', $invitation->company_id)->exists()) {
                    $user->companies()->attach($invitation->company_id);
                }
                $invitation->update(['status' => 'registered']);

                return redirect()->route('appointment.index')
                    ->with('success', 'Empresa vinculada a tu cuenta correctamente.');
            }
        }

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
