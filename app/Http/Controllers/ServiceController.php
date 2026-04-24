<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ServiceRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $services = Service::paginate();


        return view('services.index', compact('services'))
            ->with('i', ($request->input('page', 1) - 1) * $services->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $service = new Service();
        $companies = Company::all();

        return view('services.create', compact('service', 'companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {


        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $ruta = $request->file('image')->store('services', 'public');

        Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration,
            'price' => $request->price,
            'image' => $ruta,
            'company_id' => $request->company_id
        ]);


        return redirect()->route('services.index')
            ->with('success', 'Servicio creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $service = Service::find($id);

        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $service = Service::find($id);
        $companies = Company::all();

        return view('services.edit', compact('service', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        return Redirect::route('services.index')
            ->with('success', 'Service updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Service::find($id)->delete();

        return Redirect::route('services.index')
            ->with('success', 'Service deleted successfully');
    }
}
