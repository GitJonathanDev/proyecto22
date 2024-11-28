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
        // Obtener la compra con sus relaciones de proveedor y encargado
        $compra = Compra::with(['proveedor', 'encargado'])->findOrFail($codCompra);
    
        // Obtener los detalles de la compra con los productos
        $detalleCompra = DetalleCompra::with('producto')->where('codCompra', $codCompra)->get();
    
        // Extraer los códigos de los productos comprados
        $codProductos = $detalleCompra->pluck('producto.codProducto'); 
    
        // Usar dd() para mostrar los códigos de los productos comprados
        dd($codProductos);
    
        // Renderizar la vista si no se detiene la ejecución
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