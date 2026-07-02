<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        $email = $request->query('email');
        $token = $request->route('token');

        // Verificamos el token contra la tabla ANTES de mostrar el formulario,
        // para no dejar que el usuario llene la contraseña con un enlace muerto.
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        $expirado = !$record
            || !Hash::check($token, $record->token)
            || now()->diffInSeconds($record->created_at) > config('auth.passwords.users.expire', 60) * 60;

        if ($expirado) {
            return view('auth.reset-link-invalid', [
                'message' => 'Este enlace para restablecer tu contraseña ha expirado o ya fue usado. Solicita uno nuevo.',
            ]);
        }

        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse|View
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        // Token inválido o expirado (pudo vencer justo entre el GET y el POST):
        // vista dedicada en vez de error de validación de email.
        if (in_array($status, [Password::INVALID_TOKEN, Password::INVALID_USER])) {
            return view('auth.reset-link-invalid', [
                'message' => 'Este enlace ha expirado o ya fue usado. Solicita uno nuevo.',
            ]);
        }

        // Otros casos (throttle, etc.) sí van por el flujo normal de validación.
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
