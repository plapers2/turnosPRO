<?php

namespace App\Http\Controllers;

use App\Models\Company;
use app\Models\TypeCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $companies = Company::paginate(10);

        return view('company.index', compact('companies'))
            ->with('i', ($request->input('page', 1) - 1) * $companies->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $company = new Company();
        // $typeCompanies = TypeCompany::all();

        return view('company.create', compact('company'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Company::create($data);

        return Redirect::route('companies.index')
            ->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $company = Company::find($id);

        return view('company.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $company = Company::find($id);
        // $typeCompanies = TypeCompany::all();

        return view('company.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($data);

        return Redirect::route('companies.index')
            ->with('success', 'Company updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Company::find($id)->delete();

        return Redirect::route('companies.index')
            ->with('success', 'Company deleted successfully');
    }
}
