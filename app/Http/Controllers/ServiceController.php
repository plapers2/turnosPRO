<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ServiceRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
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
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'duracion' => 'required|integer',
            'precio' => 'required|numeric',
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        // guardar imagen
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('services', 'public');
        }

        // crear servicio
        Service::create([
            'name' => $data['nombre'],
            'description' => $data['descripcion'],
            'duration' => $data['duracion'],
            'price' => $data['precio'],
            'image' => $data['imagen'],
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
    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'duracion' => 'required|integer',
            'precio' => 'required|numeric',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        // Manejo de imagen
        if ($request->hasFile('imagen')) {

            // eliminar anterior
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            // guardar nueva
            $data['imagen'] = $request->file('imagen')->store('services', 'public');
        }

        // Mapear nombres
        $service->update([
            'name' => $data['nombre'],
            'description' => $data['descripcion'],
            'duration' => $data['duracion'],
            'price' => $data['precio'],
            'image' => $data['imagen'] ?? $service->image,
            'company_id' => $request->company_id
        ]);

        return Redirect::route('services.index')
            ->with('success', 'Servicio actualizado correctamente');
    }


    public function destroy(Request $request, Service $service)
    {
        // eliminar imagen
        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        // Si es AJAX (fetch)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Servicio eliminado correctamente'
            ]);
        }

        // Si es formulario normal
        return redirect()->route('services.index')
            ->with('success', 'Servicio eliminado correctamente');
    }
}
