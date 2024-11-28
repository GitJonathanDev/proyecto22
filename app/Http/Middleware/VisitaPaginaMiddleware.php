<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitaPaginaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Obtener el usuario autenticado
        $user = $request->user();

        // Obtener el nombre de la página desde la ruta
        // Aquí estamos obteniendo la ruta actual, no su nombre
        $paginaNombre = $request->route()->getName(); // Corregido: nombre de la ruta
        
        if ($user && $paginaNombre) {
            DB::transaction(function () use ($user, $paginaNombre, $request) {
                // Consultar si la página ya existe en la base de datos
                $pagina = DB::table('Pagina')->where('nombrePagina', $paginaNombre)->first();

                if (!$pagina) {
                    // Si no existe, insertar la página con un conteo de visitas inicial de 0
                    $paginaId = DB::table('Pagina')->insertGetId([
                        'nombrePagina' => $paginaNombre,
                        'conteoVisitas' => 0,
                    ]);
                } else {
                    // Obtener el ID de la página existente
                    $paginaId = $pagina->id;
                }

                // Verificar si el usuario ya ha visitado esta página
                $visita = DB::table('visitaPagina')
                    ->where('codUsuario', $user->codUsuario)
                    ->where('codPagina', $paginaId)
                    ->first();

                if ($visita) {
                    // Si ya ha visitado, incrementar el contador de visitas
                    DB::table('visitaPagina')
                        ->where('codUsuario', $user->codUsuario)
                        ->where('codPagina', $paginaId)
                        ->increment('cantidad', 1);
                } else {
                    // Si es una visita nueva, insertarla
                    DB::table('visitaPagina')->insert([
                        'codUsuario' => $user->codUsuario,
                        'codPagina' => $paginaId,
                        'cantidad' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Actualizar el conteo total de visitas de la página
                $totalVisitas = DB::table('visitaPagina')
                    ->where('codPagina', $paginaId)
                    ->sum('cantidad');

                DB::table('Pagina')
                    ->where('id', $paginaId)
                    ->update(['conteoVisitas' => $totalVisitas]);

                // Compartir el conteo de visitas con la vista
                $request->attributes->set('visitas_pagina', $totalVisitas);
            });
        }

        // Continuar con la solicitud
        return $next($request);
    }
}
