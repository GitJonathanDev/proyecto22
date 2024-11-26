<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Opcion;
use App\Models\Permiso;
use App\Models\TipoUsuarioPermiso;
use App\Models\TipoUsuario;

class MenuDinamicoController extends Controller
{
    // MENÚS
    public function listarMenus()
    {
        return response()->json(Menu::all(), 200);
    }

    public function crearMenu(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'icono' => 'nullable|string|max:255',
        ]);

        $menu = Menu::create($validatedData);
        return response()->json($menu, 201);
    }

    public function actualizarMenu(Request $request, $codMenu)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'icono' => 'nullable|string|max:255',
        ]);

        $menu = Menu::findOrFail($codMenu);
        $menu->update($validatedData);
        return response()->json($menu, 200);
    }

    public function eliminarMenu($codMenu)
    {
        Menu::destroy($codMenu);
        return response()->json(['message' => 'Menú eliminado'], 200);
    }

    // OPCIONES
    public function listarOpciones($codMenu)
    {
        $opciones = Opcion::where('codMenuF', $codMenu)->get();
        return response()->json($opciones, 200);
    }

    public function crearOpcion(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'ruta' => 'required|string|max:255',
            'icono' => 'nullable|string|max:255',
            'codMenuF' => 'required|exists:menu,codMenu',
        ]);

        $opcion = Opcion::create($validatedData);
        return response()->json($opcion, 201);
    }

    public function actualizarOpcion(Request $request, $codOpcion)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'ruta' => 'required|string|max:255',
            'icono' => 'nullable|string|max:255',
        ]);

        $opcion = Opcion::findOrFail($codOpcion);
        $opcion->update($validatedData);
        return response()->json($opcion, 200);
    }

    public function eliminarOpcion($codOpcion)
    {
        Opcion::destroy($codOpcion);
        return response()->json(['message' => 'Opción eliminada'], 200);
    }

    // PERMISOS
    public function listarPermisos($codOpcion)
    {
        $permisos = Permiso::where('codOpcionF', $codOpcion)->get();
        return response()->json($permisos, 200);
    }

    public function crearPermiso(Request $request)
    {
        $validatedData = $request->validate([
            'codOpcionF' => 'required|exists:opcion,codOpcion',
            'accion' => 'required|string|max:255',
        ]);

        $permiso = Permiso::create($validatedData);
        return response()->json($permiso, 201);
    }

    public function eliminarPermiso($codPermiso)
    {
        Permiso::destroy($codPermiso);
        return response()->json(['message' => 'Permiso eliminado'], 200);
    }

    // TIPOS DE USUARIO CON PERMISOS
    public function listarTiposUsuario($codPermiso)
    {
        $tiposUsuario = TipoUsuarioPermiso::with('tipoUsuario')
            ->where('codPermisoF', $codPermiso)
            ->get()
            ->pluck('tipoUsuario');

        return response()->json($tiposUsuario, 200);
    }

    public function agregarTipoUsuario(Request $request, $codPermiso)
    {
        $validatedData = $request->validate([
            'codTipoUsuarioF' => 'required|exists:TipoUsuario,codTipoUsuario',
        ]);

        $validatedData['codPermisoF'] = $codPermiso;

        $tipoUsuarioPermiso = TipoUsuarioPermiso::create($validatedData);
        return response()->json($tipoUsuarioPermiso, 201);
    }

    public function quitarTipoUsuario($codPermiso, $codTipoUsuario)
    {
        TipoUsuarioPermiso::where('codPermisoF', $codPermiso)
            ->where('codTipoUsuarioF', $codTipoUsuario)
            ->delete();

        return response()->json(['message' => 'Tipo de usuario eliminado del permiso'], 200);
    }
}
