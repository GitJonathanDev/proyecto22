<?php

namespace App\Http\Controllers;

use App\Models\Opcion;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Método para realizar la búsqueda en todas las tablas (excepto las por defecto de Laravel).
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
                                  'ruta' => $opcion->ruta,
                                  'icono' => 'fas fa-cogs', // Icono de engranaje
                              ];
                          });

        // Resultados para "Proveedor"
        $proveedores = Proveedor::where('nombre', 'like', "%$searchQuery%")
                                ->orWhere('direccion', 'like', "%$searchQuery%")
                                ->get()
                                ->map(function($proveedor) {
                                    return [
                                        'type' => 'proveedor',
                                        'nombre' => $proveedor->nombre,
                                        'ruta' => route('proveedor.index', ['buscar' => $proveedor->nombre]),
                                        'icono' => 'fas fa-pencil-alt', // Icono de lápiz
                                    ];
                                });

        // Combina ambos resultados
        $results = $opciones->merge($proveedores);
        
        return response()->json($results);
    }
}
