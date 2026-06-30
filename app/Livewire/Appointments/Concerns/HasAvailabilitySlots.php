<?php

namespace App\Livewire\Appointments\Concerns;

use App\Models\Appointment;
use App\Models\OpeningHour;
use App\Models\ProfessionalAvailability;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

trait HasAvailabilitySlots
{
    // ── Estado público ────────────────────────────────────────────────────────

    /** 'week' | 'month' */
    public string $availabilityView = 'week';

    /** Fecha base para calcular el rango (Y-m-d). */
    public string $availabilityBaseDate = '';

    /** Duración en minutos de cada slot. Se sincroniza desde el servicio seleccionado. */
    public int $slotMinutes = 30;


    // ── Boot ──────────────────────────────────────────────────────────────────

    public function bootHasAvailabilitySlots(): void
    {
        if (empty($this->availabilityBaseDate)) {
            $this->availabilityBaseDate = now()->toDateString();
        }
    }

    // ── Watchers ──────────────────────────────────────────────────────────────

    /**
     * Cuando el usuario elige un servicio, sincronizamos slotMinutes
     * con la duración definida en el modelo Service.
     */
    public function updatedFilterService($value): void
    {
        if (!empty($value)) {
            $service = Service::find($value);
            $this->slotMinutes = $service?->duration ?? 30;
        } else {
            $this->slotMinutes = 30;
        }
    }

    /**
     * Cuando cambia el profesional, resetear el servicio seleccionado
     * porque puede no pertenecer al nuevo profesional.
     */
    public function updatedFilterProfessional($value): void
    {
        $this->filterService = '';
        $this->slotMinutes   = 30;
        unset($this->availableServices);
    }

    // ── Navegación ────────────────────────────────────────────────────────────

    public function availabilityNext(): void
    {
        $this->availabilityBaseDate = Carbon::parse($this->availabilityBaseDate)
            ->add(1, $this->availabilityView === 'week' ? 'week' : 'month')
            ->toDateString();
    }

    public function availabilityPrev(): void
    {
        $this->availabilityBaseDate = Carbon::parse($this->availabilityBaseDate)
            ->sub(1, $this->availabilityView === 'week' ? 'week' : 'month')
            ->toDateString();
    }

    public function availabilityToday(): void
    {
        $this->availabilityBaseDate = now()->toDateString();
    }

    // ── Rango de fechas según la vista ───────────────────────────────────────

    protected function availabilityDateRange(): array
    {
        $base = Carbon::parse($this->availabilityBaseDate);

        if ($this->availabilityView === 'week') {
            return [
                'start' => $base->copy()->startOfWeek(),
                'end'   => $base->copy()->endOfWeek(),
            ];
        }

        return [
            'start' => $base->copy()->startOfMonth(),
            'end'   => $base->copy()->endOfMonth(),
        ];
    }

    // ── Horarios ──────────────────────────────────────────────────────────────

    protected function getOpeningHoursMap(): Collection
    {
        $companyId = session('active_company_id');

        return OpeningHour::query()
            ->where('company_id', $companyId)
            ->get()
            ->keyBy('day_of_week');
    }

    /**
     * Devuelve el mapa de disponibilidad del profesional activo (si aplica).
     * Retorna null si no hay profesional específico (admin sin filtro).
     */
    protected function getProfessionalAvailabilityMap(): ?Collection
    {
        $userId = null;

        if ($this->isAdmin && !empty($this->filterProfessional)) {
            $userId = $this->filterProfessional;
        } elseif (!$this->isAdmin) {
            $userId = auth()->id();
        }

        if (!$userId) {
            return null;
        }

        return ProfessionalAvailability::query()
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->get()
            ->keyBy('day_of_week');
    }

