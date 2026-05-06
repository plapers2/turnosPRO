<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileSettingsController extends Controller
{
    public function edit()
    {
        $cliente = auth()->user();
        session(['profile_return_url' => url()->previous()]);
        return view('profile.settings', compact('cliente'));
    }

    public function update(Request $request)
    {
        $cliente = auth()->user();

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];

        if ($request->filled('new_password')) {
            $rules['current_password']      = ['required'];
            $rules['new_password']          = ['required', Password::min(8), 'confirmed'];
        }

        $request->validate($rules);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $cliente->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
            }
            $cliente->password = Hash::make($request->new_password);
        }

        $cliente->name  = $request->name;
        $cliente->phone = $request->phone;
        $cliente->save();

        return redirect(session('profile_return_url', route('dashboard')))->with('success', 'Perfil actualizado correctamente.');
    }
}
