<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();

        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        if ($user->hasRole('admin') || $user->hasRole('empleado')) {
            if ($user->companies()->count() > 1) {
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
            $token = session()->pull('invitation_token');

            if ($token) {
                $invitation = \App\Models\CompanyInvitation::where('token', $token)
                    ->whereNull('deleted_at')
                    ->where('expires_at', '>', now())
                    ->first();

                if ($invitation && $invitation->isUsable()) {
                    $user->companies()->syncWithoutDetaching([$invitation->company_id]);
                    $invitation->update(['status' => 'registered']);
                }
            }

            return redirect('/dashboard');
        }

        return redirect('/dashboard');
    }
}
