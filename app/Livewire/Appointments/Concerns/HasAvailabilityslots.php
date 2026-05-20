<?php

namespace App\Livewire\Appointments\Concerns;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Attributes\Computed;

trait HasAvailabilitySlots
{
    // ── Estado público ────────────────────────────────────────────────────────

    /** 'week' | 'month' */
    public string $availabilityView = 'week';

    /** Fecha base para calcular el rango (Y-m-d).  */
    public string $availabilityBaseDate = '';

    /** Duración en minutos de cada slot a evaluar. */
    public int $slotMinutes = 30;

    // ── Boot ──────────────────────────────────────────────────────────────────

    public function bootHasAvailabilitySlots(): void
    {
        if (empty($this->availabilityBaseDate)) {
            $this->availabilityBaseDate = now()->toDateString();
        }
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
                'start' => $base->copy()->startOfWeek(),   // lunes
                'end'   => $base->copy()->endOfWeek(),      // domingo
            ];
        }

        return [
            'start' => $base->copy()->startOfMonth(),
            'end'   => $base->copy()->endOfMonth(),
        ];
    }

    // ── Horario laboral ───────────────────────────────────────────────────────

    /**
     * Devuelve el horario laboral del día.
     * Ajusta este método a cómo almacenes el horario en tu BD.
     *
     * Retorna ['start' => '09:00', 'end' => '18:00'] o null si no labora.
     */
    protected function getWorkingHours(Carbon $date, ?int $professionalId = null): ?array
    {
        // Días no laborables (0 = domingo, 6 = sábado).
        // TODO: reemplaza con la lógica real de tu empresa/profesional.
        $dayOfWeek = (int) $date->format('N'); // 1=lun … 7=dom
       

        if ($dayOfWeek >= 6) {
            return null; // fin de semana → sin slots
        }

        // Horario genérico. Puedes leerlo de company->schedule o user->schedule.
        return ['start' => '09:00', 'end' => '18:00'];
    }

    // ── Consulta de citas del rango ───────────────────────────────────────────

    protected function appointmentsInRange(Carbon $from, Carbon $to): \Illuminate\Support\Collection
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
            ->when(
                $this->isAdmin && ! empty($this->filterProfessional),
                fn($q) => $q->where('user_id', $this->filterProfessional)
            )
            ->when(
                ! $this->isAdmin,
                fn($q) => $q->where('user_id', auth()->id())
            )
            ->get();
    }

    // ── Cálculo principal ─────────────────────────────────────────────────────

    /**
     * Retorna un array de días con sus slots libres y ocupados.
     *
     * Estructura de cada elemento:
     * [
     *   'date'           => '2025-06-02',
     *   'label'          => 'Lun 2',
     *   'is_working_day' => true,
     *   'total_slots'    => 18,
     *   'free_slots'     => 12,
     *   'busy_slots'     => 6,
     *   'occupancy_pct'  => 33,          // 0-100
     *   'status'         => 'available', // 'available'|'partial'|'full'|'closed'
     *   'slots'          => [            // detalle hora a hora
     *       ['time' => '09:00', 'free' => true],
     *       ['time' => '09:30', 'free' => false],
     *       ...
     *   ],
     * ]
     */
    #[Computed]
    public function availabilityDays(): array
    {
        ['start' => $from, 'end' => $to] = $this->availabilityDateRange();

        $appointments = $this->appointmentsInRange($from, $to);
        $days         = [];

        foreach (CarbonPeriod::create($from, $to) as $day) {
            $hours = $this->getWorkingHours($day);

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

            // Generar todos los slots del día.
            $slots      = [];
            $cursor     = Carbon::parse($day->toDateString() . ' ' . $hours['start']);
            $dayEnd     = Carbon::parse($day->toDateString() . ' ' . $hours['end']);
            $dayAppts   = $appointments->filter(
                fn($a) => Carbon::parse($a->start_time)->toDateString() === $day->toDateString()
            );

            while ($cursor->copy()->addMinutes($this->slotMinutes)->lte($dayEnd)) {
                $slotEnd = $cursor->copy()->addMinutes($this->slotMinutes);

                // Un slot está ocupado si hay alguna cita que se solape con él.
                $busy = $dayAppts->contains(function ($appt) use ($cursor, $slotEnd) {
                    $apptStart = Carbon::parse($appt->start_time);
                    $apptEnd   = Carbon::parse($appt->end_time);

                    // Solape: el slot empieza antes de que la cita acabe
                    //         Y el slot acaba después de que la cita empiece.
                    return $cursor->lt($apptEnd) && $slotEnd->gt($apptStart);
                });

                $slots[] = [
                    'time' => $cursor->format('H:i'),
                    'free' => ! $busy,
                ];

                $cursor->addMinutes($this->slotMinutes);
            }

            $total   = count($slots);
            $free    = collect($slots)->where('free', true)->count();
            $busy    = $total - $free;
            $pct     = $total > 0 ? (int) round(($busy / $total) * 100) : 0;

            $status = match (true) {
                $total === 0  => 'closed',
                $free  === 0  => 'full',
                $pct   >= 60  => 'partial',
                default       => 'available',
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

    /**
     * Resumen de la semana/mes para la barra de estadísticas superior.
     */
    #[Computed]
    public function availabilitySummary(): array
    {
        $days = $this->availabilityDays();

        $workingDays  = collect($days)->where('is_working_day', true);
        $totalSlots   = $workingDays->sum('total_slots');
        $freeSlots    = $workingDays->sum('free_slots');
        $busySlots    = $workingDays->sum('busy_slots');
        $fullDays     = $workingDays->where('status', 'full')->count();
        $availDays    = $workingDays->whereIn('status', ['available', 'partial'])->count();

        return [
            'total_slots'  => $totalSlots,
            'free_slots'   => $freeSlots,
            'busy_slots'   => $busySlots,
            'occupancy_pct' => $totalSlots > 0 ? (int) round(($busySlots / $totalSlots) * 100) : 0,
            'full_days'    => $fullDays,
            'avail_days'   => $availDays,
            'period_label' => $this->availabilityPeriodLabel(),
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
