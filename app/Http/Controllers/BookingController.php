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
            return redirect()->route('appointment.index')
                ->with('error', 'Selecciona una empresa y al menos un servicio.');
        }

        $company  = Company::findOrFail($companyId);
        $services = Service::whereIn('id', $serviceIds)->get();

        $totalDuration = $services->sum('duration');
        $totalPrice    = $services->sum('price');

        // Horario mínimo y máximo de todos los profesionales de la empresa
        $availabilities = \DB::table('professional_availabilities')
            ->whereIn('user_id', $company->users()->pluck('users.id'))
            ->selectRaw('MIN(start_time) as hora_inicio, MAX(end_time) as hora_fin')
            ->first();

        // Fallback si no hay horarios cargados
        $horaInicio = $availabilities->hora_inicio
            ? \Carbon\Carbon::parse($availabilities->hora_inicio)->format('H:i')
            : '08:00';

        $horaFin = $availabilities->hora_fin
            ? \Carbon\Carbon::parse($availabilities->hora_fin)->format('H:i')
            : '20:00';

        return view('appointment.confirm', compact(
            'company',
            'services',
            'totalDuration',
            'totalPrice',
            'horaInicio',
            'horaFin'
        ));
    }

    // ─── API: Profesionales disponibles (AJAX) ──────────────────────────
    public function profesionalesDisponibles(Request $request): JsonResponse
    {
        $companyId = $request->company_id;
        $fecha     = $request->fecha;
        $hora      = $request->hora;
        $duration  = (int) $request->duration;

        $dayOfWeek = Carbon::parse($fecha)->format('l');

        $inicio = Carbon::parse("$fecha $hora:00");
        $fin    = $inicio->copy()->addMinutes($duration);

        $profesionales = User::whereHas('companies', function ($q) use ($companyId) {
            $q->where('companies.id', $companyId);
        })
            ->whereHas('professionalAvailabilities', function ($q) use ($dayOfWeek, $hora, $fin) {
                $q->where('day_of_week', $dayOfWeek)
                    ->where('start_time', '<=', $hora . ':00')
                    ->where('end_time',   '>=', $fin->format('H:i:s'));
            })
            ->whereDoesntHave('appointments', function ($q) use ($inicio, $fin) {
                $q->where(function ($q2) use ($inicio, $fin) {
                    $q2->where('start_time', '<', $fin)
                        ->where('end_time', '>', $inicio);
                });
            })
            ->get(['id', 'name', 'phone', 'image']);

        // ── NUEVO: horario disponible ese día específico ──────────────────
        $horarioDelDia = \DB::table('professional_availabilities')
            ->whereIn('user_id', User::whereHas('companies', fn($q) =>
            $q->where('companies.id', $companyId))->pluck('id'))
            ->where('day_of_week', $dayOfWeek)
            ->selectRaw('MIN(start_time) as hora_inicio, MAX(end_time) as hora_fin')
            ->first();

        return response()->json([
            'profesionales' => $profesionales,
            'hora_inicio'   => $horarioDelDia->hora_inicio
                ? Carbon::parse($horarioDelDia->hora_inicio)->format('H:i')
                : null,
            'hora_fin'      => $horarioDelDia->hora_fin
                ? Carbon::parse($horarioDelDia->hora_fin)->format('H:i')
                : null,
        ]);
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
    // En BookingController, agregar este método:
    public function horariosEmpresa(Request $request): JsonResponse
    {
        $companyId = $request->company_id;

        $horarios = \DB::table('professional_availabilities')
            ->whereIn('user_id', User::whereHas('companies', fn($q) =>
            $q->where('companies.id', $companyId))->pluck('id'))
            ->select('day_of_week', 'start_time as hora_inicio', 'end_time as hora_fin')
            ->distinct()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json(['horarios' => $horarios]);
    }
    public function citasOcupadas(Request $request): JsonResponse
    {
        $companyId = $request->company_id;
        $start     = Carbon::parse($request->start);
        $end       = Carbon::parse($request->end);

        $totalProfesionales = User::whereHas(
            'companies',
            fn($q) =>
            $q->where('companies.id', $companyId)
        )->count();
        \Log::info('debug-citas', [
            'total_profesionales' => $totalProfesionales,
            'total_citas_sin_filtro' => Appointment::whereHas('user.companies', fn($q) =>
            $q->where('companies.id', $companyId))
                ->whereBetween('start_time', [$start, $end])
                ->count(),
        ]);


        // Citas agrupadas por fecha y hora de inicio/fin
        // Solo devuelve slots donde el número de citas == total de profesionales
        $citas = Appointment::whereHas('user.companies', fn($q) =>
        $q->where('companies.id', $companyId))
            ->whereBetween('start_time', [$start, $end])
            ->select(
                'start_time',
                'end_time',
                \DB::raw('COUNT(DISTINCT user_id) as ocupados')
            )
            ->groupBy('start_time', 'end_time')
            ->having('ocupados', '>=', $totalProfesionales)
            ->get()
            ->map(fn($c) => [
                'fecha'  => Carbon::parse($c->start_time)->format('Y-m-d'),
                'inicio' => Carbon::parse($c->start_time)->format('H:i'),
                'fin'    => Carbon::parse($c->end_time)->format('H:i'),
            ]);

        return response()->json(['citas' => $citas]);
    }
}
