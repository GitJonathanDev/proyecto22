<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VentaClienteController extends Controller
{
    public function index(Request $request)
    {
        $criterio = $request->get('criterio', 'nombre');
        $buscar = $request->get('buscar', '');

        $productos = Producto::query()
            ->when($buscar, function ($query) use ($criterio, $buscar) {
                return $query->where($criterio, 'like', "%{$buscar}%");
            })
            ->paginate(10);

        return Inertia::render('RealizarVenta/Index', [
            'productos' => $productos,
            'criterio' => $criterio,
            'buscar' => $buscar,
        ]);
    }

    public function obtenerProductos(Request $request)
    {
        $productos = Producto::all();
        return response()->json($productos);
    }

    public function mostrarDetalles(Request $request)
    {
        $items = explode(',', $request->idsYCantidades);
        $ids = [];
        $cantidades = [];

        foreach ($items as $item) {
            [$id, $cantidad] = explode(':', $item);
            $ids[] = $id;
            $cantidades[$id] = $cantidad;
        }

        $productos = Producto::whereIn('codProducto', $ids)->get();

        $cliente = Cliente::where('codUsuarioF', Auth::user()->codUsuario)->first();

        if ($productos->isEmpty()) {
            abort(404, "No se encontraron productos para procesar.");
        }

        return Inertia::render('VistaCliente/detallecompracliente', [
            'productos' => $productos,
            'cantidades' => $cantidades,
            'cliente' => $cliente,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $cliente = Cliente::where('codUsuarioF', $user->codUsuario)->first();

        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado.'], 404);
        }

        $idCliente = $cliente->carnetIdentidad;
        $montoTotal = array_reduce($request->productos, function ($total, $producto) {
            return $total + $producto['cantidad'] * $producto['precio'];
        }, 0);

        $montoPago = $request->tnMonto ?? $montoTotal;

        $pago = Pago::create([
            'monto' => $montoPago,
            'fechaPago' => now()->toDateString(),
            'codClienteF' => $idCliente,
        ]);

        $venta = Venta::create([
            'fechaVenta' => now()->toDateString(),
            'montoTotal' => $montoTotal,
            'codClienteF' => $idCliente,
            'codEncargadoF' => 11111111,
            'codPagoF' => $pago->codPago,
        ]);

        foreach ($request->productos as $producto) {
            DetalleVenta::create([
                'codVenta' => $venta->codVenta,
                'codProducto' => $producto['codProducto'],
                'cantidad' => $producto['cantidad'],
                'precioV' => $producto['precio'],
            ]);
        }

        return response()->json([
            'success' => 'Compra realizada con éxito.',
            'redirect' => route('vista-cliente'), 
        ]);
    }
}
