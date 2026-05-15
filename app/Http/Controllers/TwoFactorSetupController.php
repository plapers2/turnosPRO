<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorSetupController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $qrSvg = null;
        $recoveryCodes = null;
        $secret = null;

        if ($user->two_factor_secret && !$user->two_factor_confirmed_at) {
            // Tiene secret pero no ha confirmado → mostrar QR
            $qrSvg  = $user->twoFactorQrCodeSvg();
            $secret = decrypt($user->two_factor_secret);
        }

        if ($user->two_factor_confirmed_at) {
            // Ya tiene 2FA activo → mostrar códigos de recuperación
            $recoveryCodes = $user->recoveryCodes();
        }

        return view('profile.two-factor', compact('qrSvg', 'secret', 'recoveryCodes'));
    }
}
