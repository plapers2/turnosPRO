<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($user->hasRole('master')) {
                    return redirect()->route('master.index');
                }

                if ($user->hasRole('admin') || $user->hasRole('empleado') || $user->hasRole('cliente')) {
                    return redirect()->route('dashboard');
                }

                // Sesión activa pero sin rol válido — destruir y mostrar error
                Auth::guard($guard)->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Tu cuenta no tiene ninguna empresa asignada. Contacta al administrador de la plataforma.');
            }
        }

        return $next($request);
    }
}
