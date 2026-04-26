<?php

namespace App\Http\Controllers;

use App\Models\TypeCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TypeCompanyRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TypeCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $typeCompanies = TypeCompany::withCount('companies')->paginate();

        return view('type-company.index', compact('typeCompanies'))
            ->with('i', ($request->input('page', 1) - 1) * $typeCompanies->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $typeCompany = new TypeCompany();

        return view('type-company.create', compact('typeCompany'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TypeCompanyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('type-companies', 'public');
        }

        TypeCompany::create($data);

        return Redirect::route('type-companies.index')
            ->with('success', 'TypeCompany created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $typeCompany = TypeCompany::find($id);

        return view('type-company.show', compact('typeCompany'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $typeCompany = TypeCompany::find($id);

        return view('type-company.edit', compact('typeCompany'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TypeCompanyRequest $request, TypeCompany $typeCompany): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('type-companies', 'public');
        }

        $typeCompany->update($data);

        return Redirect::route('type-companies.index')
            ->with('success', 'TypeCompany updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        TypeCompany::find($id)->delete();

        return Redirect::route('type-companies.index')
            ->with('success', 'TypeCompany deleted successfully');
    }
}
