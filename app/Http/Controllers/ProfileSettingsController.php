<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileSettingsController extends Controller
{
    public function edit()
    {
        $cliente = auth()->user();
        session(['profile_return_url' => url()->previous()]);
        return view('profile.settings', compact('cliente'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $cliente = auth()->user();

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $cliente->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
            }
            $cliente->password = Hash::make($request->new_password);
        }

        if ($request->hasFile('image')) {
            if ($cliente->image) {
                Storage::disk('public')->delete($cliente->image);
            }
            $cliente->image = $request->file('image')->store('users', 'public');
        }

        $cliente->name  = $request->name;
        $cliente->phone = $request->phone;
        $cliente->save();

        return redirect(session('profile_return_url', route('dashboard')))
            ->with('success', 'Perfil actualizado correctamente.');
    }
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
