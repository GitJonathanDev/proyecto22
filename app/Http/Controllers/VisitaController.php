<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class VisitaController extends Controller
{
    public function obtenerVisitas(Request $request)
    {
        // Obtener la ruta actual de la solicitud
        $ruta = $request->input('ruta');
        
        // Obtener el nombre de la ruta usando la acción de la ruta
        $nombrePagina = $this->obtenerNombreRuta($ruta);

        if (!$nombrePagina) {
            return response()->json(['visitas' => 0]);
        }

        // Consultar el conteo de visitas de la página en la base de datos
        $pagina = DB::table('Pagina')->where('nombrePagina', $nombrePagina)->first();

        // Devolver el conteo de visitas o 0 si no se encuentra la página
        $visitas = $pagina ? $pagina->conteoVisitas : 0;

        return response()->json(['visitas' => $visitas]);
    }

    // Función para obtener el nombre de la ruta
    private function obtenerNombreRuta($ruta)
    {
        // Obtener la ruta registrada por su nombre
        $route = Route::getRoutes()->match(Request::create($ruta));
        return $route ? $route->getName() : null;
    }
}
