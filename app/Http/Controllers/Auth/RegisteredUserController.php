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
        $invitation = null;

        if ($token) {
            $invitation = CompanyInvitation::where('token', $token)->first();

            if (!$invitation || !$invitation->isUsable()) {
                return view('auth.invitation-invalid', [
                    'message' => 'Este enlace de invitación no es válido o ha expirado. Solicita una nueva invitación al administrador.',
                ]);
            }

            // Caso 1.1.3: Ya está logueado → asignar empresa y redirigir
            if (auth()->check()) {
                $user = auth()->user();
                $user->companies()->syncWithoutDetaching([$invitation->company_id]);
                $invitation->update(['status' => 'registered']);
                return redirect()->route('appointment.index')
                    ->with('success', '¡Empresa asignada correctamente!');
            }

            // Caso 1.1.2: Email ya registrado pero no logueado → redirigir a login
            if ($invitation->email && User::where('email', $invitation->email)->exists()) {
                session(['invitation_token' => $token]);
                return redirect()->route('login')
                    ->with('info', 'Ya tienes una cuenta. Inicia sesión para vincular tu nueva empresa.');
            }

            session(['invitation_token' => $token]);
        }

        return view('auth.register', compact('invitation', 'token'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $customAttributes = ['password' => 'contraseña'];

        $invitation = null;
        $token = $request->invitation_token ?? session('invitation_token');

        if ($token) {
            $invitation = CompanyInvitation::where('token', $token)
                ->whereNull('deleted_at')
                ->where('expires_at', '>', now())
                ->first();

            if (!$invitation || !$invitation->isUsable()) {
                return view('auth.invitation-invalid', [
                    'message' => 'La invitación no es válida o ha expirado. Solicita una nueva al administrador.',
                ]);
            }

            if ($invitation->email && $request->email !== $invitation->email) {
                throw ValidationException::withMessages([
                    'email' => 'El correo no coincide con el de la invitación.',
                ]);
            }
        }

        // Validar formulario
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::min(8)
                ->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
            'phone'    => ['required', 'string', 'min:8', 'max:20', 'regex:/^\+?[\d\s\-\(\)]+$/'],
        ], [
            'name.required'    => 'El nombre es obligatorio.',
            'email.required'   => 'El correo electrónico es obligatorio.',
            'email.email'      => 'El correo no tiene un formato válido.',
            'email.unique'     => 'Ya existe un usuario con ese correo.',
            'phone.required'   => 'El teléfono es obligatorio.',
            'phone.min'        => 'El teléfono debe tener al menos 8 dígitos.',
            'phone.max'        => 'El teléfono no puede tener más de 20 caracteres.',
            'phone.regex'      => 'El teléfono solo puede contener números, +, -, espacios y paréntesis.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Crear usuario
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
        ]);

        $user->assignRole('cliente');

        // Vincular empresa si viene de invitación
        if ($invitation) {
            $user->companies()->attach($invitation->company_id);
            $invitation->update(['status' => 'registered']);
            session()->forget('invitation_token');
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
