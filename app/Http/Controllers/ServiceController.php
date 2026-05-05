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
        return view('services.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $service = new Service();

        return view('services.create', compact('service'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $companyId = session('active_company_id');

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
            'company_id' => $companyId
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
        $companyId = session("active_company_id");
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
            'company_id' => $companyId
        ]);

        return Redirect::route('services.index')
            ->with('success', 'Servicio actualizado correctamente');
    }


    public function destroy(Request $request, Service $service)
    {

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

    public function restore($id)
    {
        $user = Service::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('services.index')
            ->with('success', 'Servicio restaurado correctamente');
    }
}
