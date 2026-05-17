<?php

namespace App\Responses;

class LoginResponse
{
    public function toResponse($request)
    {
        $user = auth()->user();

        // Ejemplo de lógica
        if ($user->hasRole('admin')) {
            if($user->companies->count() > 1)
            return redirect('/admin/dashboard');
        }

        if ($user->companies()->count() > 1) {
            return redirect('/select-company');
        }

        if ($user->companies()->count() === 1) {
            $company = $user->companies()->first();

            session(['company_id' => $company->id]);

            return redirect('/dashboard');
        }

        return redirect('/no-company');
    }
}
