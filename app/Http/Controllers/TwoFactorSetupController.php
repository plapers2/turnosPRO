<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;

class TwoFactorSetupController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $qrSvg = null;
        $recoveryCodes = null;
        $secret = null;

        if ($user->two_factor_secret && !$user->two_factor_confirmed_at) {
            $qrSvg  = $user->twoFactorQrCodeSvg();
            $secret = decrypt($user->two_factor_secret);
        }

        if ($user->two_factor_confirmed_at) {
            $recoveryCodes = $user->recoveryCodes();
        }

        return view('profile.two-factor', compact('qrSvg', 'secret', 'recoveryCodes'));
    }

    public function confirm(Request $request, ConfirmTwoFactorAuthentication $confirm)
    {
        try {
            $confirm($request->user(), $request->input('code'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors(['code' => 'El código es incorrecto o ha expirado. Inténtalo de nuevo.'])
                ->withInput();
        }

        return redirect()->route('two-factor.setup')
            ->with('status', 'two-factor-authentication-confirmed');
    }
}
