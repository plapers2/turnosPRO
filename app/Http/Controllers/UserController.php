<?php

namespace App\Http\Controllers;

use App\Models\OpeningHour;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use App\Rules\DentroHorarioEmpresa;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $users = User::withTrashed()
            ->where('id', '!=', auth()->id())
            ->orderByRaw('deleted_at IS NOT NULL')
            ->paginate();

        return view('users.index', compact('users'))
            ->with('i', ($request->input('page', 1) - 1) * $users->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $companyId = session('active_company_id');

        $user = new User();
        $roles = Role::all();
        $horariosEmpresa = OpeningHour::where("company_id", $companyId)->where('deleted_at', true)
            ->get()
            ->keyBy('day_of_week');

        return view('users.create', compact('user', 'roles', 'horariosEmpresa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación base del usuario
        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|string|max:255|unique:users,email',
            'telefono' => 'required|string|min:8|max:20',
            'password' => ['required', Password::min(8), 'confirmed'],
            'archivo'  => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'role'     => 'required|exists:roles,name',
        ]);

        // Validación dinámica de disponibilidad
        $slots = $request->input('disponibilidad', []);
        $companyId = session('active_company_id');

        // 1. Asegúrate que companyId no sea null
        abort_if(!$companyId, 403, 'No hay empresa activa en sesión.');

        // 2. Valida los slots con dia_semana primero
        $slotRules = [];
        foreach ($slots as $i => $slot) {
            $dia = $slot['dia_semana'] ?? '';

            $slotRules["disponibilidad.{$i}.dia_semana"] = [
                'required',
                'string',
                'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday', // ← valida el valor
            ];
            $slotRules["disponibilidad.{$i}.hora_inicio"] = [
                'required',
                'date_format:H:i',
                new DentroHorarioEmpresa($dia, $companyId),
            ];
            $slotRules["disponibilidad.{$i}.hora_fin"] = [
                'required',
                'date_format:H:i',
                "after:disponibilidad.{$i}.hora_inicio",
                new DentroHorarioEmpresa($dia, $companyId),
            ];
        }

        if ($slotRules) {
            $request->validate($slotRules);
        }

        // Imagen
        $imagePath = $request->file('archivo')->store('users', 'public');

        // Crear usuario
        $user = User::create([
            'name'     => $data['nombre'],
            'email'    => $data['email'],
            'phone'    => $data['telefono'],
            'password' => Hash::make($data['password']),
            'image'    => $imagePath,
        ]);

        $user->assignRole($data['role']);

        // Guardar disponibilidades (múltiples turnos por día)
        foreach ($slots as $slot) {
            $user->disponibilidades()->create([
                'day_of_week' => $slot['dia_semana'],
                'start_time'  => $slot['hora_inicio'],
                'end_time'    => $slot['hora_fin'],
                'user_id'     => $user->id, // ya lo toma del hasMany, pero explícito no daña
            ]);
        }

        return Redirect::route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $user = User::find($id);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $user = User::find($id);
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {

        $data = $request->validate([
            'nombre' => "required|string|max:255",
            'email' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id, 'id'),
            ],
            'telefono' => "required|string|min:8|max:20",
            'password' => ['nullable', Password::min(8), 'confirmed'],
            'archivo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'role' => 'required|exists:roles,name'
        ]);

        // Manejo de imagen
        if ($request->hasFile('archivo')) {

            // eliminar anterior
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            // guardar nueva
            $data['archivo'] = $request->file('archivo')->store('users', 'public');
        }



        $user->update([
            'name' => $data['nombre'],
            'email' => $data['email'],
            'phone' => $data['telefono'],
            'image' => $data['archivo'] ?? $user->image,
        ]);

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $user->save();
        }


        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $user)
    {
        // Solo hace soft delete (NO borra imagen)
        $user->delete();

        // Si es AJAX (fetch)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuario enviado a la papelera'
            ]);
        }

        // Si es formulario normal
        return redirect()->route('users.index')
            ->with('success', 'Usuario enviado a la papelera');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index')
            ->with('success', 'Usuario restaurado correctamente');
    }
}
