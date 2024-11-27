<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Opcion;
use App\Models\Menu;
use Illuminate\Support\Facades\Route;


class MenuController extends Controller
{
    public function getMenuOptions()
    {
        $user = Auth::user();
        $tipoUsuario = $user->tipoUsuario;

        $permisos = $tipoUsuario->permisos;

        $menuOptions = [];

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

                    $opcionExistente = false;
                    foreach ($menuOptions[$menuName]['opciones'] as $existingOption) {
                        if ($existingOption['nombre'] === $opcion->nombre) {
                            $opcionExistente = true;
                            break;
                        }
                    }

                    if (!$opcionExistente) {
                        $ruta = $opcion->ruta; // Guardar el nombre de la ruta
                        $url = Route::has($ruta) ? route($ruta) : null; // Generar URL solo si la ruta existe

                        $menuOptions[$menuName]['opciones'][] = [
                            'nombre' => $opcion->nombre,
                            'icono' => $opcion->icono,
                            'ruta' => $url, // Usar la URL generada
                        ];
                    }
                }
            }
        }

        return response()->json($menuOptions);
    }
}
