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

    public function create()
    {
        $productos = Producto::all(); 
        // $encargado = auth()->user();
        $encargado = Encargado::where('carnetIdentidad', '12454859')->first();
        $categoria = Categoria::all();
        $clientes = Cliente::all();

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
        'codClienteF' => 'required|exists:cliente,carnetIdentidad',
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

