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

    foreach ($productosSeleccionados as $producto) {
        $detalleVenta = new DetalleVenta();
        $detalleVenta->codVenta = $venta->codVenta;
        $detalleVenta->codProducto = $producto->id;
        $detalleVenta->cantidad = $producto->cantidad;
        $detalleVenta->precioV = $producto->precio;
        $detalleVenta->save();
    }

    // Redirigir a la página de la venta con Inertia
    return Inertia::location(route('venta.show', $venta->codVenta));
}

public function show($codVenta)
{
    // Asegurarse de que el valor de $codVenta sea un string
    $codVenta = (string) $codVenta;

    // Buscar la venta con sus relaciones
    $venta = Venta::with(['cliente', 'encargado'])->findOrFail($codVenta);

    // Obtener los detalles de la venta y cargar la relación con el producto
    $detalleVenta = DetalleVenta::where('codVenta', $codVenta)
        ->get()
        ->map(function ($detalle) {
            // Asegurarse de que el codProducto sea tratado como un string
            $detalle->codProducto = (string) $detalle->codProducto;

            // Cargar la relación con Producto y aplicar CAST a codProducto
            $detalle->producto = Producto::whereRaw('CAST("codProducto" AS VARCHAR) = ?', [$detalle->codProducto])
                ->first();

            return $detalle;
        });

    // Obtener el pago asociado a la venta
    $pago = Pago::where('codPago', $venta->codPagoF)->first();

    // Renderizar la vista con Inertia
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

