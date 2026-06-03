<?php

namespace App\Livewire\Appointments;

use App\Models\Appointment;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Service;
use App\Models\TypeCompany;
use App\Models\User;
use App\Traits\BookingChainTrait;
use App\Mail\AppointmentConfirmationMail;
use App\Mail\AppointmentAdminNotificationMail;
use App\Mail\AppointmentCancelledAdminMail;
use App\Models\NotificationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class AppointmentCreate extends Component
{
    use BookingChainTrait;

    // ── Empresa ────────────────────────────────────────────────────────
    public ?int    $companyId       = null;
    public ?array  $companyData     = null; // {id, name, address, phone, logo}

    // ── Servicios ──────────────────────────────────────────────────────
    public array   $services        = [];   // lista cargada de la empresa
    public array   $selectedServices = [];  // ids seleccionados
    public bool    $combinacionValida = true;
    public ?string $combinacionError  = null;

    // ── Calendario / slot ──────────────────────────────────────────────
    public ?string $fecha           = null;
    public ?string $hora            = null;
    public ?string $horaFin         = null; // calculado tras seleccionar slot
    public string  $horaInicio      = '08:00';
    public string  $horaFinEmpresa  = '20:00';

    // ── Profesionales ──────────────────────────────────────────────────
    // [service_id => [ [id, name, image], ... ] ]
    public array   $profesionalesPorServicio = [];
    // [service_id => user_id]
    public array   $selectedProfesionales   = [];

    // ── Extras ────────────────────────────────────────────────────────
    public string  $notas           = '';
    public array   $proximas        = [];   // citas próximas del cliente

    // ── UI state ──────────────────────────────────────────────────────
    public bool    $loadingServicios     = false;
    public bool    $loadingProfesionales = false;
    public bool    $confirmando          = false;
    public ?string $successMsg           = null;
    public ?string $errorMsg             = null;

    // ────────────────────────────────────────────────────────────────────
    // MOUNT
    // ────────────────────────────────────────────────────────────────────
    public function mount(?int $companyId = null): void
    {
        $this->cargarProximas();

        if ($companyId) {
            $this->cargarEmpresa($companyId);
        }
    }

    // ────────────────────────────────────────────────────────────────────
    // EMPRESA
    // ────────────────────────────────────────────────────────────────────
    public function seleccionarEmpresa(int $companyId): void
    {
        $this->resetExceptoEmpresaYProximas();
        $this->cargarEmpresa($companyId);
    }

    private function cargarEmpresa(int $companyId): void
    {
        $company = Company::find($companyId);
        if (!$company) return;

        $this->companyId   = $company->id;
        $this->companyData = [
            'id'      => $company->id,
            'name'    => $company->name,
            'address' => $company->address ?? '',
            'phone'   => $company->phone ?? '',
            'logo'    => $company->logo ? asset('storage/' . $company->logo) : null,
        ];

        $this->cargarServicios();
    }

    // ────────────────────────────────────────────────────────────────────
    // SERVICIOS
    // ────────────────────────────────────────────────────────────────────
    private function cargarServicios(): void
    {
        if (!$this->companyId) return;

        $company = Company::find($this->companyId);

        $services = $company->services()
            ->whereHas(
                'users',
                fn($q) =>
                $q->whereHas('roles', fn($r) => $r->where('name', 'empleado'))
                    ->whereHas('professionalAvailabilities')
            )
            ->get(['id', 'name', 'description', 'duration', 'price', 'image']);

        $this->services = $services->map(fn($s) => [
            'id'          => $s->id,
            'name'        => $s->name,
            'description' => $s->description,
            'duration'    => $s->duration,
            'price'       => $s->price,
            'image'       => $s->image ? asset('storage/' . $s->image) : null,
        ])->toArray();

        // Horario de la empresa
        $avail = DB::table('professional_availabilities')
            ->whereIn('user_id', $company->users()->pluck('users.id'))
            ->selectRaw('MIN(start_time) as hora_inicio, MAX(end_time) as hora_fin')
            ->first();

        $this->horaInicio     = $avail?->hora_inicio
            ? Carbon::parse($avail->hora_inicio)->format('H:i')
            : '08:00';
        $this->horaFinEmpresa = $avail?->hora_fin
            ? Carbon::parse($avail->hora_fin)->format('H:i')
            : '20:00';

        // Notificar al JS para reinicializar el calendario
        $this->dispatch('empresa-cargada', [
            'horaInicio'  => $this->horaInicio,
            'horaFin'     => $this->horaFinEmpresa,
            'companyId'   => $this->companyId,
            'serviceIds'  => [],
        ]);
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

        // Reset slot y profesionales al cambiar servicios
        $this->fecha                   = null;
        $this->hora                    = null;
        $this->horaFin                 = null;
        $this->profesionalesPorServicio = [];
        $this->selectedProfesionales   = [];

        $this->validarCombinacion();

        if ($this->combinacionValida && !empty($this->selectedServices)) {
            $this->dispatch('servicios-actualizados', [
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

        $diasSemana = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($diasSemana as $dia) {
            $horasInicio = $profesionales
                ->flatMap->professionalAvailabilities
                ->where('day_of_week', $dia)
                ->pluck('start_time')
                ->filter();

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
    // SLOT (llamado desde JS via $wire.call)
    // ────────────────────────────────────────────────────────────────────
    public function seleccionarSlot(string $fecha, string $hora): void
    {
        $this->fecha                  = $fecha;
        $this->hora                   = $hora;
        $this->selectedProfesionales  = [];
        $this->profesionalesPorServicio = [];
        $this->errorMsg               = null;

        $this->cargarProfesionales();
    }

    private function cargarProfesionales(): void
    {
        if (!$this->fecha || !$this->hora || empty($this->selectedServices)) return;

        $this->loadingProfesionales = true;

        $services    = Service::whereIn('id', $this->selectedServices)->orderBy('id')->get();
        $dayOfWeek   = Carbon::parse("{$this->fecha} 12:00:00")->format('l');
        $cursorInicio = Carbon::parse("{$this->fecha} {$this->hora}:00");

        $resultado = [];
        $asignados = [];

        foreach ($services as $service) {
            $fin = $cursorInicio->copy()->addMinutes($service->duration);

            $profesionales = User::whereHas('companies', fn($q) =>
            $q->where('companies.id', $this->companyId))
                ->whereHas('services', fn($q) =>
                $q->where('services.id', $service->id))
                ->whereHas('roles', fn($q) => $q->where('name', 'empleado'))
                ->whereHas('professionalAvailabilities', fn($q) =>
                $q->where('day_of_week', $dayOfWeek)
                    ->where('start_time', '<=', $cursorInicio->format('H:i:s'))
                    ->where('end_time',   '>=', $fin->format('H:i:s')))
                ->whereDoesntHave('appointments', fn($q) =>
                $q->where('start_time', '<', $fin)
                    ->where('end_time',   '>', $cursorInicio)
                    ->whereNotIn('status', ['cancelled']))
                ->whereNotIn('id', $asignados)
                ->get(['id', 'name', 'image']);

            $resultado[$service->id] = [
                'service'       => [
                    'id'       => $service->id,
                    'name'     => $service->name,
                    'duration' => $service->duration,
                ],
                'hora_inicio'   => $cursorInicio->format('H:i'),
                'hora_fin'      => $fin->format('H:i'),
                'profesionales' => $profesionales->map(fn($p) => [
                    'id'    => $p->id,
                    'name'  => $p->name,
                    'image' => $p->image ? asset('storage/' . $p->image) : null,
                ])->toArray(),
            ];

            // Preseleccionar si hay solo uno
            if ($profesionales->count() === 1) {
                $this->selectedProfesionales[$service->id] = $profesionales->first()->id;
                $asignados[] = $profesionales->first()->id;
            }

            $cursorInicio = $fin;
        }

        $this->horaFin                  = $cursorInicio->format('H:i');
        $this->profesionalesPorServicio = $resultado;
        $this->loadingProfesionales     = false;
    }

    public function seleccionarProfesional(int $serviceId, int $userId): void
    {
        $this->selectedProfesionales[$serviceId] = $userId;
    }

    // ────────────────────────────────────────────────────────────────────
    // CONFIRMAR CITA
    // ────────────────────────────────────────────────────────────────────
    public function confirmar(): void
    {
        $this->errorMsg   = null;
        $this->successMsg = null;

        // Validaciones básicas
        if (!$this->companyId || empty($this->selectedServices) || !$this->fecha || !$this->hora) {
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
                $user         = auth()->user();
                $customer     = Customer::firstOrCreate([
                    'user_id'    => $user->id,
                    'company_id' => $this->companyId,
                ]);

                $services    = Service::whereIn('id', $this->selectedServices)->orderBy('id')->get();
                $cursorInicio = Carbon::parse("{$this->fecha} {$this->hora}:00");
                $customerIds  = Customer::where('user_id', $user->id)->pluck('id');

                foreach ($services as $service) {
                    $inicio = $cursorInicio->copy();
                    $fin    = $inicio->copy()->addMinutes($service->duration);
                    $userId = $this->selectedProfesionales[$service->id];

                    if ($inicio->isPast()) {
                        throw new \Exception('hora_pasada');
                    }

                    // Conflicto cliente
                    $citaConflicto = Appointment::whereIn('customer_id', $customerIds)
                        ->where('start_time', '<', $fin)
                        ->where('end_time',   '>', $inicio)
                        ->whereNotIn('status', ['cancelled'])
                        ->lockForUpdate()
                        ->first();

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
                        ->lockForUpdate()
                        ->exists();

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

            $this->successMsg = '¡Cita agendada correctamente! Revisa tu correo para la confirmación.';
            $this->resetFormulario();
            $this->cargarProximas();
        } catch (\Exception $e) {
            $msg = $e->getPrevious()?->getMessage() ?? $e->getMessage();

            if (str_starts_with($msg, 'cliente_ocupado:')) {
                $detalle = substr($msg, strlen('cliente_ocupado:'));
                $this->errorMsg = "Ya tienes una cita el {$detalle}.";
            } else {
                $this->errorMsg = match ($msg) {
                    'slot_ocupado'    => 'Un horario fue reservado mientras confirmabas. Selecciona otro.',
                    'hora_pasada'     => 'No puedes agendar en horarios que ya pasaron.',
                    'datos_invalidos' => 'Los datos enviados no son válidos.',
                    default           => 'Ocurrió un error al agendar. Intenta de nuevo.',
                };
            }
        } finally {
            $this->confirmando = false;
        }
    }

    // ────────────────────────────────────────────────────────────────────
    // CANCELAR CITA PRÓXIMA
    // ────────────────────────────────────────────────────────────────────
    public function cancelarCita(int $appointmentId): void
    {
        $user        = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        $appointment = Appointment::whereIn('customer_id', $customerIds)
            ->where('id', $appointmentId)
            ->first();

        if (!$appointment || !$appointment->isCancellable()) {
            $this->dispatch('cita-error', id: $appointmentId, msg: 'Esta cita no se puede cancelar.');
            return;
        }

        if (now()->gt($appointment->start_time->subHours(2))) {
            $this->dispatch('cita-error', id: $appointmentId, msg: 'No puedes cancelar con menos de 2 horas de anticipación.');
            return;
        }

        $appointment->update([
            'status'               => 'cancelled',
            'cancelled_by'         => $user->id,
            'cancellation_reason'  => 'Cancelada por el cliente desde el panel.',
        ]);

        $appointment->load(['customer', 'user', 'company', 'services']);
        if ($appointment->company->email) {
            $this->enviarEmail(
                new AppointmentCancelledAdminMail($appointment),
                $appointment->company->email,
                $appointment->id,
                'cancelled_admin'
            );
        }

        // Remover de la lista reactivamente
        $this->proximas = array_values(
            array_filter($this->proximas, fn($c) => $c['id'] !== $appointmentId)
        );

        $this->dispatch('cita-cancelada', id: $appointmentId);
    }

    // ────────────────────────────────────────────────────────────────────
    // HELPERS PRIVADOS
    // ────────────────────────────────────────────────────────────────────
    private function cargarProximas(): void
    {
        $user        = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');

        $this->proximas = Appointment::whereIn('customer_id', $customerIds)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('start_time', '>=', now())
            ->with(['services', 'user', 'company'])
            ->orderBy('start_time')
            ->take(4)
            ->get()
            ->map(fn($a) => [
                'id'          => $a->id,
                'company'     => $a->company->name,
                'profesional' => $a->user->name,
                'servicios'   => $a->services->pluck('name')->join(', '),
                'fecha'       => Carbon::parse($a->start_time)->locale('es')->isoFormat('ddd D MMM'),
                'hora'        => Carbon::parse($a->start_time)->format('h:i A'),
                'hora_fin'    => Carbon::parse($a->end_time)->format('h:i A'),
                'status'      => $a->status,
                'cancellable' => in_array($a->status, ['confirmed', 'pending'])
                    && now()->lt($a->start_time->subHours(2)),
                'direccion'   => $a->company->address ?? '',
                'telefono'    => $a->company->phone ?? '',
            ])->toArray();
    }

    private function resetFormulario(): void
    {
        $this->selectedServices         = [];
        $this->fecha                    = null;
        $this->hora                     = null;
        $this->horaFin                  = null;
        $this->profesionalesPorServicio = [];
        $this->selectedProfesionales    = [];
        $this->notas                    = '';
        $this->combinacionValida        = true;
        $this->combinacionError         = null;

        $this->dispatch('reset-calendario');
    }

    private function resetExceptoEmpresaYProximas(): void
    {
        $this->services                 = [];
        $this->selectedServices         = [];
        $this->fecha                    = null;
        $this->hora                     = null;
        $this->horaFin                  = null;
        $this->profesionalesPorServicio = [];
        $this->selectedProfesionales    = [];
        $this->notas                    = '';
        $this->combinacionValida        = true;
        $this->combinacionError         = null;
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

    // ────────────────────────────────────────────────────────────────────
    // RENDER
    // ────────────────────────────────────────────────────────────────────
    public function render()
    {
        $user = auth()->user();

        if ($user->isPremium()) {
            $tiposNegocio = TypeCompany::with(['companies' => function ($q) {
                $q->whereHas(
                    'services',
                    fn($s) =>
                    $s->whereHas(
                        'users',
                        fn($u) =>
                        $u->whereHas('roles', fn($r) => $r->where('name', 'empleado'))
                            ->whereHas('professionalAvailabilities')
                    )
                );
            }])->get()->filter(fn($t) => $t->companies->isNotEmpty())->values();
        } else {
            $companyIds = $user->companies()->pluck('companies.id');
            $tiposNegocio = TypeCompany::with(['companies' => function ($q) use ($companyIds) {
                $q->whereIn('companies.id', $companyIds)
                    ->whereHas(
                        'services',
                        fn($s) =>
                        $s->whereHas(
                            'users',
                            fn($u) =>
                            $u->whereHas('roles', fn($r) => $r->where('name', 'empleado'))
                                ->whereHas('professionalAvailabilities')
                        )
                    );
            }])->get()->filter(fn($t) => $t->companies->isNotEmpty())->values();
        }

        return view('livewire.appointments.appointment-create', [
            'tiposNegocio' => $tiposNegocio,
        ]);
    }
}
