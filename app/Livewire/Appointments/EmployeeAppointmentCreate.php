<?php

namespace App\Livewire\Appointments;

use App\Models\Appointment;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use App\Traits\BookingChainTrait;
use App\Mail\AppointmentConfirmationMail;
use App\Mail\AppointmentAdminNotificationMail;
use App\Models\NotificationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\On;

class EmployeeAppointmentCreate extends Component
{
    use BookingChainTrait;

    // ── Empresa (fija desde sesión) ────────────────────────────────────
    public ?int   $companyId   = null;
    public ?array $companyData = null;

    // ── Cliente ────────────────────────────────────────────────────────
    public string  $clienteSearch    = '';
    public array   $clienteResultados = [];
    public ?int    $clienteId        = null;  // Customer->id
    public ?array  $clienteData      = null;  // {id, name, email, phone}
    public bool    $buscandoCliente  = false;

    // ── Servicios ──────────────────────────────────────────────────────
    public array $services         = [];
    public array $selectedServices = [];
    public bool  $combinacionValida = true;
    public ?string $combinacionError = null;

    // ── Calendario / slot ──────────────────────────────────────────────
    public ?string $fecha          = null;
    public ?string $hora           = null;
    public ?string $horaFin        = null;
    public string  $horaInicio     = '08:00';
    public string  $horaFinEmpresa = '20:00';

    // ── Profesionales ──────────────────────────────────────────────────
    public array $profesionalesPorServicio = [];
    public array $selectedProfesionales   = [];
    public bool  $loadingProfesionales    = false;
    public bool  $esEmpleado              = false;
    public ?int  $empleadoUserId          = null;

    // ── Extras ────────────────────────────────────────────────────────
    public string $notas = '';

    // ── Citas próximas del cliente seleccionado ────────────────────────
    public array $proximasCita = [];

    // ── UI ─────────────────────────────────────────────────────────────
    public ?string $successMsg = null;
    public ?string $errorMsg   = null;
    public bool    $confirmando = false;

    // ────────────────────────────────────────────────────────────────────
    // MOUNT
    // ────────────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $companyId = session('active_company_id');
        $company   = Company::find($companyId);

        if (!$company) return;

        $this->companyId   = $company->id;
        $this->companyData = [
            'id'      => $company->id,
            'name'    => $company->name,
            'address' => $company->address ?? '',
            'phone'   => $company->phone ?? '',
            'logo'    => $company->logo ? asset('storage/' . $company->logo) : null,
        ];

        // PRIMERO setear rol, LUEGO cargar servicios
        $user = auth()->user();
        $this->esEmpleado     = $user->hasRole('empleado');
        $this->empleadoUserId = $this->esEmpleado ? $user->id : null;

