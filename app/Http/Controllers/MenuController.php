<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route; // Asegúrate de importar esto
use App\Models\Opcion;
use App\Models\Menu;

class MenuController extends Controller
{
    public function getMenuOptions()
    {
        $user = Auth::user();
        $tipoUsuario = $user->tipoUsuario;

        // Obtener todos los permisos del tipo de usuario
        $permisos = $tipoUsuario->permisos;

        // Inicializamos el arreglo de menús de forma dinámica
        $menuOptions = [];

        // Iteramos sobre los permisos y recogemos las opciones asociadas
        foreach ($permisos as $permiso) {
            $opcion = Opcion::find($permiso->codOpcionF);
            if ($opcion) {
                $menu = Menu::find($opcion->codMenuF);

                if ($menu) {
                    $menuName = $menu->nombre;
                    if (!isset($menuOptions[$menuName])) {
                        $menuOptions[$menuName] = [
                            'icono' => $menu->icono,
                            'opciones' => [],
                        ];
                    }

                    // Aseguramos que la opción no se haya añadido previamente
                    $opcionExistente = false;
                    foreach ($menuOptions[$menuName]['opciones'] as $existingOption) {
                        if ($existingOption['nombre'] === $opcion->nombre) {
                            $opcionExistente = true;
                            break;
                        }
                    }

                    if (!$opcionExistente) {
                        // Convertimos la ruta en una URL usando `route()`
                        $menuOptions[$menuName]['opciones'][] = [
                            'nombre' => $opcion->nombre,
                            'icono' => $opcion->icono,
                            'ruta' => route($opcion->ruta), // Aquí hacemos el ajuste
                        ];
                    }
                }
            }
        }

        return response()->json($menuOptions);
    }
}
