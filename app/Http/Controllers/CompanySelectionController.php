<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanySelectionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $companies = $user->companies;

        if ($companies->count() > 1) {
            return view('auth.select-company', compact('companies'));
        }

        if ($companies->count() === 1) {
            $company = $companies->first();
            session([
                'active_company_id'   => $company->pivot->company_id,
                'active_company_name' => $company->name,
            ]);
            return redirect()->route('dashboard');
        }

        // Sin empresas — cerrar sesión y redirigir al login con mensaje
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        request()->session()->flash('error', 'Tu cuenta no tiene ninguna empresa asignada. Contacta al administrador de la plataforma.');

        return redirect()->route('login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $user = auth()->user();

        $belongsToUser = $user->companies()
            ->where('companies.id', $request->company_id)
            ->exists();

        if (!$belongsToUser) {
            abort(403);
        }

        $company = \App\Models\Company::find($request->company_id);

        session([
            'active_company_id'   => $request->company_id,
            'active_company_name' => $company->name,
        ]);

        return redirect()->route('dashboard');
    }
}
