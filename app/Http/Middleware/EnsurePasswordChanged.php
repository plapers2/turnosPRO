<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Redirige obligatoriamente al formulario de cambio de contraseña
     * si el usuario tiene una contraseña temporal activa (must_change_password = true).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $user &&
            $user->must_change_password &&
            !$request->routeIs('password.change') &&
            !$request->routeIs('password.change.update') &&
            !$request->routeIs('logout')
        ) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
