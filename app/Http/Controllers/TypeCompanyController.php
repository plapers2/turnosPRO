<?php

namespace App\Http\Controllers;

use App\Models\TypeCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TypeCompanyRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TypeCompanyController extends Controller
{
    public function index(): View
    {
        return view('type-company.index');
    }

    public function create(): View
    {
        $typeCompany = new TypeCompany();

        return view('type-company.create', compact('typeCompany'));
    }

    public function store(TypeCompanyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('type-companies', 'public');
        }

        TypeCompany::create($data);

        return Redirect::route('master.type-companies.index')
            ->with('success', 'Tipo de empresa creado correctamente.');
    }

    public function edit($id): View
    {
        $typeCompany = TypeCompany::findOrFail($id);

        return view('type-company.edit', compact('typeCompany'));
    }

    public function update(TypeCompanyRequest $request, TypeCompany $typeCompany): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($typeCompany->logo) {
                Storage::disk('public')->delete($typeCompany->logo);
            }
            $data['logo'] = $request->file('logo')->store('type-companies', 'public');
        } else {
            unset($data['logo']);
        }

        $typeCompany->update($data);

        return Redirect::route('master.type-companies.index')
            ->with('success', 'Tipo de empresa actualizado correctamente.');
    }

    public function destroy($id): RedirectResponse
    {
        TypeCompany::findOrFail($id)->delete();

        return Redirect::route('master.type-companies.index')
            ->with('success', 'Tipo de empresa desactivado correctamente.');
    }

    public function restore(int $id): RedirectResponse
    {
        TypeCompany::withTrashed()->findOrFail($id)->restore();

        return Redirect::route('master.type-companies.index')
            ->with('success', 'Tipo de empresa reactivado correctamente.');
    }
}
