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

        // Si ya está logueado, redirigir a accept directamente
        if (auth()->check() && $token) {
            return redirect()->route('invitations.accept', $token);
        }

        $invitation = null;
        if ($token) {
            $invitation = CompanyInvitation::where('token', $token)->first();
            abort_if(!$invitation || !$invitation->isUsable(), 410, 'Este enlace no es válido o ha expirado.');
        }

        if ($token) {
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

        // 1. Validar invitación PRIMERO, antes de crear nada
        $invitation = null;
        if ($request->invitation_token) {
            $invitation = CompanyInvitation::where('token', $request->invitation_token)
                ->whereNull('deleted_at')
                ->where('expires_at', '>', now())
                ->first();

            if (!$invitation || !$invitation->isUsable()) {
                abort(403, 'La invitación no es válida o ha expirado.');
            }
        }
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser && $invitation) {
            $existingUser->companies()->syncWithoutDetaching([$invitation->company_id]);
            $invitation->update(['status' => 'registered']);
            Auth::login($existingUser);
            return redirect(route('dashboard', absolute: false));
        }
        // 2. Validar campos del formulario
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

        // 3. Validar que el correo coincida con el de la invitación
        if ($invitation && $invitation->email && $request->email !== $invitation->email) {
            throw ValidationException::withMessages([
                'email' => 'El correo no coincide con el de la invitación.',
            ]);
        }

        // 4. Crear usuario
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
        ]);

        $user->assignRole('cliente');

        // 5. Vincular empresa y marcar invitación como usada
        if ($invitation) {
            $user->companies()->attach($invitation->company_id);
            $invitation->update(['status' => 'registered']);
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
