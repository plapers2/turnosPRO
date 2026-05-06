<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateCustomerProfileRequest;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $companyId = session('active_company_id');
        $search    = $request->input('search');

        $customers = Customer::where('company_id', $companyId)
            ->whereHas('appointments', fn($q) => $q->where('company_id', $companyId))
            ->when($search, fn($q) => $q->where(
                fn($inner) =>
                $inner->where('name',  'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
            ))
            ->withCount([
                'appointments as total_visitas' => fn($q) => $q
                    ->where('status', 'completed')
                    ->where('company_id', $companyId),
            ])
            ->with([
                'appointments' => fn($q) => $q
                    ->where('company_id', $companyId)
                    ->where('status', 'completed')
                    ->latest('start_time')
                    ->limit(1)
            ])
            ->orderByDesc('total_visitas')
            ->paginate(15)
            ->withQueryString();

        $customers->each(function ($customer) use ($companyId) {
            $customer->servicio_favorito = \DB::table('appointment_service')
                ->join('appointments', 'appointments.id', '=', 'appointment_service.appointment_id')
                ->join('services', 'services.id', '=', 'appointment_service.service_id')
                ->where('appointments.customer_id', $customer->id)
                ->where('appointments.company_id', $companyId)
                ->where('appointments.status', 'completed')
                ->select('services.name', \DB::raw('count(*) as total'))
                ->groupBy('services.id', 'services.name')
                ->orderByDesc('total')
                ->first();
        });

        return view('customer.history', compact('customers', 'search'));
    }


    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $customer = Customer::findOrFail($id);

        return view('customer.show', compact('customer'));
    }
}