        $this->cargarServicios();
    }

    // ────────────────────────────────────────────────────────────────────
    // CLIENTE — búsqueda en tiempo real
    // ────────────────────────────────────────────────────────────────────
    public function updatedClienteSearch(): void
    {
        $q = trim($this->clienteSearch);

        if (strlen($q) < 2) {
            $this->clienteResultados = [];
            return;
        }

        // Buscar usuarios con rol cliente que tengan Customer en esta empresa
        $userIds = Customer::where('company_id', $this->companyId)->pluck('user_id');

        $this->clienteResultados = User::role('cliente')
            ->whereIn('id', $userIds)
            ->where(
                fn($u) =>
                $u->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
            )
            ->limit(100)
            ->get(['id', 'name', 'email', 'phone'])
            ->map(fn($u) => [
                'user_id' => $u->id,
                'name'    => $u->name,
                'email'   => $u->email,
                'phone'   => $u->phone ?? '',
            ])
            ->toArray();
    }

    public function seleccionarCliente(int $userId): void
    {
        $user     = User::find($userId);
        $customer = Customer::where('user_id', $userId)
            ->where('company_id', $this->companyId)
            ->first();

        if (!$user || !$customer) return;

        $this->clienteId   = $customer->id;
        $this->clienteData = [
            'id'    => $customer->id,
            'name'  => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
        ];

        $this->clienteSearch     = '';
        $this->clienteResultados = [];

        $this->cargarProximasCliente();
    }

    public function limpiarCliente(): void
    {
        $this->clienteId         = null;
        $this->clienteData       = null;
        $this->clienteSearch     = '';
        $this->clienteResultados = [];
        $this->proximasCita      = [];
        $this->fecha             = null;
        $this->hora              = null;
        $this->horaFin           = null;
        $this->profesionalesPorServicio = [];
        $this->selectedProfesionales    = [];
        $this->dispatch('emp-reiniciar-calendario');  // evento nuevo
    }

    private function cargarProximasCliente(): void
    {
        if (!$this->clienteId) return;

        $this->proximasCita = Appointment::where('customer_id', $this->clienteId)
            ->where('company_id', $this->companyId)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('start_time', '>=', now())
            ->with(['services', 'user'])
            ->orderBy('start_time')
            ->take(5)
            ->get()
            ->map(fn($a) => [
                'id'          => $a->id,
                'profesional' => $a->user->name,
                'servicios'   => $a->services->pluck('name')->join(', '),
                'fecha'       => Carbon::parse($a->start_time)->locale('es')->isoFormat('ddd D MMM'),
                'hora'        => Carbon::parse($a->start_time)->format('h:i A'),
                'status'      => $a->status,
            ])
            ->toArray();
    }

    // ────────────────────────────────────────────────────────────────────
    // SERVICIOS
    // ────────────────────────────────────────────────────────────────────
    private function cargarServicios(): void
    {
        if (!$this->companyId) return;

        $company = Company::find($this->companyId);

        $query = $company->services()
            ->whereHas(
                'users',
                fn($q) =>
                $q->whereHas('roles', fn($r) => $r->where('name', 'empleado'))
                    ->whereHas('professionalAvailabilities')
            );

        // Si es empleado, filtrar solo sus servicios
        if ($this->esEmpleado && $this->empleadoUserId) {
            $query->whereHas(
                'users',
                fn($q) =>
                $q->where('users.id', $this->empleadoUserId)
            );
        }

        $services = $query->get(['id', 'name', 'description', 'duration', 'price', 'image']);

        $this->services = $services->map(fn($s) => [
            'id'          => $s->id,
            'name'        => $s->name,
            'description' => $s->description,
            'duration'    => $s->duration,
            'price'       => $s->price,
            'image'       => $s->image ? asset('storage/' . $s->image) : null,
        ])->toArray();

        // Horarios: si es empleado, solo sus disponibilidades
        $availQuery = DB::table('professional_availabilities');

        if ($this->esEmpleado && $this->empleadoUserId) {
            $availQuery->where('user_id', $this->empleadoUserId);
        } else {
            $availQuery->whereIn('user_id', $company->users()->pluck('users.id'));
        }

        $avail = $availQuery->selectRaw('MIN(start_time) as hora_inicio, MAX(end_time) as hora_fin')->first();

        $this->horaInicio     = $avail?->hora_inicio ? Carbon::parse($avail->hora_inicio)->format('H:i') : '08:00';
        $this->horaFinEmpresa = $avail?->hora_fin    ? Carbon::parse($avail->hora_fin)->format('H:i')    : '20:00';
    }

    public function toggleServicio(int $serviceId): void
    {
        if (in_array($serviceId, $this->selectedServices)) {
            $this->selectedServices = array_values(
                array_filter($this->selectedServices, fn($id) => $id !== $serviceId)
            );
        } else {
            $this->selectedServices[] = $serviceId;
        }

        $this->resetSlotYProfesionales();
        $this->validarCombinacion();

        if ($this->combinacionValida && !empty($this->selectedServices)) {
            $this->dispatch('emp-servicios-actualizados', [
                'serviceIds' => $this->selectedServices,
                'companyId'  => $this->companyId,
            ]);
        }
    }

    private function validarCombinacion(): void
    {
        if (count($this->selectedServices) <= 1) {
            $this->combinacionValida = true;
            $this->combinacionError  = null;
            return;
        }

        $services = Service::whereIn('id', $this->selectedServices)->orderBy('id')->get();

        $profesionales = User::whereHas('companies', fn($q) =>
        $q->where('companies.id', $this->companyId))
            ->whereHas('services', fn($q) =>
            $q->whereIn('services.id', $this->selectedServices))
            ->whereHas('roles', fn($q) => $q->where('name', 'empleado'))
            ->with(['professionalAvailabilities', 'services'])
            ->get();

        if ($profesionales->isEmpty()) {
            $this->combinacionValida = false;
            $this->combinacionError  = 'Ningún profesional puede atender esta combinación.';
            return;
        }

        foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $dia) {
            $horasInicio = $profesionales->flatMap->professionalAvailabilities
                ->where('day_of_week', $dia)->pluck('start_time')->filter();

            if ($horasInicio->isEmpty()) continue;

            $slotInicio = Carbon::parse('2025-01-06 ' . $horasInicio->min());
            $contador   = 0;
            $this->contarCadenas(0, $services, $profesionales, collect(), $slotInicio, $dia, [], $contador);

            if ($contador > 0) {
                $this->combinacionValida = true;
                $this->combinacionError  = null;
                return;
            }
        }

        $this->combinacionValida = false;
        $this->combinacionError  = 'La combinación seleccionada no puede ser atendida. Intenta con menos servicios.';
    }

    // ────────────────────────────────────────────────────────────────────
    // SLOT
    // ────────────────────────────────────────────────────────────────────
    public function seleccionarSlot(string $fecha, string $hora): void
    {
        $this->fecha    = $fecha;
        $this->hora     = $hora;
        $this->errorMsg = null;
        $this->selectedProfesionales   = [];
        $this->profesionalesPorServicio = [];

        // Eliminar toda la validación previa — el calendario ya la hizo
        $this->cargarProfesionales();
    }

    private function cargarProfesionales(): void
    {
        if (!$this->fecha || !$this->hora || empty($this->selectedServices)) return;

        $this->loadingProfesionales = true;

        $services     = Service::whereIn('id', $this->selectedServices)->orderBy('id')->get();
        $dayOfWeek    = Carbon::parse("{$this->fecha} 12:00:00")->format('l');
        $cursorInicio = Carbon::parse("{$this->fecha} {$this->hora}:00");

        if ($this->esEmpleado) {
            // Caso simple: el único "profesional" posible es el propio empleado.
            // No hay combinaciones que explorar, solo confirmar que puede
            // atender los servicios en secuencia.
            $profesionales = User::where('id', $this->empleadoUserId)
                ->whereHas('companies', fn($q) =>
                $q->where('companies.id', $this->companyId))
                ->whereHas('services', fn($q) =>
                $q->whereIn('services.id', $this->selectedServices))
                ->with(['professionalAvailabilities', 'services'])
                ->get(['id', 'name', 'image']);
        } else {
            // Caso admin: pool completo de staff calificado; requiere backtracking
            // real para no bloquear servicios posteriores por asignar mal los primeros.
            $profesionales = User::whereHas('companies', fn($q) =>
            $q->where('companies.id', $this->companyId))
                ->whereHas('services', fn($q) =>
                $q->whereIn('services.id', $this->selectedServices))
                ->whereHas('roles', fn($q) => $q->where('name', 'empleado'))
                ->with(['professionalAvailabilities', 'services'])
                ->get(['id', 'name', 'image']);
        }

        $citasReales = Appointment::whereIn('user_id', $profesionales->pluck('id'))
            ->whereDate('start_time', $this->fecha)
            ->whereNotIn('status', ['cancelled'])
            ->get(['id', 'user_id', 'start_time', 'end_time']);

        $asignacion = $this->construirAsignacion(
            $services,
            $profesionales,
            $citasReales,
            $cursorInicio,
            $dayOfWeek
        );

        $resultado = [];
        foreach ($asignacion as $serviceId => $datos) {
            $resultado[$serviceId] = [
                'service'       => $datos['service'],
                'hora_inicio'   => $datos['hora_inicio'],
                'hora_fin'      => $datos['hora_fin'],
                'profesionales' => $datos['profesionales']->map(fn($p) => [
                    'id'    => $p->id,
                    'name'  => $p->name,
                    'image' => $p->image ? asset('storage/' . $p->image) : null,
                ])->toArray(),
            ];

            // Para el empleado, auto-asignar en cuanto haya 1 candidato (igual
            // que antes); para admin, auto_asignado ya viene resuelto por el
            // backtracking solo cuando es la única opción viable.
            $unicoCandidato = $datos['profesionales']->count() === 1
                ? $datos['profesionales']->first()->id
                : null;

            $autoAsignar = $this->esEmpleado
                ? ($unicoCandidato ?? $datos['auto_asignado'])
                : $datos['auto_asignado'];

            if ($autoAsignar) {
                $this->selectedProfesionales[$serviceId] = $autoAsignar;
            }
        }

        $ultimoServicio = $services->last();
        $this->horaFin = $ultimoServicio
            ? ($resultado[$ultimoServicio->id]['hora_fin'] ?? $cursorInicio->format('H:i'))
            : $cursorInicio->format('H:i');
        $this->profesionalesPorServicio = $resultado;
        $this->loadingProfesionales     = false;
    }

    public function seleccionarProfesional(int $serviceId, int $userId): void
    {
        $this->selectedProfesionales[$serviceId] = $userId;
    }

    // ────────────────────────────────────────────────────────────────────
    // CONFIRMAR
    // ────────────────────────────────────────────────────────────────────
    public function confirmar(): void
    {
        $this->errorMsg   = null;
        $this->successMsg = null;

        if (!$this->clienteId) {
            $this->errorMsg = 'Selecciona un cliente antes de confirmar.';
            return;
        }

        if (empty($this->selectedServices) || !$this->fecha || !$this->hora) {
            $this->errorMsg = 'Completa todos los campos antes de confirmar.';
            return;
        }

        foreach ($this->selectedServices as $serviceId) {
            if (empty($this->selectedProfesionales[$serviceId])) {
                $this->errorMsg = 'Selecciona un profesional para cada servicio.';
                return;
            }
        }

        $this->confirmando = true;

        try {
            DB::transaction(function () {
                $bookingGroup = Str::uuid();
                $customer     = Customer::findOrFail($this->clienteId);
                $services     = Service::whereIn('id', $this->selectedServices)->orderBy('id')->get();
                $cursorInicio = Carbon::parse("{$this->fecha} {$this->hora}:00");

                // IDs de todos los customers del mismo usuario (para detectar conflictos)
                $customerIds = Customer::where('user_id', $customer->user_id)->pluck('id');

                foreach ($services as $service) {
                    $inicio = $cursorInicio->copy();
                    $fin    = $inicio->copy()->addMinutes($service->duration);
                    $userId = $this->selectedProfesionales[$service->id];

                    if ($inicio->isPast()) throw new \Exception('hora_pasada');

                    // Validar que el profesional pertenece a la empresa en sesión
                    $profesionalValido = User::where('id', $userId)
                        ->whereHas('companies', fn($q) => $q->where('companies.id', $this->companyId))
                        ->whereHas('roles', fn($q) => $q->where('name', 'empleado'))
                        ->exists();

                    if (!$profesionalValido) throw new \Exception('datos_invalidos');

                    // Conflicto cliente
                    $citaConflicto = Appointment::whereIn('customer_id', $customerIds)
                        ->where('start_time', '<', $fin)
                        ->where('end_time',   '>', $inicio)
                        ->whereNotIn('status', ['cancelled'])
                        ->lockForUpdate()->first();

                    if ($citaConflicto) {
                        $fechaF = Carbon::parse($citaConflicto->start_time)->locale('es')->isoFormat('dddd D [de] MMMM');
                        $horaF  = Carbon::parse($citaConflicto->start_time)->format('h:i A');
                        throw new \Exception("cliente_ocupado:{$fechaF} a las {$horaF}");
                    }

                    // Conflicto profesional
                    $conflicto = Appointment::where('user_id', $userId)
                        ->where('start_time', '<', $fin)
                        ->where('end_time',   '>', $inicio)
                        ->whereNotIn('status', ['cancelled'])
                        ->lockForUpdate()->exists();

                    if ($conflicto) throw new \Exception('slot_ocupado');

                    $appointment = Appointment::create([
                        'start_time'              => $inicio,
                        'end_time'                => $fin,
                        'customer_id'             => $customer->id,
                        'user_id'                 => $userId,
                        'company_id'              => $this->companyId,
                        'notes'                   => $this->notas,
                        'booking_group'           => $bookingGroup,
                        'status'                  => 'confirmed',
                        'cancel_token'            => Str::random(40),
                        'cancel_token_expires_at' => now()->addDays(7),
                    ]);

                    $appointment->services()->attach($service->id);
                    $appointment->load(['customer', 'user', 'company', 'services']);

                    $this->enviarEmail(
                        new AppointmentConfirmationMail($appointment),
                        $appointment->customer->email,
                        $appointment->id,
                        'confirmation'
                    );

                    if ($appointment->company->email) {
                        $this->enviarEmail(
                            new AppointmentAdminNotificationMail($appointment),
                            $appointment->company->email,
                            $appointment->id,
                            'admin_notification'
                        );
                    }

                    $cursorInicio = $fin;
                }
            });

            $this->successMsg = '¡Cita agendada correctamente!';
            $this->resetFormulario();
            $this->cargarProximasCliente();
        } catch (\Exception $e) {
            $msg = $e->getPrevious()?->getMessage() ?? $e->getMessage();

            if (str_starts_with($msg, 'cliente_ocupado:')) {
                $detalle = substr($msg, strlen('cliente_ocupado:'));
                $this->errorMsg = "El cliente ya tiene una cita el {$detalle}.";
            } else {
                $this->errorMsg = match ($msg) {
                    'slot_ocupado'    => 'Un horario fue reservado mientras confirmabas. Selecciona otro.',
                    'hora_pasada'     => 'No puedes agendar en horarios que ya pasaron.',
                    'datos_invalidos' => 'Profesional no válido para esta empresa.',
                    default           => 'Ocurrió un error al agendar. Intenta de nuevo.',
                };
            }
        } finally {
            $this->confirmando = false;
        }
    }

    // ────────────────────────────────────────────────────────────────────
    // HELPERS
    // ────────────────────────────────────────────────────────────────────
    private function resetSlotYProfesionales(): void
    {
        $this->fecha                    = null;
        $this->hora                     = null;
        $this->horaFin                  = null;
        $this->profesionalesPorServicio = [];
        $this->selectedProfesionales    = [];
        $this->dispatch('emp-reset-calendario');
    }

    private function resetFormulario(): void
    {
        $this->selectedServices         = [];
        $this->notas                    = '';
        $this->combinacionValida        = true;
        $this->combinacionError         = null;
        $this->resetSlotYProfesionales();
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
    #[On('seleccionarClienteEvt')]
    public function seleccionarClienteDesdeEvento(int $userId): void
    {
        $this->seleccionarCliente($userId);
    }
    public function cargarTodosClientes(): void
    {
        $userIds = Customer::where('company_id', $this->companyId)->pluck('user_id');

        $this->clienteResultados = User::role('cliente')
            ->whereIn('id', $userIds)
            ->limit(100)
            ->get(['id', 'name', 'email', 'phone'])
            ->map(fn($u) => [
                'user_id' => $u->id,
                'name'    => $u->name,
                'email'   => $u->email,
                'phone'   => $u->phone ?? '',
            ])
            ->toArray();
    }
    public function cerrarBuscador(): void
    {
        $this->clienteResultados = [];
    }

    public function render()
    {
        return view('livewire.appointments.employee-appointment-create');
    }
}
