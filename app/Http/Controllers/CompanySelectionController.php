<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanySelectionController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $companies = $user->companies;

        return view('auth.select-company', compact('companies'));
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

        session([
            'active_company_id' => $request->company_id
        ]);

        return redirect()->route('dashboard');
    }
}
