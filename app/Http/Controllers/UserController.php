<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
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
use App\Rules\HoraFinPosteriorAInicio;
use App\Rules\SinSolapamientoEnSlots;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
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

        $horariosEmpresa = OpeningHour::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->selectRaw('day_of_week, MIN(start_time) as start_time, MAX(end_time) as end_time')
            ->groupBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        return view('users.create', compact('user', 'roles', 'horariosEmpresa', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        // Validación base del usuario
        $data = $request->validated();

        // Disponibilidad
        $slots = $request->input('disponibilidad', []);
        $companyId = session('active_company_id');

        abort_if(!$companyId, 403, 'No hay empresa activa en sesión.');

        $slotRules = [];

        // Arreglo con nombres
        $diasNombres = [
            'Monday'    => 'lunes',
            'Tuesday'   => 'martes',
            'Wednesday' => 'miércoles',
            'Thursday'  => 'jueves',
            'Friday'    => 'viernes',
            'Saturday'  => 'sábado',
            'Sunday'    => 'domingo',
        ];
        // Verificar que todos los campos cumplan con lo requerido
        foreach ($slots as $i => $slot) {
            $dia = $slot['day_of_week'] ?? '';

            $slotRules["disponibilidad.{$i}.day_of_week"] = [
                'required',
                'string',
                'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            ];
            $slotRules["disponibilidad.{$i}.start_time"] = [
                'required',
                'date_format:H:i',
                new DentroHorarioEmpresa($dia, $companyId, validarDia: true),
                new SinSolapamientoEnSlots($dia, $i, $slots),
            ];
            $slotRules["disponibilidad.{$i}.end_time"] = [
                'required',
                'date_format:H:i',
                new HoraFinPosteriorAInicio($slot['start_time'] ?? null, $diasNombres[$dia] ?? $dia),
                new DentroHorarioEmpresa($dia, $companyId, validarDia: false),
            ];
        }

        if ($slotRules) {
            $request->validate($slotRules);
        }

        // Imagen
        $imagePath = $request->file('image')->store('users', 'public');

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'image'    => $imagePath,
        ]);

        // Asignar el rol del usuario
        $user->assignRole('empleado');

        // Asignar la empresa al usuario
        $user->companies()->attach($companyId);

        // Asingar los servicios al usuario
        $user->services()->sync($data['services'] ?? []);


        foreach ($slots as $slot) {
            $user->professionalAvailabilities()->create([
                'day_of_week' => $slot['day_of_week'],
                'start_time'  => $slot['start_time'],
                'end_time'    => $slot['end_time'],
            ]);
        }

        return Redirect::route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $user = User::find($id);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $companyId = session('active_company_id');
        $user = User::find($id);
        $roles = Role::all();
        $services = Service::where("company_id", $companyId)->get();
        $horariosEmpresa = OpeningHour::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->selectRaw('day_of_week, MIN(start_time) as start_time, MAX(end_time) as end_time')
            ->groupBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');


        return view('users.edit', compact('user', 'roles', 'horariosEmpresa', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        $slots     = $request->input('disponibilidad', []);
        $companyId = session('active_company_id');

        abort_if(!$companyId, 403, 'No hay empresa activa en sesión.');

        $slotRules = [];
        foreach ($slots as $i => $slot) {
            $dia = $slot['day_of_week'] ?? '';

            $slotRules["disponibilidad.{$i}.day_of_week"] = [
                'required',
                'string',
                'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            ];
            $slotRules["disponibilidad.{$i}.start_time"] = [
                'required',
                'date_format:H:i',
                new DentroHorarioEmpresa($dia, $companyId, validarDia: true),
                new SinSolapamientoEnSlots($dia, $i, $slots),  // ← mismo que en store
            ];
            $slotRules["disponibilidad.{$i}.end_time"] = [
                'required',
                'date_format:H:i',
                "after:disponibilidad.{$i}.start_time",
                new DentroHorarioEmpresa($dia, $companyId, validarDia: false),
            ];
        }

        if ($slotRules) {
            $request->validate($slotRules);
        }

        // Manejo de imagen
        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $data['image'] = $request->file('image')->store('users', 'public');
        }

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'image' => $data['image'] ?? $user->image,
        ]);

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $user->save();
        }

        // Sincronizar roles — sync() añade los nuevos y elimina los que se quitaron
        $user->syncRoles('empleado');

        // Sincronizar servicios — sync() añade los nuevos y elimina los que se quitaron
        $user->services()->sync($data['services'] ?? []);


        $user->professionalAvailabilities()->forceDelete();
        foreach ($slots as $slot) {
            $user->professionalAvailabilities()->create([
                'day_of_week' => $slot['day_of_week'],
                'start_time'  => $slot['start_time'],
                'end_time'    => $slot['end_time'],
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

    public function restore(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        $user->professionalAvailabilities()->onlyTrashed()->restore();

        return redirect()->route('users.index')
            ->with('success', 'Usuario restaurado correctamente');
    }
}
