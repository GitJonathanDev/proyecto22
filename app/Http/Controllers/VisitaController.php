<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class VisitaController extends Controller
{
    public function obtenerVisitas(Request $request)
    {
        $ruta = $request->input('ruta');
        
        $nombrePagina = $this->obtenerNombreRuta($ruta);

        if (!$nombrePagina) {
            return response()->json(['visitas' => 0]);
        }

        $pagina = DB::table('Pagina')->where('nombrePagina', $nombrePagina)->first();
        $visitas = $pagina ? $pagina->conteoVisitas : 0;

        return response()->json(['visitas' => $visitas]);
    }
    private function obtenerNombreRuta($ruta)
    {
        $route = Route::getRoutes()->match(Request::create($ruta));
        return $route ? $route->getName() : null;
    }
}
