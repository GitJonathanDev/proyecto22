<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Encargado;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $criterio = $request->input('criterio', 'fechaCompra');
        $buscar = $request->input('buscar', '');
        $compras = Compra::with(['proveedor', 'encargado'])
            ->when($buscar, function ($query) use ($criterio, $buscar) {
                return $query->where($criterio, 'like', "%$buscar%");
            })
            ->paginate(5);
        return inertia('Compra/Index', [
            'compras' => $compras,
            'deleteMessage' => session('delete'),
        ]);
    }
    public function create(Request $request)
    {
        // Obtener el usuario autenticado
        $user = $request->user();
    
        // Obtener el encargado relacionado con el usuario autenticado
        $encargado = $user->encargado; // Usar la relación definida en el modelo User
    
        if (!$encargado) {
            // Si no existe un encargado relacionado, devolver un error
            return response()->json(['error' => 'El usuario no tiene un encargado asociado.'], 403);
        }
        $proveedores = Proveedor::all();
        $productos = Producto::with('categoria')->get();

        return Inertia::render('Compra/Create', [
             'encargado' => $encargado,
            'proveedores' => $proveedores,
            'productos' => $productos,
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'fechaCompra' => 'required|date',
            'codEncargadoF' => 'required|exists:Encargado,carnetIdentidad',
            'codProveedorF' => 'required|exists:Proveedor,codProveedor',
            'productosSeleccionados' => 'required|json',
            'totalCompra' => 'required|numeric', 
        ]);
        $productosSeleccionados = json_decode($request->productosSeleccionados);

        // Calcular el monto total de la compra
        $montoTotal = 0;
        foreach ($productosSeleccionados as $producto) {
            $montoTotal += $producto->precio * $producto->cantidad;
        }
        // Crear la compra
        $compra = new Compra();
        $compra->fechaCompra = $request->fechaCompra;
        $compra->codEncargadoF = $request->codEncargadoF;
        $compra->codProveedorF = $request->codProveedorF;
        $compra->montoTotal = $request->totalCompra;
        $compra->save();

        // Registrar los detalles de la compra
        foreach ($productosSeleccionados as $producto) {
            $detalleCompra = new DetalleCompra();
            $detalleCompra->codCompra = $compra->codCompra;
            $detalleCompra->codProducto = $producto->id;
            $detalleCompra->cantidad = $producto->cantidad;
            $detalleCompra->precioC = $producto->precio;
            $detalleCompra->save();
        }
        return Inertia::render('Compra/Detalle', [
            'compra' => $compra,
            'detalleCompra' => $compra->detalleCompra, 
        ]);
    }
    public function show($codCompra)
{
    // Asegurarse de que el valor de $codCompra es un string
    $codCompra = (string) $codCompra;

    // Buscar la compra con sus relaciones
    $compra = Compra::with(['proveedor', 'encargado'])->findOrFail($codCompra);

    // Obtener los detalles de la compra
    $detalleCompra = DetalleCompra::where('codCompra', $codCompra)
        ->get()
        ->map(function ($detalle) {
            // Asegurarse que el codProducto sea tratado como un string
            $detalle->codProducto = (string) $detalle->codProducto;

            // Cargar la relación con Producto y aplicar CAST a codProducto
            $detalle->producto = Producto::whereRaw('CAST("codProducto" AS VARCHAR) = ?', [$detalle->codProducto])
                ->first();

            return $detalle;
        });

    // Renderizar la vista con Inertia
    return Inertia::render('Compra/Detalle', [
        'compra' => $compra,
        'detalleCompra' => $detalleCompra
    ]);
}

    


    public function buscarProductos(Request $request)
    {
        $query = $request->input('nombreProducto', '');
        $productos = Producto::where('nombre', 'like', "%$query%")->get();
        return response()->json($productos);
    }

}