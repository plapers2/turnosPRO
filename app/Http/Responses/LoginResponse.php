<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('empleado')) {
            if ($user->companies->count() > 1) {
                return redirect('/select-company');
            }

            $company = $user->companies()->first();
            session(['active_company_id' => $company->id]);
            return redirect('/dashboard');
        }

        if ($user->hasRole('master')) {
            return redirect('/master');
        }

        if ($user->hasRole('cliente')) {
            return redirect('/dashboard');
        }

        return redirect('/no-company');
    }
}
