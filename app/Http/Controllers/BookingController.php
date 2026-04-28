<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Service;
use App\Models\User;
use App\Models\Customer;
use App\Models\TypeCompany;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class BookingController extends Controller
{
    // ─── PASO 1: Seleccionar empresa ────────────────────────────────────
    public function selectCompany(): View
    {
        $tiposNegocio = TypeCompany::with(['companies'])
            ->get();

        return view('appointment.index', compact('tiposNegocio'));
    }

    // ─── PASO 2: Seleccionar servicios ──────────────────────────────────
    public function selectServices(Company $company): View
    {
        $services = $company->services()->get();

        return view('appointment.select-services', compact('company', 'services'));
    }

    // ─── PASO 3: Confirmar cita ─────────────────────────────────────────
    public function prepareCreate(Request $request): View|RedirectResponse
    {
        $companyId  = $request->company_id;
        $serviceIds = $request->services ?? [];

        if (!$companyId || empty($serviceIds)) {
            return redirect()->route('booking.selectCompany')
                ->with('error', 'Selecciona una empresa y al menos un servicio.');
        }

        $company  = Company::findOrFail($companyId);
        $services = Service::whereIn('id', $serviceIds)->get();

        $totalDuration = $services->sum('duration');
        $totalPrice    = $services->sum('price');

        return view('appointment.confirm', compact('company', 'services', 'totalDuration', 'totalPrice'));
    }

    // ─── API: Profesionales disponibles (AJAX) ──────────────────────────
    public function profesionalesDisponibles(Request $request): JsonResponse
    {
        $companyId = $request->company_id;
        $fecha     = $request->fecha;           // Y-m-d
        $hora      = $request->hora;            // H:i
        $duration  = (int) $request->duration;  // minutos

        // day_of_week en inglés como está en tu BD (ENUM en professional_availabilities)
        $dayOfWeek = Carbon::parse($fecha)->format('l'); // Monday, Tuesday...

        $inicio = Carbon::parse("$fecha $hora:00");
        $fin    = $inicio->copy()->addMinutes($duration);

        $profesionales = User::whereHas('companies', function ($q) use ($companyId) {
            // tabla pivot: company_user con columnas user_id, company_id
            $q->where('companies.id', $companyId);
        })
            ->whereHas('professionalAvailabilities', function ($q) use ($dayOfWeek, $hora, $fin) {
                $q->where('day_of_week', $dayOfWeek)
                    ->where('start_time', '<=', $hora . ':00')
                    ->where('end_time',   '>=', $fin->format('H:i:s'));
            })
            // Excluir profesionales con cita solapada en ese rango
            ->whereDoesntHave('appointments', function ($q) use ($inicio, $fin) {
                $q->where(function ($q2) use ($inicio, $fin) {
                    $q2->whereBetween('start_time', [$inicio, $fin])
                        ->orWhereBetween('end_time',   [$inicio, $fin])
                        ->orWhere(function ($q3) use ($inicio, $fin) {
                            $q3->where('start_time', '<=', $inicio)
                                ->where('end_time',   '>=', $fin);
                        });
                });
            })
            ->get(['id', 'name', 'phone', 'image']);

        return response()->json(['profesionales' => $profesionales]);
    }

    // ─── PASO 3 STORE: Guardar cita ─────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'fecha'       => 'required|date|after:today',
            'hora'        => 'required',
            'user_id'     => 'required|exists:users,id',
            'services'    => 'required|array|min:1',
            'services.*'  => 'exists:services,id',
        ]);

        $inicio   = Carbon::parse("{$request->fecha} {$request->hora}:00");
        $services = Service::whereIn('id', $request->services)->get();
        $duration = $services->sum('duration');
        $fin      = $inicio->copy()->addMinutes($duration);

        // Crear o encontrar customer a partir del usuario autenticado
        $user     = auth()->user();
        $customer = Customer::firstOrCreate(
            [
                'email'      => $user->email,
                'company_id' => $request->company_id,
            ],
            [
                'name'       => $user->name,
                'phone'      => $user->phone ?? '',
                'company_id' => $request->company_id,
            ]
        );

        // Crear la cita — columnas según tabla appointments del MER
        $appointment = Appointment::create([
            'start_time'  => $inicio,
            'end_time'    => $fin,
            'status'      => 'Pending',
            'customer_id' => $customer->id,
            'user_id'     => $request->user_id,
            'company_id'  => $request->company_id,
            'notes'       => $request->notas,
        ]);

        // Asociar servicios en la tabla pivot appointment_service
        $appointment->services()->attach($request->services);

        return redirect()->route('appointment.index')
            ->with('success', '¡Cita agendada correctamente!');
    }
}
