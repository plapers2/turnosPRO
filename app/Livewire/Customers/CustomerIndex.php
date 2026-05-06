<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class CustomerIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
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
                    ->limit(1)
            ])
            ->orderByDesc('total_visitas')
            ->paginate(15);

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

        return view('livewire.customers.customer-index', compact('customers'));
    }
}
