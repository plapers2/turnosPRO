<?php

namespace App\Http\Controllers;

use App\Models\OpeningHour;
use App\Models\Service;
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
use App\Rules\SinSolapamientoEnSlots;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $companyId = session('active_company_id');

        $user = new User();
        $roles = Role::all();
        $services = Service::where("company_id", $companyId)->get();
        $horariosEmpresa = OpeningHour::where("company_id", $companyId)->where('deleted_at', true)
            ->get()
            ->keyBy('day_of_week');

        return view('users.create', compact('user', 'roles', 'horariosEmpresa', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación base del usuario
        // ! FALTA CORREGIR LA VALIDACION DEL EMAIL CON ESTADO
        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|string|max:255|unique:users,email',
            'telefono' => 'required|string|min:8|max:20',
            'password' => ['required', Password::min(8), 'confirmed'],
            'archivo'  => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'role'     => 'required|exists:roles,name',
            'services'     => 'required|array',
            'services.*'   => 'exists:services,id',
        ]);

        // Disponibilidad
        $slots = $request->input('disponibilidad', []);
        $companyId = session('active_company_id');

        abort_if(!$companyId, 403, 'No hay empresa activa en sesión.');

        $slotRules = [];
        // Verificar que todos los campos cumplan con lo requerido
        foreach ($slots as $i => $slot) {
            $dia = $slot['dia_semana'] ?? '';

            $slotRules["disponibilidad.{$i}.dia_semana"] = [
                'required',
                'string',
                'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            ];
            $slotRules["disponibilidad.{$i}.hora_inicio"] = [
                'required',
                'date_format:H:i',
                new DentroHorarioEmpresa($dia, $companyId, validarDia: true),
                new SinSolapamientoEnSlots($dia, $i, $slots),
            ];
            $slotRules["disponibilidad.{$i}.hora_fin"] = [
                'required',
                'date_format:H:i',
                "after:disponibilidad.{$i}.hora_inicio",
                new DentroHorarioEmpresa($dia, $companyId, validarDia: false),
            ];
        }

        if ($slotRules) {
            $request->validate($slotRules);
        }

        // Imagen
        $imagePath = $request->file('archivo')->store('users', 'public');

        $user = User::create([
            'name'     => $data['nombre'],
            'email'    => $data['email'],
            'phone'    => $data['telefono'],
            'password' => Hash::make($data['password']),
            'image'    => $imagePath,
        ]);

        // Asignar el rol del usuario
        $user->assignRole($data['role']);

        // Asignar la empresa al usuario
        $user->companies()->attach($companyId);

        // Asingar los servicios al usuario
        $user->services()->sync($data['services'] ?? []);


        foreach ($slots as $slot) {
            $user->professionalAvailabilities()->create([
                'day_of_week' => $slot['dia_semana'],
                'start_time'  => $slot['hora_inicio'],
                'end_time'    => $slot['hora_fin'],
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
        $companyId = session('active_company_id');
        $user = User::find($id);
        $roles = Role::all();
        $services = Service::where("company_id", $companyId)->get();
        $horariosEmpresa = OpeningHour::where("company_id", $companyId)->where('deleted_at', true)
            ->get()
            ->keyBy('day_of_week');

        return view('users.edit', compact('user', 'roles', 'horariosEmpresa', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id, 'id'),
            ],
            'telefono' => 'required|string|min:8|max:20',
            'password' => ['nullable', Password::min(8), 'confirmed'],
            'archivo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'role'     => 'required|exists:roles,name',
            'services'     => 'required|array',
            'services.*'   => 'exists:services,id',
        ]);

        $slots     = $request->input('disponibilidad', []);
        $companyId = session('active_company_id');

        abort_if(!$companyId, 403, 'No hay empresa activa en sesión.');

        $slotRules = [];
        foreach ($slots as $i => $slot) {
            $dia = $slot['dia_semana'] ?? '';

            $slotRules["disponibilidad.{$i}.dia_semana"] = [
                'required',
                'string',
                'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            ];
            $slotRules["disponibilidad.{$i}.hora_inicio"] = [
                'required',
                'date_format:H:i',
                new DentroHorarioEmpresa($dia, $companyId, validarDia: true),
                new SinSolapamientoEnSlots($dia, $i, $slots),  // ← mismo que en store
            ];
            $slotRules["disponibilidad.{$i}.hora_fin"] = [
                'required',
                'date_format:H:i',
                "after:disponibilidad.{$i}.hora_inicio",
                new DentroHorarioEmpresa($dia, $companyId, validarDia: false),
            ];
        }

        if ($slotRules) {
            $request->validate($slotRules);
        }

        // Manejo de imagen
        if ($request->hasFile('archivo')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $data['archivo'] = $request->file('archivo')->store('users', 'public');
        }

        $user->update([
            'name'  => $data['nombre'],
            'email' => $data['email'],
            'phone' => $data['telefono'],
            'image' => $data['archivo'] ?? $user->image,
        ]);

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $user->save();
        }

        // Sincronizar roles — sync() añade los nuevos y elimina los que se quitaron
        $user->syncRoles([$data['role']]);

        // Sincronizar servicios — sync() añade los nuevos y elimina los que se quitaron
        $user->services()->sync($data['services'] ?? []);


        $user->professionalAvailabilities()->forceDelete();
        foreach ($slots as $slot) {
            $user->professionalAvailabilities()->create([
                'day_of_week' => $slot['dia_semana'],
                'start_time'  => $slot['hora_inicio'],
                'end_time'    => $slot['hora_fin'],
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $user)
    {

        // Solo hace soft delete (NO borra imagen)
        $user->professionalAvailabilities()->delete();
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
        $user->professionalAvailabilities()->onlyTrashed()->restore();

        return redirect()->route('users.index')
            ->with('success', 'Usuario restaurado correctamente');
    }
}
