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
        $user  = auth()->user();
        $hasta = now()->addMonths(2);

        $filtroEmpresas = function ($q) {
            $q->whereHas(
                'services',
                fn($s) =>
                $s->whereHas(
                    'users',
                    fn($u) =>
                    $u->whereHas('roles', fn($r) => $r->where('name', 'empleado'))
                        ->whereHas('professionalAvailabilities') // solo que tenga horario configurado
                )
            );
        };

        if ($user->isPremium()) {
            $tiposNegocio = TypeCompany::with(['companies' => $filtroEmpresas])->get();
        } else {
            $companyIds = $user->companies()->pluck('companies.id');

            $tiposNegocio = TypeCompany::with(['companies' => function ($q) use ($filtroEmpresas, $companyIds) {
                $filtroEmpresas($q);
                $q->whereIn('companies.id', $companyIds);
            }])->get();
        }

        $tiposNegocio = $tiposNegocio
            ->filter(fn($tipo) => $tipo->companies->isNotEmpty())
            ->values();

        if (!$user->isPremium() && $user->companies()->doesntExist()) {
            return view('appointment.no-company');
        }

        return view('appointment.index', compact('tiposNegocio'));
    }

    // ─── PASO 2: Seleccionar servicios ──────────────────────────────────
    public function selectServices(Company $company): View
    {
        // Solo mostrar servicios que tengan al menos un profesional (empleado)
        // asignado Y con disponibilidad semanal configurada
        $services = $company->services()
            ->whereHas(
                "users",
                fn($q) =>
                $q->whereHas(
                    "roles",
                    fn($r) =>
                    $r->where("name", "empleado")
                )
                    ->whereHas("professionalAvailabilities")
            )
            ->get();

        // Si hay servicios en la empresa pero ninguno está disponible para reservar
        $sinProfesionales = $company->services()->exists() && $services->isEmpty();

        return view("appointment.select-services", compact("company", "services", "sinProfesionales"));
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
        // ── Validación de combinación ────────────────────────────────────────
        if ($services->count() > 1) {
            $profesionales = User::whereHas('companies', fn($q) =>
            $q->where('companies.id', $companyId))
                ->whereHas('services', fn($q) =>
                $q->whereIn('services.id', $serviceIds))
                ->whereHas('roles', fn($q) =>
                $q->where('name', 'empleado'))
                ->with(['professionalAvailabilities', 'services'])
                ->get();

            $diasSemana = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $combinacionValida = false;

            foreach ($diasSemana as $dia) {
                $horasInicio = $profesionales
                    ->flatMap->professionalAvailabilities
                    ->where('day_of_week', $dia)
                    ->pluck('start_time')
                    ->filter();

                if ($horasInicio->isEmpty()) continue;

                $slotInicio = \Carbon\Carbon::parse('2025-01-06 ' . $horasInicio->min());
                $contador   = 0;

                $this->contarCadenas(
                    0,
                    $services,
                    $profesionales,
                    collect(),
                    $slotInicio,
                    $dia,
                    [],
                    $contador
                );

                if ($contador > 0) {
                    $combinacionValida = true;
                    break;
                }
            }

            if (!$combinacionValida) {
                return redirect()->back()
                    ->with('error', 'La combinación de servicios seleccionada no puede ser atendida por ningún profesional. Intenta con una combinación diferente.')
                    ->withInput();
            }
        }
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

    // ─── PASO 4 STORE: Guardar cita ─────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        // \Log::info('Store iniciado', $request->all());
        // $validated = validator($request->all(), [
        //     'company_id'              => 'required|exists:companies,id',
        //     'fecha'                   => 'required|date|after_or_equal:today',
        //     'hora'                    => 'required',
        //     'asignaciones'            => 'required|array|min:1',
        //     'asignaciones.*.user_id'  => 'required|exists:users,id',
        //     'asignaciones.*.service_id' => 'required|exists:services,id',
        //     'asignaciones.*.hora_inicio' => 'required',
        // ]);

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
                $customerIds = Customer::where('user_id', $user->id)->pluck('id');

                $customer = Customer::firstOrCreate(
                    ['user_id' => $user->id, 'company_id' => $companyId]
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

                    // Servicio pertenece a la empresa
                    $servicioValido = Service::where('id', $asignacion['service_id'])
                        ->where('company_id', $companyId)
                        ->exists();
                    if (!$servicioValido) throw new \Exception('datos_invalidos');

                    // Profesional pertenece a la empresa y atiende el servicio
                    $profesionalValido = \App\Models\User::where('id', $asignacion['user_id'])
                        ->whereHas('companies', fn($q) => $q->where('companies.id', $companyId))
                        ->whereHas('services', fn($q) => $q->where('services.id', $asignacion['service_id']))
                        ->exists();
                    if (!$profesionalValido) throw new \Exception('datos_invalidos');
                    // \Log::info('Validando conflicto cliente', [
                    //     'customerIds' => $customerIds->toArray(),
                    //     'inicio' => $inicio,
                    //     'fin' => $fin,
                    // ]);
                    // Verificar conflicto de horario de cliente
                    $citaConflicto = Appointment::whereIn('customer_id', $customerIds)
                        ->where('start_time', '<', $fin)
                        ->where('end_time', '>', $inicio)
                        ->whereNotIn('status', ['cancelled'])
                        ->lockForUpdate()
                        ->first();
                    // \Log::info('Resultado conflicto cliente', ['conflicto' => $conflictoCliente]);
                    if ($citaConflicto) {
                        $fechaFormateada = Carbon::parse($citaConflicto->start_time)
                            ->locale('es')
                            ->isoFormat('dddd D [de] MMMM');
                        $horaFormateada = Carbon::parse($citaConflicto->start_time)->format('h:i A');
                        throw new \Exception("cliente_ocupado:{$fechaFormateada} a las {$horaFormateada}");
                    }

                    // Verificar conflicto con bloqueo pesimista
                    $conflictoProfesional = Appointment::where('user_id', $asignacion['user_id'])
                        ->where('start_time', '<', $fin)
                        ->where('end_time', '>', $inicio)
                        ->whereNotIn('status', ['cancelled'])
                        ->lockForUpdate()
                        ->exists();
                    // \Log::info('Conflicto', ['hay_conflicto' => $conflictoProfesional]);

                    if ($conflictoProfesional) throw new \Exception('slot_ocupado');

                    $appointment = Appointment::create([
                        'start_time'   => $inicio,
                        'end_time'     => $fin,
                        'customer_id'  => $customer->id,
                        'user_id'      => $asignacion['user_id'],
                        'company_id'   => $companyId,
                        'notes'        => $request->notas,
                        'booking_group' => $bookingGroup,
                        'status'        => 'confirmed',
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
            //     'clase'   => get_class($e),
            //     'anterior' => $e->getPrevious()?->getMessage(),
            // ]);

            $mensajeReal = $e->getPrevious()?->getMessage() ?? $e->getMessage();

            if (str_starts_with($mensajeReal, 'cliente_ocupado:')) {
                $detalle = substr($mensajeReal, strlen('cliente_ocupado:'));
                $mensaje = "Ya tienes una cita agendada el {$detalle}. Revisa tus citas antes de continuar.";
            } else {
                $mensaje = match ($mensajeReal) {
                    'slot_ocupado'         => 'Un horario fue reservado mientras confirmabas. Por favor selecciona otro.',
                    'hora_pasada'          => 'No puedes agendar citas en horarios que ya pasaron.',
                    'datos_invalidos'      => 'Los datos enviados no son válidos.',
                    'profesional_invalido' => 'El profesional seleccionado no está disponible.',
                    default                => 'Ocurrió un error al agendar la cita. Intenta de nuevo.',
                };
            }


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
            ->whereHas('roles', fn($q) =>
            $q->where('name', 'empleado'))
            ->with(['professionalAvailabilities', 'services'])
            ->get();
        // return response()->json([
        //     'debug_companyId'  => $companyId,
        //     'debug_serviceIds' => $serviceIds,
        //     'debug_profesionales' => $profesionales->map(fn($p) => [
        //         'id'            => $p->id,
        //         'name'          => $p->name,
        //         'availabilities' => $p->professionalAvailabilities->count(),
        //         'services'      => $p->services->pluck('id'),
        //     ]),
        // ]);
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
            'cancelled_by' => auth()->id(),
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
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');
        // \Log::info('Customer encontrado', [
        //     'user_email' => $user->email,
        //     'customerIds' => $customerIds?->toArray(),
        // ]);

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
            ->whereIn('status', ['confirmed'])
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
    public function exportView(): \Illuminate\View\View
    {
        return view('appointment.export');
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
            'modo'  => 'nullable|in:color,bw',
        ], [
            'hasta.after_or_equal' => 'La fecha "Hasta" no puede ser anterior a la fecha "Desde".',
        ]);

        $companyId = session('active_company_id');
        $company   = \App\Models\Company::findOrFail($companyId);
        $modo      = $request->input('modo', 'color');

        $query = \App\Models\Appointment::with(['customer', 'user', 'services'])
            ->where('company_id', $companyId);

        if ($request->filled('desde')) $query->whereDate('start_time', '>=', $request->desde);
        if ($request->filled('hasta')) $query->whereDate('start_time', '<=', $request->hasta);
        if ($request->filled('status')) $query->where('status', $request->status);

        $appointments = $query->orderBy('start_time')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.appointments', [
            'appointments' => $appointments,
            'company'      => $company,
            'desde'        => $request->desde,
            'hasta'        => $request->hasta,
            'generado_en'  => now()->format('d/m/Y H:i'),
            'modo'         => $modo,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('citas-' . now()->format('Y-m-d') . '.pdf');
    }
    public function validarCombinacion(Request $request): JsonResponse
    {
        $companyId  = $request->company_id;
        $serviceIds = (array) $request->services;

        if (empty($serviceIds)) {
            return response()->json(['valido' => true]);
        }

        $services = Service::whereIn('id', $serviceIds)->orderBy('id')->get();

        // Profesionales de la empresa que atienden AL MENOS UNO de los servicios
        $profesionales = User::whereHas('companies', fn($q) =>
        $q->where('companies.id', $companyId))
            ->whereHas('services', fn($q) =>
            $q->whereIn('services.id', $serviceIds))
            ->whereHas('roles', fn($q) =>
            $q->where('name', 'empleado'))
            ->with(['professionalAvailabilities', 'services'])
            ->get();

        if ($profesionales->isEmpty()) {
            return response()->json(['valido' => false, 'razon' => 'sin_profesionales']);
        }

        // Intentar con cada día de la semana que tenga disponibilidad
        $diasSemana = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($diasSemana as $dia) {
            // Encontrar la hora de inicio más temprana de ese día entre todos los profesionales
            $horasInicio = $profesionales
                ->flatMap->professionalAvailabilities
                ->where('day_of_week', $dia)
                ->pluck('start_time')
                ->filter();

            if ($horasInicio->isEmpty()) continue;

            $horaInicio = $horasInicio->min();
            $slotInicio = \Carbon\Carbon::parse("2025-01-06 $horaInicio"); // lunes fijo para prueba

            $contador = 0;
            $this->contarCadenas(
                0,
                $services,
                $profesionales,
                collect(), // sin citas existentes — validación estructural pura
                $slotInicio,
                $dia,
                [],
                $contador
            );

            if ($contador > 0) {
                return response()->json(['valido' => true]);
            }
        }

        return response()->json([
            'valido' => false,
            'razon'  => 'sin_combinacion',
            'duracion_total' => $services->sum('duration'),
        ]);
    }
    public function cancelFromPanel(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $user     = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        $appointment = Appointment::whereIn('customer_id', $customerIds)
            ->where('id', $id)
            ->first();

        if (!$appointment) {
            return response()->json(['success' => false, 'message' => 'Cita no encontrada.'], 404);
        }

        if (!$appointment->isCancellable()) {
            return response()->json(['success' => false, 'message' => 'Esta cita no se puede cancelar.'], 422);
        }

        if (now()->gt($appointment->start_time->subHours(2))) {
            return response()->json(['success' => false, 'message' => 'No puedes cancelar con menos de 2 horas de anticipación.'], 422);
        }

        $appointment->update([
            'status'               => 'cancelled',
            'cancelled_by'         => $user->id,
            'cancellation_reason'  => 'Cancelada por el cliente desde el panel.',
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

        return response()->json(['success' => true, 'message' => 'Cita cancelada correctamente.']);
    }
    public function voucher(int $id)
    {
        $user        = auth()->user();
        $appointment = Appointment::with(['customer', 'user', 'company', 'services'])
            ->findOrFail($id);

        $customerIds = Customer::where('user_id', $user->id)->pluck('id');
        $esCliente   = $customerIds->contains($appointment->customer_id);
        $esAdmin     = $user->hasRole('admin') && $user->companies()->where('companies.id', $appointment->company_id)->exists();
        $esEmpleado  = $user->hasRole('empleado') && $appointment->user_id === $user->id;

        abort_unless($esCliente || $esAdmin || $esEmpleado, 403);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.appointment-voucher', [
            'appt'        => $appointment,
            'generado_en' => now()->format('d/m/Y H:i'),
        ])->setPaper([0, 0, 400, 600], 'portrait');

        return $pdf->stream('comprobante-cita-' . $appointment->id . '.pdf');
    }
}
