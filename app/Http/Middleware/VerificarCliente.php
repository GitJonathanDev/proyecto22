<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarCliente
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->codTipoUsuarioF === 1) {
                return $next($request); 
            }
        }
        return redirect()->back()->with('error', 'Acceso no autorizado. Solo clientes pueden acceder.');
    }
}
