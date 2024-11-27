<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitaPaginaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $paginaNombre = $request->route()->getName();

        if ($user && $paginaNombre) {
            DB::transaction(function () use ($user, $paginaNombre, $request) {
                $pagina = DB::table('Pagina')->where('nombrePagina', $paginaNombre)->first();

                if (!$pagina) {
                    $paginaId = DB::table('Pagina')->insertGetId([
                        'nombrePagina' => $paginaNombre,
                        'conteoVisitas' => 0,
                    ]);
                } else {
                    $paginaId = $pagina->id;
                }
                $visita = DB::table('visitaPagina')
                    ->where('codUsuario', $user->codUsuario)
                    ->where('codPagina', $paginaId)
                    ->first();

                if ($visita) {
                    DB::table('visitaPagina')
                        ->where('codUsuario', $user->codUsuario)
                        ->where('codPagina', $paginaId)
                        ->increment('cantidad', 1);
                } else {
                    DB::table('visitaPagina')->insert([
                        'codUsuario' => $user->codUsuario,
                        'codPagina' => $paginaId,
                        'cantidad' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $totalVisitas = DB::table('visitaPagina')
                    ->where('codPagina', $paginaId)
                    ->sum('cantidad');

                DB::table('Pagina')
                    ->where('id', $paginaId)
                    ->update(['conteoVisitas' => $totalVisitas]);

                // Compartir el conteo de visitas con la vista
                $request->attributes->set('visitaPagina', $totalVisitas);
            });
        }

        return $next($request);
    }
}
