<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    public function show(): View
    {
        return view('auth.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = $request->user();
        $user->update([
            'password'            => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('company.select')
            ->with('success', '¡Contraseña actualizada! Ya puedes usar el sistema.');
    }
}
