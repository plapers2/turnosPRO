<?php

namespace App\Http\Controllers;

use App\Models\OpeningHour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\OpeningHourRequest;
use App\Models\ProfessionalAvailability;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OpeningHourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('opening-hour.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $openingHour = new OpeningHour();

        return view('opening-hour.create', compact('openingHour'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'dia' => 'required',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
        ]);

        // VALIDAR QUE NO CHOQUEN HORARIOS ENTRE SI
        $companyId = session('active_company_id');
        $exists = OpeningHour::where('company_id', $companyId)
            ->where('day_of_week', $data['dia'])
            ->where(function ($query) use ($data) {
                $query->where('start_time', '<', $data['hora_fin'])
                    ->where('end_time', '>', $data['hora_inicio']);
            })
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['hora_inicio' => 'Este horario se choca con otro existente'])
                ->withInput();
        }

        $companyId = session('active_company_id');

        OpeningHour::create([
            "day_of_week" => $data["dia"],
            'start_time' => $data["hora_inicio"],
            'end_time' => $data["hora_fin"],
            'company_id' => $companyId
        ]);

        return redirect()->route('opening-hours.index')->with("success", "Horario de atencion creado");
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $openingHour = OpeningHour::find($id);

        return view('opening-hour.show', compact('openingHour'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $openingHour = OpeningHour::find($id);

        return view('opening-hour.edit', compact('openingHour'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OpeningHour $openingHour)
    {
        $data = $request->validate([
            'dia' => 'required',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:start_time',
        ]);

        // VALIDAR QUE NO CHOQUEN HORARIOS ENTRE SI
        $companyId = session('active_company_id');
        $exists = OpeningHour::where('company_id', $companyId)
            ->where('day_of_week', $data['dia'])
            ->where(function ($query) use ($data) {
                $query->where('start_time', '<', $data['hora_fin'])
                    ->where('end_time', '>', $data['hora_inicio']);
            })
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['hora_inicio' => 'Este horario se choca con otro existente'])
                ->withInput();
        }

        $company_id = session('active_company_id');

        $openingHour->update([
            "day_of_week" => $data["dia"],
            'start_time' => $data["hora_inicio"],
            'end_time' => $data["hora_fin"],
            'company_id' => $company_id
        ]);

        return redirect()->route('opening-hours.index');
    }

    public function destroy($id)
    {
        $hour = OpeningHour::findOrFail($id);
        $companyId = session('active_company_id');

        // Seguridad
        if ($hour->company_id != $companyId) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        // 1. Buscar disponibilidades afectadas
        $availabilities = ProfessionalAvailability::where('day_of_week', $hour->day_of_week)
            ->where(function ($query) use ($hour) {
                $query->where('start_time', '<', $hour->end_time)
                    ->where('end_time', '>', $hour->start_time);
            })
            ->whereHas('user.companies', function ($q) use ($companyId) {
                $q->where('companies.id', $companyId);
            })
            ->get();

        foreach ($availabilities as $availability) {

            // 2. Verificar si existe OTRO horario que cubra esta disponibilidad
            $covered = OpeningHour::where('company_id', $companyId)
                ->where('id', '!=', $hour->id)
                ->where('day_of_week', $availability->day_of_week)
                ->where('start_time', '<=', $availability->start_time)
                ->where('end_time', '>=', $availability->end_time)
                ->exists();

            // 3. Si NO está cubierto → bloquear
            if (!$covered) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar este horario porque dejaría disponibilidades fuera del horario permitido.'
                ], 422);
            }
        }

        // Eliminar
        $hour->delete();

        return response()->json([
            'success' => true,
            'message' => 'Horario eliminado correctamente'
        ]);
    }

    public function restore($id)
    {
        $hour = OpeningHour::withTrashed()->findOrFail($id);
        $hour->restore();

        return response()->json(['success' => true]);
    }
}
