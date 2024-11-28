<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        // Parámetros de búsqueda
        $criterio = $request->get('criterio', '');
        $buscar = $request->get('buscar', '');

        // Consulta con filtros de búsqueda
        $clientes = Cliente::when($criterio, function ($query) use ($criterio, $buscar) {
            return $query->where($criterio, 'LIKE', '%' . $buscar . '%');
        })->paginate(5); // Paginación de 10 por página

        // Retornar a Inertia la vista de clientes con los datos de paginación y los filtros
        return Inertia::render('Cliente/Index', [
            'clientes' => $clientes,
            'criterio' => $criterio,
            'buscar' => $buscar,
        ]);
    }
    public function create()
    {
        return Inertia::render('Cliente/Create');
    }
    public function store(Request $request)
    {
        // Validación de los datos del formulario.
        $validator = Validator::make($request->all(), [
            'carnetIdentidad' => 'required|digits_between:8,10|unique:cliente,carnetIdentidad',
            'nombre' => 'required|string|min:3|max:30',
            'apellidoPaterno' => 'required|string|min:3|max:30',
            'apellidoMaterno' => 'required|string|min:3|max:30',
            'sexo' => 'required|in:masculino,femenino',
            'edad' => 'required|integer|between:8,100',
            'telefono' => 'required|digits_between:8,10',
            'nombreUsuario' => 'required|string|min:4|unique:users,nombreUsuario',
            'email' => 'required|email|unique:clientes,email',
            'password' => 'required|string|min:8',
        ]);

        // Si la validación falla, devolver errores a Vue
        if ($validator->fails()) {
            return Inertia::render('Cliente/Create', [
                'errors' => $validator->errors()
            ]);
        }

        // Crear el cliente en la base de datos
        Cliente::create([
            'carnetIdentidad' => $request->input('carnetIdentidad'),
            'nombre' => $request->input('nombre'),
            'apellidoPaterno' => $request->input('apellidoPaterno'),
            'apellidoMaterno' => $request->input('apellidoMaterno'),
            'sexo' => $request->input('sexo'),
            'edad' => $request->input('edad'),
            'telefono' => $request->input('telefono'),
            'nombreUsuario' => $request->input('nombreUsuario'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')), // Encriptamos la contraseña
        ]);

        // Redirigir a la lista de clientes con un mensaje de éxito
        return Inertia::render('Cliente/Index', [
            'successMessage' => 'Cliente registrado con éxito'
        ]);
    }
    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id); // Obtener el cliente por ID
        return Inertia::render('Cliente/Edit', [
            'cliente' => $cliente
        ]);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'carnetIdentidad' => 'required|digits_between:8,10|unique:cliente,carnetIdentidad,' . $id,
            'nombre' => 'required|string|min:3|max:30',
            'apellidoPaterno' => 'required|string|min:3|max:30',
            'apellidoMaterno' => 'required|string|min:3|max:30',
            'sexo' => 'required|in:masculino,femenino',
            'telefono' => 'required|digits_between:8,10',
            'edad' => 'required|integer|min:8|max:100',
        ]);

        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->all());

        return redirect()->route('cliente.index')->with('success', 'Cliente actualizado correctamente.');
    }
    public function buscar(Request $request)
    {
        $query = $request->input('query');
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }
        $clientes = Cliente::where('nombre', 'like', "%$query%")
                   ->orWhere('apellidoPaterno', 'like', "%$query%")
                   ->orWhere('telefono', 'like', "%$query%")
                   ->orWhere('carnetIdentidad', 'like', "%$query%") 
                   ->get(['carnetIdentidad', 'nombre', 'apellidoPaterno', 'apellidoMaterno', 'telefono']);
        return response()->json($clientes);
    }


}
