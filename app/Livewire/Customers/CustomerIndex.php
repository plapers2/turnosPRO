<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\CompanyInvitation;
use App\Mail\InvitationMail;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerIndex extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $servicio  = '';
    public string $frecuente = '';

    // Modal invitación
    public bool   $showInvitationModal = false;
    public string $invitationEmail     = '';
    public ?string $generatedLink      = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingServicio(): void
    {
        $this->resetPage();
    }
    public function updatingFrecuente(): void
    {
        $this->resetPage();
    }

    public function openInvitationModal(): void
    {
        $this->invitationEmail = '';
        $this->generatedLink   = null;
        $this->showInvitationModal = true;
    }

    public function closeInvitationModal(): void
    {
        $this->showInvitationModal = false;
        $this->invitationEmail     = '';
        $this->generatedLink       = null;
    }

    public function generateInvitation(): void
    {
        $this->validate([
            'invitationEmail' => 'required|email',
        ], [
            'invitationEmail.required' => 'El correo es obligatorio para generar la invitación.',
            'invitationEmail.email'    => 'El correo no tiene un formato válido.',
        ]);

        $companyId = session('active_company_id');
        abort_if(!$companyId, 403);

        $invitation = CompanyInvitation::create([
            'company_id' => $companyId,
            'invited_by' => auth()->id(),
            'token'      => Str::random(48),
            'email'      => $this->invitationEmail,
            'status'     => 'sent',
            'expires_at' => now()->addDays(7),
        ]);

        $link = route('register.invite', $invitation->token);

        \Mail::to($invitation->email)->send(new InvitationMail($invitation, $link));

        $this->generatedLink   = $link;
        $this->invitationEmail = '';
    }

    public function render()
    {
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');
        $companyId = session('active_company_id');

        $customers = Customer::where('company_id', $companyId)
            ->whereHas('appointments', fn($q) => $q->where('company_id', $companyId))
            ->when($this->search, fn($q) => $q->whereHas(
                'user',
                fn($inner) =>
                $inner->where('name',  'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
            ))
            ->when($this->servicio, function ($q) use ($companyId) {
                $q->whereHas('appointments', function ($inner) use ($companyId) {
                    $inner->where('company_id', $companyId)
                        ->where('status', 'completed')
                        ->whereHas(
                            'services',
                            fn($s) => $s->where('name', 'like', "%{$this->servicio}%")
                        );
                });
            })
            ->when($this->frecuente === 'si', fn($q) => $q->having('total_visitas', '>=', 5))
            ->when($this->frecuente === 'no', fn($q) => $q->having('total_visitas', '<', 5))
            ->withCount([
                'appointments as total_visitas' => fn($q) => $q
                    ->where('status', 'completed')
                    ->where('company_id', $companyId),
            ])
            ->with([
                'user',
                'appointments' => fn($q) => $q
                    ->where('company_id', $companyId)
                    ->where('status', 'completed')
                    ->latest('start_time')
                    ->limit(1),
            ])
            ->orderByDesc('total_visitas')
            ->paginate(10);

        $customers->each(function ($customer) use ($companyId) {
            $customer->servicio_favorito = DB::table('appointment_service')
                ->join('appointments', 'appointments.id', '=', 'appointment_service.appointment_id')
                ->join('services', 'services.id', '=', 'appointment_service.service_id')
                ->where('appointments.customer_id', $customer->id)
                ->where('appointments.company_id', $companyId)
                ->where('appointments.status', 'completed')
                ->select('services.name', DB::raw('count(*) as total'))
                ->groupBy('services.id', 'services.name')
                ->orderByDesc('total')
                ->first();
        });

        $servicios = \App\Models\Service::where('company_id', $companyId)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('livewire.customers.customer-index', compact('customers', 'servicios'));
    }
}
