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
        $menu = Menu::create($request->all());
        return response()->json($menu, 201);
    }

    public function actualizarMenu(Request $request, $codMenu)
    {
        $menu = Menu::findOrFail($codMenu);
        $menu->update($request->all());
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
        $opcion = Opcion::create($request->all());
        return response()->json($opcion, 201);
    }

    public function actualizarOpcion(Request $request, $codOpcion)
    {
        $opcion = Opcion::findOrFail($codOpcion);
        $opcion->update($request->all());
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
        $permiso = Permiso::create($request->all());
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
        $tiposUsuario = tipoUsuarioPermiso::with('TipoUsuario')
            ->where('codPermisoF', $codPermiso)
            ->get()
            ->pluck('TipoUsuario');

        return response()->json($tiposUsuario, 200);
    }

    public function agregarTipoUsuario(Request $request, $codPermiso)
    {
        $validatedData = $request->all();
        $validatedData['codPermisoF'] = $codPermiso;

        $tipoUsuarioPermiso = tipoUsuarioPermiso::create($validatedData);
        return response()->json($tipoUsuarioPermiso, 201);
    }

    public function quitarTipoUsuario($codPermiso, $codTipoUsuario)
    {
        tipoUsuarioPermiso::where('codPermisoF', $codPermiso)
            ->where('codTipoUsuarioF', $codTipoUsuario)
            ->delete();

        return response()->json(['message' => 'Tipo de usuario eliminado del permiso'], 200);
    }
}
