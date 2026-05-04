<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentConfirmationMail;
use App\Mail\AppointmentAdminNotificationMail;
use App\Mail\AppointmentCancelledAdminMail;
use App\Models\Company;
use App\Models\Service;
use App\Models\User;
use App\Models\Customer;
use App\Models\TypeCompany;
use App\Models\Appointment;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
        $companyId    = $request->company_id;
        $fecha        = $request->fecha;
        $hora         = $request->hora; // hora de inicio de ESTE servicio
        $serviceId    = (int) $request->service_id;
        $excluirUsers = (array) ($request->excluir_users ?? []);

        $service   = Service::findOrFail($serviceId);
        $dayOfWeek = Carbon::parse("$fecha 12:00:00")->format('l');
        $inicio    = Carbon::parse("$fecha $hora:00");
        $fin       = $inicio->copy()->addMinutes($service->duration);

        if ($inicio->isPast()) {
            return response()->json(['profesionales' => []]);
        }

        $profesionales = User::whereHas('companies', fn($q) =>
        $q->where('companies.id', $companyId))
            ->whereHas('services', fn($q) =>
            $q->where('services.id', $serviceId))
            ->whereHas('professionalAvailabilities', fn($q) =>
            $q->where('day_of_week', $dayOfWeek)
                ->where('start_time', '<=', $inicio->format('H:i:s'))
                ->where('end_time',   '>=', $fin->format('H:i:s')))
            ->whereDoesntHave('appointments', fn($q) =>
            $q->where('start_time', '<', $fin)
                ->where('end_time',   '>', $inicio)
                ->where('status', '<>', 'cancelled'))
            ->whereNotIn('id', $excluirUsers) // excluir profesionales ya asignados
            ->get(['id', 'name', 'phone', 'image']);

        return response()->json([
            'profesionales' => $profesionales,
            'hora_inicio'   => $inicio->format('H:i'),
            'hora_fin'      => $fin->format('H:i'),
        ]);
    }

    // ─── PASO 3 STORE: Guardar cita ─────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        // \Log::info('Store iniciado', $request->all());
        $validated = validator($request->all(), [
            'company_id'              => 'required|exists:companies,id',
            'fecha'                   => 'required|date|after_or_equal:today',
            'hora'                    => 'required',
            'asignaciones'            => 'required|array|min:1',
            'asignaciones.*.user_id'  => 'required|exists:users,id',
            'asignaciones.*.service_id' => 'required|exists:services,id',
            'asignaciones.*.hora_inicio' => 'required',
        ]);

        // \Log::info('Errores', $validated->errors()->toArray());
        // \Log::info('Errores de validacion', session()->get('errors') ? session()->get('errors')->toArray() : []);
        // \Log::info('Validación pasada');
        try {
            \DB::transaction(function () use ($request) {
                // \Log::info('Dentro de la transacción');
                $bookingGroup = \Str::uuid();
                $fecha        = $request->fecha;
                $companyId    = $request->company_id;

                $user     = auth()->user();
                $customer = Customer::firstOrCreate(
                    ['email' => $user->email, 'company_id' => $companyId],
                    ['name'  => $user->name, 'phone' => $user->phone ?? '', 'company_id' => $companyId]
                );
                // \Log::info('Customer', ['id' => $customer->id]);

                foreach ($request->asignaciones as $asignacion) {
                    // \Log::info('Procesando asignacion', $asignacion);
                    $servicio  = Service::findOrFail($asignacion['service_id']);
                    $inicio    = Carbon::parse("$fecha {$asignacion['hora_inicio']}:00");
                    $fin       = $inicio->copy()->addMinutes($servicio->duration);
                    if ($inicio->isPast()) {
                        throw new \Exception('hora_pasada');
                    }
                    // \Log::info('Horario', ['inicio' => $inicio, 'fin' => $fin]);

                    // Verificar conflicto con bloqueo pesimista
                    $conflicto = Appointment::where('user_id', $asignacion['user_id'])
                        ->where('start_time', '<', $fin)
                        ->where('end_time', '>', $inicio)
                        ->lockForUpdate()
                        ->exists();
                    // \Log::info('Conflicto', ['hay_conflicto' => $conflicto]);

                    if ($conflicto) throw new \Exception('slot_ocupado');

                    $appointment = Appointment::create([
                        'start_time'   => $inicio,
                        'end_time'     => $fin,
                        'customer_id'  => $customer->id,
                        'user_id'      => $asignacion['user_id'],
                        'company_id'   => $companyId,
                        'notes'        => $request->notas,
                        'booking_group' => $bookingGroup,
                        'status'        => 'pending',
                        'cancel_token'  => Str::random(40),
                        'cancel_token_expires_at' => now()->addDays(7)
                    ]);
                    // \Log::info('Cita creada', ['id' => $appointment->id]);

                    $appointment->services()->attach($asignacion['service_id']);

                    // Cargar relaciones para el email
                    $appointment->load(['customer', 'user', 'company', 'services']);

                    // Email al cliente
                    $this->enviarEmail(
                        new AppointmentConfirmationMail($appointment),
                        $appointment->customer->email,
                        $appointment->id,
                        'confirmation'
                    );

                    // Email al admin
                    $adminEmail = $appointment->company->email;
                    if ($adminEmail) {
                        $this->enviarEmail(
                            new AppointmentAdminNotificationMail($appointment),
                            $adminEmail,
                            $appointment->id,
                            'admin_notification'
                        );
                    }
                }
            });

            return redirect()->route('appointment.index')
                ->with('success', '¡Cita agendada correctamente!');
        } catch (\Exception $e) {
            // \Log::error('Error en store', [
            //     'mensaje' => $e->getMessage(),
            //     'linea'   => $e->getLine(),
            //     'archivo' => $e->getFile(),
            // ]);
            $mensaje = match ($e->getMessage()) {
                'slot_ocupado' => 'Un horario fue reservado mientras confirmabas. Por favor selecciona otro.',
                'hora_pasada'  => 'No puedes agendar citas en horarios que ya pasaron.',
                default        => 'Ocurrió un error al agendar la cita. Intenta de nuevo.',
            };

            return redirect()->back()->with('error', $mensaje)->withInput();
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
        $companyId  = $request->company_id;
        $start      = Carbon::parse($request->start);
        $end        = Carbon::parse($request->end);
        $serviceIds = (array) $request->services;

        $services = Service::whereIn('id', $serviceIds)->orderBy('id')->get();

        if ($services->isEmpty() || $start >= $end) {
            return response()->json(['citas' => [], 'disponibles' => [], 'totalProfesionales' => 0]);
        }

        // Profesionales de la empresa con sus horarios y servicios
        $profesionales = User::whereHas('companies', fn($q) =>
        $q->where('companies.id', $companyId))
            ->whereHas('services', fn($q) =>
            $q->whereIn('services.id', $serviceIds))
            ->with(['professionalAvailabilities', 'services'])
            ->get();

        $horaMinGlobal = $profesionales->flatMap->professionalAvailabilities->min('start_time');
        $horaMaxGlobal = $profesionales->flatMap->professionalAvailabilities->max('end_time');

        if (!$horaMinGlobal || !$horaMaxGlobal) {
            return response()->json(['citas' => [], 'disponibles' => [], 'totalProfesionales' => 0]);
        }

        // Citas existentes en el rango
        $citasExistentes = Appointment::whereHas('user.companies', fn($q) =>
        $q->where('companies.id', $companyId))
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->where('status', '<>', 'cancelled')
            ->get(['start_time', 'end_time', 'user_id']);

        // Contar profesionales que realmente pueden participar en al menos una cadena válida
        $dayOfWeekHoy = $start->format('l');
        $totalProfesionales = $profesionales->filter(function ($prof) use (
            $services,
            $profesionales,
            $citasExistentes,
            $start,
            $dayOfWeekHoy
        ) {
            // ¿Puede este profesional cubrir al menos un servicio en su ventana?
            foreach ($services as $index => $servicio) {
                $horaIni = $start->format('H:i:s');
                $horaFin = $start->copy()->addMinutes($servicio->duration)->format('H:i:s');

                $tieneServicio = $prof->services->contains('id', $servicio->id);
                $tieneHorario  = $prof->professionalAvailabilities->contains(
                    fn($pa) =>
                    $pa->day_of_week === $dayOfWeekHoy
                        && $pa->start_time <= $horaIni
                        && $pa->end_time   >= $horaFin
                );

                if ($tieneServicio && $tieneHorario) return true;
            }
            return false;
        })->count();

        $slots     = [];
        $cursorFecha = $start->copy();

        while ($cursorFecha < $end) {
            $fecha   = $cursorFecha->format('Y-m-d');
            $cursor  = Carbon::parse("$fecha $horaMinGlobal");
            $diaFin  = Carbon::parse("$fecha $horaMaxGlobal");

            while ($cursor < $diaFin) {
                $slotInicio = $cursor->copy();
                $dayOfWeek  = $slotInicio->format('l');

                // Verificar si existe combinación válida de profesionales consecutivos
                $combinacionValida = $this->verificarCadenaConsecutiva(
                    $services,
                    $profesionales,
                    $citasExistentes,
                    $slotInicio,
                    $dayOfWeek
                );

                $combinaciones = $this->verificarCadenaConsecutiva(
                    $services,
                    $profesionales,
                    $citasExistentes,
                    $slotInicio,
                    $dayOfWeek
                );

                $slots[] = [
                    'fecha'       => $fecha,
                    'inicio'      => $slotInicio->format('H:i'),
                    'fin'         => $slotInicio->copy()->addMinutes(30)->format('H:i'),
                    'disponibles' => $combinaciones,
                ];

                $cursor->addMinutes(30);
            }

            $cursorFecha->addDay();
        }

        $horaMinStr = substr($horaMinGlobal, 0, 5);
        $horaMaxStr = substr($horaMaxGlobal, 0, 5);

        $bloqueados = collect($slots)->filter(
            fn($s) =>
            $s['disponibles'] === 0
                && $s['inicio'] >= $horaMinStr
                && $s['fin']    <= $horaMaxStr
                && $s['fin']    !== '00:00'
        )->map(fn($s) => ['fecha' => $s['fecha'], 'inicio' => $s['inicio'], 'fin' => $s['fin']])
            ->values();

        $disponiblesPorSlot = collect($slots)->filter(fn($s) => $s['disponibles'] > 0)->values();

        return response()->json([
            'citas'              => $bloqueados,
            'disponibles'        => $disponiblesPorSlot,
            'totalProfesionales' => $totalProfesionales,
        ]);
    }

    // ─── Helper: verificar si existe cadena consecutiva válida ──────────
    private function verificarCadenaConsecutiva(
        $services,
        $profesionales,
        $citasExistentes,
        Carbon $slotInicio,
        string $dayOfWeek
    ): int {
        $contador = 0;
        $this->contarCadenas(
            0,
            $services,
            $profesionales,
            $citasExistentes,
            $slotInicio,
            $dayOfWeek,
            [],
            $contador
        );
        return $contador;
    }

    private function contarCadenas(
        int $index,
        $services,
        $profesionales,
        $citasExistentes,
        Carbon $cursor,
        string $dayOfWeek,
        array $asignados,
        int &$contador
    ): void {
        if ($index >= $services->count()) {
            $contador++;
            return;
        }

        $servicio   = $services[$index];
        $inicio     = $cursor->copy();
        $fin        = $inicio->copy()->addMinutes($servicio->duration);
        $horaIniStr = $inicio->format('H:i:s');
        $horaFinStr = $fin->format('H:i:s');

        foreach ($profesionales as $prof) {
            if (!$prof->services->contains('id', $servicio->id)) continue;
            if (in_array($prof->id, $asignados)) continue;

            $tieneHorario = $prof->professionalAvailabilities->contains(
                fn($pa) =>
                $pa->day_of_week === $dayOfWeek
                    && $pa->start_time <= $horaIniStr
                    && $pa->end_time   >= $horaFinStr
            );
            if (!$tieneHorario) continue;

            $ocupado = $citasExistentes->contains(
                fn($cita) =>
                Carbon::parse($cita->start_time) < $fin
                    && Carbon::parse($cita->end_time) > $inicio
                    && $cita->user_id === $prof->id
            );
            if ($ocupado) continue;

            $this->contarCadenas(
                $index + 1,
                $services,
                $profesionales,
                $citasExistentes,
                $fin,
                $dayOfWeek,
                [...$asignados, $prof->id],
                $contador
            );
        }
    }

    private function asignarServicio(
        int $index,
        $services,
        $profesionales,
        $citasExistentes,
        Carbon $cursor,
        string $dayOfWeek,
        array $asignados
    ): bool {
        // Todos los servicios asignados → combinación válida encontrada
        if ($index >= $services->count()) return true;

        $servicio  = $services[$index];
        $duration  = $servicio->duration;
        $inicio    = $cursor->copy();
        $fin       = $inicio->copy()->addMinutes($duration);
        $horaIniStr = $inicio->format('H:i:s');
        $horaFinStr = $fin->format('H:i:s');

        foreach ($profesionales as $prof) {
            // El profesional debe manejar este servicio
            if (!$prof->services->contains('id', $servicio->id)) continue;

            // No puede estar ya asignado en esta cadena (mismo profesional, dos servicios simultáneos)
            if (in_array($prof->id, $asignados)) continue;

            // Debe tener horario ese día cubriendo esta ventana
            $tieneHorario = $prof->professionalAvailabilities->contains(
                fn($pa) =>
                $pa->day_of_week === $dayOfWeek
                    && $pa->start_time <= $horaIniStr
                    && $pa->end_time   >= $horaFinStr
            );
            if (!$tieneHorario) continue;

            // No debe tener cita en esa ventana
            $ocupado = $citasExistentes->contains(
                fn($cita) =>
                Carbon::parse($cita->start_time) < $fin
                    && Carbon::parse($cita->end_time) > $inicio
                    && $cita->user_id === $prof->id
            );
            if ($ocupado) continue;

            // Este profesional es válido → intentar asignar el siguiente servicio
            if ($this->asignarServicio(
                $index + 1,
                $services,
                $profesionales,
                $citasExistentes,
                $fin,
                $dayOfWeek,
                [...$asignados, $prof->id]
            )) return true;
        }

        return false;
    }
    public function cancelByToken(string $token)
    {
        $appointment = Appointment::where('cancel_token', $token)
            ->whereNull('deleted_at')
            ->first();

        if (!$appointment) {
            return view('appointment.cancel-invalid');
        }

        if ($appointment->status === 'cancelled') {
            return view('appointment.cancel-already');
        }

        if ($appointment->cancel_token_expires_at && now()->gt($appointment->cancel_token_expires_at)) {
            return view('appointment.cancel-expired');
        }

        if (now()->gt($appointment->start_time->subHours(2))) {
            return view('appointment.cancel-toolate');
        }

        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => 'Cancelada por el cliente desde el enlace del correo.',
        ]);

        $appointment->load(['customer', 'user', 'company', 'services']);
        $adminEmail = $appointment->company->email;
        if ($adminEmail) {
            $this->enviarEmail(
                new AppointmentCancelledAdminMail($appointment),
                $adminEmail,
                $appointment->id,
                'cancelled_admin'
            );
        }

        return view('appointment.cancel-success', compact('appointment'));
    }
    public function misCitas(): View
    {
        $user     = auth()->user();
        $customerIds = Customer::where('email', $user->email)->pluck('id');
        \Log::info('Customer encontrado', [
            'user_email' => $user->email,
            'customerIds' => $customerIds?->toArray(),
        ]);

        if (!$customerIds) {
            return view('appointment.history', [
                'proximas'   => collect(),
                'historicas' => collect(),
            ]);
        }

        $historicas = Appointment::withTrashed()
            ->whereIn('customer_id', $customerIds)
            ->where(function ($q) {
                $q->whereIn('status', ['completed', 'cancelled'])
                    ->orWhere('start_time', '<', now());
            })
            ->with(['services', 'user', 'company'])
            ->orderBy('start_time', 'desc')
            ->get();

        $proximas = Appointment::whereIn('customer_id', $customerIds)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_time', '>=', now())
            ->with(['services', 'user', 'company'])
            ->orderBy('start_time')
            ->get();


        return view('appointment.history', compact('proximas', 'historicas'));
    }
    private function enviarEmail($mailable, string $email, int $appointmentId, string $type): void
    {
        try {
            Mail::to($email)->send($mailable);
            NotificationLog::create([
                'appointment_id'  => $appointmentId,
                'type'            => $type,
                'recipient_email' => $email,
                'status'          => 'sent',
            ]);
        } catch (\Exception $e) {
            NotificationLog::create([
                'appointment_id'  => $appointmentId,
                'type'            => $type,
                'recipient_email' => $email,
                'status'          => 'error',
                'error_message'   => $e->getMessage(),
            ]);
        }
    }
}
