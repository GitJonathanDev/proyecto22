<?php

namespace App\Http\Controllers;

use App\Models\Opcion;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Método para realizar la búsqueda en el modelo Opcion.
     */
    public function search(Request $request)
    {
        $searchQuery = $request->input('query');

        // Resultados para "Opcion"
        $opciones = Opcion::where('nombre', 'like', "%$searchQuery%")
                          ->get()
                          ->map(function($opcion) {
                              return [
                                  'type' => 'opcion',
                                  'nombre' => $opcion->nombre,
                                  'ruta' => route($opcion->ruta), // Genera la URL con `route()`
                                  'icono' => 'fas fa-cogs', // Icono de engranaje
                              ];
                          });

        // Retorna los resultados como JSON
        return response()->json($opciones);
    }
}
