<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use Illuminate\Support\Facades\Redirect;
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
        $customers = Customer::paginate(8);

        return view('customer.index', compact('customers'))
            ->with('i', ($request->input('page', 1) - 1) * $customers->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create(): View
    // {
    //     $customer = new Customer();

    //     return view('customer.create', compact('customer'));
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request): RedirectResponse
    {
        Customer::create($request->validated());

        return Redirect::route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $customer = Customer::findOrFail($id);

        return view('customer.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $customer = Customer::find($id);

        return view('customer.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return Redirect::route('customers.index')
            ->with('success', 'Customer updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Customer::find($id)->delete();

        return Redirect::route('customers.index')
            ->with('success', 'Customer deleted successfully');
    }
    public function editProfile()
    {
        $cliente = auth()->user();
        return view('customer.edit-profile', compact('cliente'));
    }

    public function updateProfile(UpdateCustomerProfileRequest $request)
    {
        $cliente = auth()->user();

        $cliente->name  = $request->name;
        $cliente->phone = $request->phone;

        if ($request->filled('new_password')) {
            $cliente->password = Hash::make($request->new_password);
        }

        $cliente->save();

        return redirect()->route('dashboard')->with('success', 'Perfil actualizado correctamente.');
    }
}
