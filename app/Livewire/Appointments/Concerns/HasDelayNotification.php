<?php

namespace App\Livewire\Appointments\Concerns;

use App\Mail\AppointmentDelayMail;
use App\Models\Appointment;
use App\Models\DelayNotification;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;

trait HasDelayNotification
{
    public bool $showDelayModal  = false;
    public int  $delayMinutes    = 15;
    public int  $delayRecipients = 0;

    public function openDelayModal(): void
    {

        $this->delayMinutes    = 15;
        $this->delayRecipients = $this->getPendingTodayAppointments()->count();
        $this->showDelayModal  = true;
    }

    public function closeDelayModal(): void
    {
        $this->showDelayModal = false;
        $this->resetErrorBag();
    }

    public function sendDelayNotification(): void
    {

        $this->validate([
            'delayMinutes' => 'required|integer|min:10|max:300',
        ], [
            'delayMinutes.required' => 'Indica los minutos de retraso.',
            'delayMinutes.min'      => 'El retraso mínimo es 1 minuto.',
            'delayMinutes.max'      => 'El retraso máximo es 300 minutos (8 horas).',
        ]);

        $appointments = $this->getPendingTodayAppointments();

        if ($appointments->isEmpty()) {
            $this->dispatch('notify', type: 'warning', message: 'No hay citas confirmadas pendientes hoy.');
            $this->closeDelayModal();
            return;
        }

        // 1. Crear cabecera del evento de retraso
        $delayEvent = DelayNotification::create([
            'company_id'       => $this->companyId,
            'sent_by'          => auth()->id(),
            'delay_minutes'    => $this->delayMinutes,
            'recipients_count' => $appointments->count(),
            'status'           => 'sent', // se actualiza abajo si hay errores
        ]);

        $sent   = 0;
        $errors = 0;

        foreach ($appointments as $appointment) {
            $email = $appointment->customer?->user?->email;

            if (! $email) {
                $errors++;
                continue;
            }

            try {
                Mail::to($email)->queue(
                    new AppointmentDelayMail($appointment, $this->delayMinutes)
                );

                // 2. Log individual — ligado a la cabecera
                NotificationLog::create([
                    'delay_notification_id' => $delayEvent->id,
                    'appointment_id'        => $appointment->id,
                    'type'                  => 'Retraso de citas',
                    'recipient_email'       => $email,
                    'status'                => 'sent',
                ]);

                $sent++;
            } catch (\Throwable $e) {
                NotificationLog::create([
                    'delay_notification_id' => $delayEvent->id,
                    'appointment_id'        => $appointment->id,
                    'type'                  => 'delay',
                    'recipient_email'       => $email,
                    'status'                => 'error',
                    'error_message'         => $e->getMessage(),
                ]);

                $errors++;
            }
        }

        // 3. Actualizar estado de la cabecera según resultado final
        $delayEvent->update([
            'status' => match (true) {
                $errors === 0              => 'sent',
                $sent === 0               => 'failed',
                default                   => 'partial',
            },
        ]);

        $this->closeDelayModal();
        $this->dispatch(
            'notify',
            type: $errors === 0 ? 'success' : 'warning',
            message: "Notificación enviada a {$sent} cliente(s)." . ($errors ? " {$errors} fallaron." : '')
        );
    }

    private function getPendingTodayAppointments()
    {
        return $this->scopeCompany(
            Appointment::with([
                'customer' => fn($q) => $q->withTrashed()->with('user:id,name,email'),
            ])
        )
            ->where('status', 'confirmed')
            ->whereDate('start_time', today())
            ->whereTime('start_time', ">", now())
            ->get();
    }
}
