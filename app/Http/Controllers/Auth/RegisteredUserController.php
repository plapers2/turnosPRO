<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CompanyInvitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * Acepta un token opcional en la URL (/register/{token})
     */
    public function create(Request $request): View|RedirectResponse
    {
        $token = $request->route('token');

        // Si ya está autenticado y trae token, ir directo a vincular empresa
        if (auth()->check() && $token) {
            return redirect()->route('invitations.accept', $token);
        }

        $invitation = null;

        if ($token) {
            $invitation = CompanyInvitation::where('token', $token)->first();
            abort_if(!$invitation || !$invitation->isUsable(), 410, 'Este enlace no es válido o ha expirado.');

            // Guardar en sesión por si el usuario prefiere loguearse en vez de registrarse
            session(['invitation_token' => $token]);
        }

        return view('auth.register', compact('invitation', 'token'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $customAttributes = [
            'password' => 'contraseña',
        ];

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()],
            'phone'    => ['required', 'string', 'max:20'],
        ], [], $customAttributes);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
        ]);

        $user->assignRole('cliente');

        // Vincular empresa si viene de invitación
        $token = $request->input('invitation_token') ?? session()->pull('invitation_token');

        if ($token) {
            $invitation = CompanyInvitation::where('token', $token)
                ->whereNull('deleted_at')
                ->first();

            if ($invitation && $invitation->isUsable()) {
                $user->companies()->attach($invitation->company_id);
                $invitation->update(['status' => 'registered']);
            }
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
