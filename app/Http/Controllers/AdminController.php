<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\AdminRequest;
use App\Mail\AdminCredentialsMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('master.admins.index');
    }

    public function create(): View
    {
        return view('master.admins.create');
    }

    public function store(AdminRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $tempPassword = Str::random(10);

        $admin = User::create([
            'name'                 => $data['name'],
            'email'                => $data['email'],
            'phone'                => $data['phone'],
            'password'             => Hash::make($tempPassword),
            'must_change_password' => true,
            'image'                => null,
        ]);

        $admin->assignRole('admin');

        Mail::to($admin->email)->send(new AdminCredentialsMail($admin, null, $tempPassword));

        return redirect()->route('master.admins.index')
            ->with('success', 'Administrador creado correctamente. Se enviaron las credenciales por correo.');
    }

    public function destroy(User $admin): RedirectResponse
    {
        $admin->delete();

        return redirect()->route('master.admins.index')
            ->with('success', 'Administrador desactivado correctamente.');
    }

    public function restore(int $id): RedirectResponse
    {
        $admin = User::withTrashed()->findOrFail($id);
        $admin->restore();

        return redirect()->route('master.admins.index')
            ->with('success', 'Administrador reactivado correctamente.');
    }
}
