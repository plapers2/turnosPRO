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

        $dayOfWeek = Carbon::parse($fecha . ' 12:00:00')->format('l');

        $inicio = Carbon::parse("$fecha $hora:00");
        $fin    = $inicio->copy()->addMinutes($duration);
        if ($inicio->isPast()) {
            return response()->json(['profesionales' => []]);
        }

        $query = User::whereHas('companies', function ($q) use ($companyId) {
            $q->where('companies.id', $companyId);
        })
            ->whereHas('professionalAvailabilities', function ($q) use ($dayOfWeek, $hora, $fin) {
                $q->where('day_of_week', $dayOfWeek)
                    ->where('start_time', '<=', $hora . ':00')
                    ->where('end_time',   '>=', $fin->format('H:i:s'));
            })
            ->whereHas('services', function ($q) use ($request) {
                $q->whereIn('services.id', (array) $request->services);
            })
            ->whereDoesntHave('appointments', function ($q) use ($inicio, $fin) {
                $q->where('start_time', '<', $fin)
                    ->where('end_time', '>', $inicio);
            });

        $profesionales = $query->get(['id', 'name', 'phone', 'image']);

        // ── horario disponible ese día específico ──────────────────
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

        try {
            $appointment = \DB::transaction(function () use ($request, $inicio, $fin, $services, $duration) {

                // Bloquear las citas del profesional para evitar concurrencia
                $conflicto = Appointment::where('user_id', $request->user_id)
                    ->where('start_time', '<', $fin)
                    ->where('end_time', '>', $inicio)
                    ->lockForUpdate()
                    ->exists();

                if ($conflicto) {
                    throw new \Exception('slot_ocupado');
                }

                // Verificar que el profesional sigue perteneciendo a la empresa
                $profesional = User::whereHas('companies', fn($q) =>
                $q->where('companies.id', $request->company_id))
                    ->where('id', $request->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();

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

                $appointment = Appointment::create([
                    'start_time'  => $inicio,
                    'end_time'    => $fin,
                    'customer_id' => $customer->id,
                    'user_id'     => $request->user_id,
                    'company_id'  => $request->company_id,
                    'notes'       => $request->notas,
                ]);

                $appointment->services()->attach($request->services);

                return $appointment;
            });

            return redirect()->route('appointment.index')
                ->with('success', '¡Cita agendada correctamente!');
        } catch (\Exception $e) {
            $mensaje = $e->getMessage() === 'slot_ocupado'
                ? 'Este horario acaba de ser reservado por otra persona. Por favor selecciona otro.'
                : 'Ocurrió un error al agendar la cita. Intenta de nuevo.';

            return redirect()->back()
                ->with('error', $mensaje)
                ->withInput();
        }
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
        $duration = (int) $request->duration;
        $companyId = $request->company_id;
        $start     = Carbon::parse($request->start);
        $end       = Carbon::parse($request->end);
        if ($duration <= 0 || $start >= $end) {
            return response()->json(['citas' => [], 'disponibles' => [], 'totalProfesionales' => 0]);
        }

        // Todos los profesionales de la empresa con sus horarios
        $profesionales = User::whereHas('companies', fn($q) =>
        $q->where('companies.id', $companyId))
            ->whereHas('services', function ($q) use ($request) {
                $q->whereIn('services.id', (array) $request->services);
            })
            ->with('professionalAvailabilities')
            ->get();

        $totalProfesionales = $profesionales->count();

        // Citas existentes en el rango
        $citasExistentes = Appointment::whereHas('user.companies', fn($q) =>
        $q->where('companies.id', $companyId))
            ->where('start_time', '<', $end)
            ->where('end_time',   '>', $start)
            ->get(['start_time', 'end_time', 'user_id']);

        // Generar slots de 30 min para toda la semana
        $horaMinGlobal = $profesionales->flatMap->professionalAvailabilities->min('start_time');
        $horaMaxGlobal = $profesionales->flatMap->professionalAvailabilities->max('end_time');

        $slots = [];
        $cursorFecha = $start->copy();

        while ($cursorFecha < $end) {

            $cursor = Carbon::parse($cursorFecha->format('Y-m-d') . ' ' . $horaMinGlobal);
            $diaFin = Carbon::parse($cursorFecha->format('Y-m-d') . ' ' . $horaMaxGlobal);

            while ($cursor < $diaFin) {
                $slotInicio = $cursor->copy();
                $slotFin    = $cursor->copy()->addMinutes(30);
                $dayOfWeek  = $slotInicio->format('l');
                $horaInicio = $slotInicio->format('H:i:s');
                $horaFin    = $slotFin->format('H:i:s');
                $fecha      = $slotInicio->format('Y-m-d');

                $slotFinReal = $slotInicio->copy()->addMinutes($duration)->format('H:i:s');

                $disponibles = $profesionales->filter(function ($prof) use ($dayOfWeek, $horaInicio, $slotInicio, $slotFin, $citasExistentes, $slotFinReal, $duration) {
                    $tieneHorario = $prof->professionalAvailabilities->contains(function ($pa) use ($dayOfWeek, $horaInicio, $slotFinReal) {
                        return $pa->day_of_week === $dayOfWeek
                            && $pa->start_time <= $horaInicio
                            && $pa->end_time >= $slotFinReal;
                    });
                    if (!$tieneHorario) return false;

                    $tieneCita = $citasExistentes->contains(function ($cita) use ($prof, $slotInicio, $duration) {
                        $slotFinReal = $slotInicio->copy()->addMinutes($duration);
                        return $cita->user_id === $prof->id
                            && $cita->start_time < $slotFinReal
                            && $cita->end_time > $slotInicio;
                    });

                    return !$tieneCita;
                })->count();
                $slots[] = [
                    'fecha'       => $fecha,
                    'inicio'      => $slotInicio->format('H:i'),
                    'fin'         => $slotFin->format('H:i'),
                    'disponibles' => $disponibles,
                ];

                $cursor->addMinutes(30);
            }

            $cursorFecha->addDay();
        }

        // Slots bloqueados = dentro del horario global pero sin disponibles
        $horaMinStr = substr($horaMinGlobal, 0, 5);
        $horaMaxStr = substr($horaMaxGlobal, 0, 5);

        $bloqueados = collect($slots)->filter(function ($s) use ($horaMinStr, $horaMaxStr) {
            return $s['disponibles'] === 0
                && $s['inicio'] >= $horaMinStr
                && $s['fin'] <= $horaMaxStr
                && $s['fin'] !== '00:00';
        })->map(fn($s) => [
            'fecha'  => $s['fecha'],
            'inicio' => $s['inicio'],
            'fin'    => $s['fin'],
        ])->values();

        // Slots con disponibilidad parcial o total
        $disponiblesPorSlot = collect($slots)->filter(fn($s) => $s['disponibles'] > 0)
            ->values();
        return response()->json([
            'citas'              => $bloqueados,
            'disponibles'        => $disponiblesPorSlot,
            'totalProfesionales' => $totalProfesionales,
        ]);
    }
}
