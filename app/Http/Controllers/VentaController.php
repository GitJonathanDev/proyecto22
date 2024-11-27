<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Pago;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\DetalleVenta;
use App\Models\Cliente;
use App\Models\Encargado;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $ventas = Venta::with(['cliente', 'encargado']) 
            ->when($request->buscar, function ($query) use ($request) {
                $criterio = $request->criterio ?: 'fechaVenta'; 
                return $query->where($criterio, 'like', '%' . $request->buscar . '%');
            })
            ->paginate(5); 

        return Inertia::render('Venta/Index', [
            'ventas' => $ventas
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
    
        // Obtener los datos necesarios para la vista
        $productos = Producto::all();
        $categoria = Categoria::all();
        $clientes = Cliente::all();
    
        // Renderizar la vista con Inertia
        return Inertia::render('Venta/Create', [
            'productos' => $productos,
            'encargado' => $encargado,
            'clientes' => $clientes,
            'categoria' => $categoria
        ]);
    }
    

    public function store(Request $request)
    {
        // Validar los datos de la venta
        $request->validate([
            'fechaVenta' => 'required|date',
            'productosSeleccionados' => 'required|json',
            'totalVenta' => 'required|numeric',
            'codClienteF' => 'required|exists:Cliente,carnetIdentidad',
        ]);
    
        // Obtener el encargado relacionado con el usuario autenticado
        $user = $request->user();
        $encargado = $user->encargado; // Asumiendo que tienes una relación definida en el modelo User
    
        if (!$encargado) {
            return response()->json(['error' => 'El usuario no tiene un encargado asociado.'], 403);
        }
    
        $venta = new Venta();
        $venta->fechaVenta = $request->fechaVenta;
        $venta->codEncargadoF = $encargado->carnetIdentidad; // Usar el carnetIdentidad del encargado autenticado
        $venta->codClienteF = $request->codClienteF;
        $venta->montoTotal = $request->totalVenta;
    
        // Registrar el pago
        $pago = new Pago();
        $pago->fechaPago = now();
        $pago->monto = $request->totalVenta;
        $pago->estado = 'pagado';
        $pago->codClienteF = $request->codClienteF;
        $pago->save();
    
        // Asociar el pago a la venta
        $venta->codPagoF = $pago->codPago;
        $venta->save();
    
        // Registrar detalles de la venta
        $productosSeleccionados = json_decode($request->productosSeleccionados);
    
        // Asegurarse de que cada id de producto sea tratado como una cadena
        $productosSeleccionados = array_map(function ($producto) {
            return (string) $producto->id; // Convierte el id a cadena
        }, $productosSeleccionados);
    
        foreach ($productosSeleccionados as $productoId) {
            // Buscar el producto por su código (como cadena)
            $producto = Producto::where('codProducto', $productoId)->first();
    
            if ($producto) {
                $detalleVenta = new DetalleVenta();
                $detalleVenta->codVenta = $venta->codVenta;
                $detalleVenta->codProducto = $producto->codProducto;
                $detalleVenta->cantidad = $producto->cantidad; // Asegúrate de tener este dato en el objeto producto
                $detalleVenta->precioV = $producto->precio; // Asegúrate de tener este dato en el objeto producto
                $detalleVenta->save();
            }
        }
    
        // Redirigir a la página de la venta con Inertia
        return Inertia::location(route('venta.show', $venta->codVenta));
    }
    

    // Mostrar detalles de una venta
    public function show($codVenta)
    {
        $venta = Venta::with(['cliente', 'encargado'])->findOrFail($codVenta);
        $detalleVenta = DetalleVenta::with('producto')->where('codVenta', $codVenta)->get();
        $pago = Pago::where('codPago', $venta->codPagoF)->first();
        return Inertia::render('Venta/Detalle', [
            'venta' => $venta,
            'detalleVenta' => $detalleVenta,
            'pago' => $pago
        ]);
    }

    public function update(Request $request, $codVenta)
    {
        $request->validate([
            'fechaVenta' => 'required|date',
            'montoTotal' => 'required|numeric',
        ]);

        $venta = Venta::findOrFail($codVenta);
        $venta->update([
            'fechaVenta' => $request->fechaVenta,
            'montoTotal' => $request->montoTotal,
        ]);

        return redirect()->route('venta.index')->with('success', 'Venta actualizada exitosamente.');
    }

    // Eliminar una venta
    public function destroy($codVenta)
    {
        $venta = Venta::findOrFail($codVenta);
        $venta->delete();

        return redirect()->route('venta.index')->with('delete', 'Venta eliminada exitosamente.');
    }
}