    /**
     * Prioridad de horario:
     *   1. ProfessionalAvailability del profesional para ese día
     *   2. OpeningHour de la empresa para ese día
     *   3. null → día no laboral
     */
    protected function getWorkingHours(Carbon $date, ?Collection $hoursMap = null, ?Collection $proMap = null): ?array
    {
        $dayOfWeek = $date->format('l'); // 'Monday', 'Tuesday'…

        // 1. Horario propio del profesional
        if ($proMap && $proMap->has($dayOfWeek)) {
            $record = $proMap->get($dayOfWeek);
            return [
                'start'  => substr($record->start_time, 0, 5),
                'end'    => substr($record->end_time,   0, 5),
                'source' => 'professional',
            ];
        }

        // 2. Horario de la empresa como fallback
        $hoursMap ??= $this->getOpeningHoursMap();
        $record = $hoursMap->get($dayOfWeek);

        if (!$record) {
            return null;
        }

        return [
            'start'  => substr($record->start_time, 0, 5),
            'end'    => substr($record->end_time,   0, 5),
            'source' => 'company',
        ];
    }

    // ── Servicios de la empresa ───────────────────────────────────────────────

    /**
     * Lista de servicios filtrada según el contexto:
     * - Admin con profesional seleccionado → servicios de ese profesional
     * - Admin sin profesional              → todos los servicios de la empresa
     * - Profesional no-admin               → solo sus propios servicios
     */
    #[Computed]
    public function availableServices(): Collection
    {
        $companyId = session('active_company_id');

        $base = Service::query()
            ->where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->orderBy('name');

        if ($this->isAdmin && !empty($this->filterProfessional)) {
            // Servicios del profesional seleccionado
            $base->whereHas('users', fn($q) => $q->where('users.id', $this->filterProfessional));
        } elseif (!$this->isAdmin) {
            // Profesional no-admin → solo los suyos
            $base->whereHas('users', fn($q) => $q->where('users.id', auth()->id()));
        }
        // Admin sin filtro → todos los de la empresa (sin whereHas adicional)

        return $base->get(['id', 'name', 'duration']);
    }

    /**
     * Devuelve el servicio actualmente seleccionado (o null).
     */
    #[Computed]
    public function selectedService(): ?Service
    {
        if (empty($this->filterService)) {
            return null;
        }

        return Service::find($this->filterService);
    }

    // ── Consulta de citas del rango ───────────────────────────────────────────

    protected function appointmentsInRange(Carbon $from, Carbon $to): Collection
    {
        return $this->scopeCompany(
            Appointment::query()
                ->select(['id', 'user_id', 'start_time', 'end_time', 'status'])
        )
            ->whereIn('status', ['confirmed'])
            ->whereBetween('start_time', [
                $from->copy()->startOfDay(),
                $to->copy()->endOfDay(),
            ])
            // Filtro por profesional (admin con filterProfessional)
            ->when(
                $this->isAdmin && !empty($this->filterProfessional),
                fn($q) => $q->where('user_id', $this->filterProfessional)
            )
            // Profesional no-admin solo ve sus propias citas
            ->when(
                !$this->isAdmin,
                fn($q) => $q->where('user_id', auth()->id())
            )
            // ── NO filtramos por servicio aquí ────────────────────────────────
            // El profesional está ocupado aunque la cita sea de otro servicio.
            // filterService solo afecta slotMinutes (duración del slot).
            // ─────────────────────────────────────────────────────────────────
            ->get();
    }

    // ── Cálculo principal ─────────────────────────────────────────────────────

    #[Computed]
    public function availabilityDays(): array
    {
        ['start' => $from, 'end' => $to] = $this->availabilityDateRange();

        $appointments = $this->appointmentsInRange($from, $to);
        $hoursMap     = $this->getOpeningHoursMap();
        $proMap       = $this->getProfessionalAvailabilityMap();
        $days         = [];

        foreach (CarbonPeriod::create($from, $to) as $day) {
            $hours = $this->getWorkingHours($day, $hoursMap, $proMap);

            if (is_null($hours)) {
                $days[] = [
                    'date'           => $day->toDateString(),
                    'label'          => ucfirst($day->locale('es')->isoFormat('ddd D')),
                    'is_working_day' => false,
                    'total_slots'    => 0,
                    'free_slots'     => 0,
                    'busy_slots'     => 0,
                    'occupancy_pct'  => 0,
                    'status'         => 'closed',
                    'slots'          => [],
                ];
                continue;
            }

            // Generar todos los slots del día
            $slots    = [];
            $cursor   = Carbon::parse($day->toDateString() . ' ' . $hours['start']);
            $dayEnd   = Carbon::parse($day->toDateString() . ' ' . $hours['end']);
            $dayAppts = $appointments->filter(
                fn($a) => Carbon::parse($a->start_time)->toDateString() === $day->toDateString()
            );

            while ($cursor->copy()->addMinutes($this->slotMinutes)->lte($dayEnd)) {
                $slotEnd = $cursor->copy()->addMinutes($this->slotMinutes);

                $busy = $dayAppts->contains(function ($appt) use ($cursor, $slotEnd) {
                    $apptStart = Carbon::parse($appt->start_time);
                    $apptEnd   = Carbon::parse($appt->end_time);

                    return $cursor->lt($apptEnd) && $slotEnd->gt($apptStart);
                });

                $slots[] = [
                    'time'     => $cursor->format('H:i'),
                    'time_end' => $slotEnd->format('H:i'),
                    'free'     => !$busy,
                ];

                $cursor->addMinutes($this->slotMinutes);
            }

            $total = count($slots);
            $free  = collect($slots)->where('free', true)->count();
            $busy  = $total - $free;
            $pct   = $total > 0 ? (int) round(($busy / $total) * 100) : 0;

            $status = match (true) {
                $total === 0 => 'closed',
                $free  === 0 => 'full',
                $pct   >= 60 => 'partial',
                default      => 'available',
            };

            $days[] = [
                'date'           => $day->toDateString(),
                'label'          => ucfirst($day->locale('es')->isoFormat('ddd D')),
                'is_working_day' => true,
                'total_slots'    => $total,
                'free_slots'     => $free,
                'busy_slots'     => $busy,
                'occupancy_pct'  => $pct,
                'status'         => $status,
                'slots'          => $slots,
            ];
        }

        return $days;
    }

    // ── Resumen ───────────────────────────────────────────────────────────────

    #[Computed]
    public function availabilitySummary(): array
    {
        $days = $this->availabilityDays;

        $workingDays = collect($days)->where('is_working_day', true);
        $totalSlots  = $workingDays->sum('total_slots');
        $freeSlots   = $workingDays->sum('free_slots');
        $busySlots   = $workingDays->sum('busy_slots');
        $fullDays    = $workingDays->where('status', 'full')->count();
        $availDays   = $workingDays->whereIn('status', ['available', 'partial'])->count();

        return [
            'total_slots'   => $totalSlots,
            'free_slots'    => $freeSlots,
            'busy_slots'    => $busySlots,
            'occupancy_pct' => $totalSlots > 0 ? (int) round(($busySlots / $totalSlots) * 100) : 0,
            'full_days'     => $fullDays,
            'avail_days'    => $availDays,
            'period_label'  => $this->availabilityPeriodLabel(),
            'slot_minutes'  => $this->slotMinutes,
            'service_name'  => $this->selectedService?->name,
        ];
    }

    protected function availabilityPeriodLabel(): string
    {
        ['start' => $from, 'end' => $to] = $this->availabilityDateRange();

        if ($this->availabilityView === 'week') {
            return ucfirst($from->locale('es')->isoFormat('D MMM'))
                . ' – '
                . ucfirst($to->locale('es')->isoFormat('D MMM YYYY'));
        }

        return ucfirst(Carbon::parse($this->availabilityBaseDate)->locale('es')->isoFormat('MMMM YYYY'));
    }
}
