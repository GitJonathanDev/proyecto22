<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TipoUsuarioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EncargadoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\PrecioServicioController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\MembresiaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\VentaClienteController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\VisitaController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReporteVentaController;
use App\Http\Middleware\VisitaPaginaMiddleware;
use App\Http\Middleware\VerificarCliente;
use App\Http\Middleware\VerificarPersonal;
use App\Http\Middleware\EnsureAuthenticated;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuDinamicoController;
use App\Http\Controllers\SearchController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::get('/api/visitas', [VisitaController::class, 'obtenerVisitas'])->name('api.visitas');



Route::middleware([EnsureAuthenticated::class, VisitaPaginaMiddleware::class])->group(function () {
    Route::get('/vista-cliente', function () {
        return Inertia::render('VistaCliente/compracliente');
    })->middleware(VerificarCliente::class)->name('vista-cliente');

    Route::get('/membresias', [MembresiaController::class, 'mostrarMembresias'])
        ->middleware(VerificarCliente::class)
        ->name('mostrar.membresias');

    Route::get('/detalle-compra', function () {
        return Inertia::render('VistaCliente/detallecompracliente');
    })->middleware(VerificarCliente::class)->name('detalle-compra');

    
    Route::get('/venta/index', [VentaClienteController::class, 'index'])->name('ventaCliente.index');
    Route::get('/api/categorias/{codCategoria}/productos', [VentaClienteController::class, 'getProductos'])->name('venta.getProductos');
    Route::get('/api/venta/productos', [VentaClienteController::class, 'obtenerProductos'])->name('venta.obtenerProductos');
    Route::get('/comprar/{idsYCantidades}', [VentaClienteController::class, 'mostrarDetalles'])->name('comprar.detalle');

    Route::post('/venta/create', [VentaClienteController::class, 'store'])->name('comprarprosel');



});
Route::middleware([EnsureAuthenticated::class, VisitaPaginaMiddleware::class, VerificarPersonal::class])->group(function () {
Route::get('/menu-options', [MenuController::class, 'getMenuOptions'])->middleware('auth')->name('menus');

Route::get('/search', [SearchController::class, 'search'])->name('search'); // Ruta para la búsqueda

    
Route::get('/reportes/ventas', [ReporteVentaController::class, 'index'])->name('reportes.ventas');
Route::get('/reportes/compras', [ReporteVentaController::class, 'indexCompras'])->name('reportes.compras');

    Route::post('register', [RegisterController::class, 'store'])->name('register.store');


    Route::prefix('tipoUsuario')->group(function () {
        Route::get('index', [TipoUsuarioController::class, 'index'])->name('tipoUsuario.index');
        Route::get('create', [TipoUsuarioController::class, 'create'])->name('tipoUsuario.create');
        Route::post('store', [TipoUsuarioController::class, 'store'])->name('tipoUsuario.store');
        Route::get('edit/{codTipoUsuario}', [TipoUsuarioController::class, 'edit'])->name('tipoUsuario.edit');
        Route::put('update/{codTipoUsuario}', [TipoUsuarioController::class, 'update'])->name('tipoUsuario.update');
        Route::delete('eliminar/{codTipoUsuario}', [TipoUsuarioController::class, 'destroy'])->name('tipoUsuario.destroy');
        Route::get('buscar', [TipoUsuarioController::class, 'buscarClientes'])->name('tipoUsuario.buscar');
    });

    Route::prefix('usuario')->group(function () {
        Route::get('index', [UsuarioController::class, 'index'])->name('usuario.index');
        Route::get('create', [UsuarioController::class, 'create'])->name('usuario.create');
        Route::post('store', [UsuarioController::class, 'store'])->name('usuario.store');
        Route::get('edit/{codUsuario}', [UsuarioController::class, 'edit'])->name('usuario.edit');
        Route::put('update/{codUsuario}', [UsuarioController::class, 'update'])->name('usuario.update');
        Route::delete('eliminar/{codUsuario}', [UsuarioController::class, 'destroy'])->name('usuario.destroy');
        Route::get('buscar', [UsuarioController::class, 'buscarClientes'])->name('usuario.buscar');
        Route::post('email-ya-existe', [UsuarioController::class, 'emailYaExiste'])->name('email-ya-existe');
    });
    Route::prefix('cliente')->group(function () {
        Route::get('index', [ClienteController::class, 'index'])->name('cliente.index');
        Route::get('create', [ClienteController::class, 'create'])->name('cliente.create');
        Route::post('store', [ClienteController::class, 'store'])->name('cliente.store');
        Route::get('edit/{carnetIdentidad}', [ClienteController::class, 'edit'])->name('cliente.edit');
        Route::put('update/{carnetIdentidad}', [ClienteController::class, 'update'])->name('cliente.update');
        Route::delete('eliminar/{carnetIdentidad}', [ClienteController::class, 'destroy'])->name('cliente.destroy');
        Route::post('ci-ya-existe', [ClienteController::class, 'ciYaExiste'])->name('ci-ya-existe');
        Route::get('buscar', [ClienteController::class, 'index'])->name('clientes.buscar');
        Route::get('seleccionCliente/{carnetIdentidad}', [MembresiaController::class, 'seleccionCliente'])->name('cliente.seleccion');
    });
    Route::get('/permisos', function () {
        return Inertia::render('permisos');
    })->name('permisos');
    Route::prefix('encargado')->group(function () {
        Route::get('index', [EncargadoController::class, 'index'])->name('encargado.index');
        Route::get('create', [EncargadoController::class, 'create'])->name('encargado.create');
        Route::post('store', [EncargadoController::class, 'store'])->name('encargado.store');
        Route::get('edit/{carnetIdentidad}', [EncargadoController::class, 'edit'])->name('encargado.edit');
        Route::put('update/{carnetIdentidad}', [EncargadoController::class, 'update'])->name('encargado.update');
        Route::delete('eliminar/{carnetIdentidad}', [EncargadoController::class, 'destroy'])->name('vendedor.destroy');
        Route::post('ci-ya-existe', [EncargadoController::class, 'ciYaExiste'])->name('ci-ya-existes');
    });

    Route::prefix('categoria')->group(function () {
        Route::get('index', [CategoriaController::class, 'index'])->name('categoria.index');
        Route::get('index2', [CategoriaController::class, 'index2'])->name('categoria.index2');
        Route::get('create', [CategoriaController::class, 'create'])->name('categoria.create');
        Route::post('store', [CategoriaController::class, 'store'])->name('categoria.store');
        Route::get('edit/{codCategoria}', [CategoriaController::class, 'edit'])->name('categoria.edit');
        Route::put('update/{codCategoria}', [CategoriaController::class, 'update'])->name('categoria.update');
        Route::delete('eliminar/{categoria}', [CategoriaController::class, 'destroy'])->name('categoria.destroy');

    });

    Route::prefix('proveedor')->group(function () {
        Route::get('index', [ProveedorController::class, 'index'])->name('proveedor.index');
        Route::get('create', [ProveedorController::class, 'create'])->name('proveedor.create');
        Route::post('store', [ProveedorController::class, 'store'])->name('proveedor.store');
        Route::get('edit/{codProveedor}', [ProveedorController::class, 'edit'])->name('proveedor.edit');
        Route::put('update/{codProveedor}', [ProveedorController::class, 'update'])->name('proveedor.update');
        Route::delete('eliminar/{codProveedor}', [ProveedorController::class, 'destroy'])->name('proveedor.destroy');
    });
    Route::prefix('producto')->group(function () {
        Route::get('index', [ProductoController::class, 'index'])->name('producto.index');
        Route::get('create', [ProductoController::class, 'create'])->name('producto.create');
        Route::post('store', [ProductoController::class, 'store'])->name('producto.store');
        Route::get('edit/{codProducto}', [ProductoController::class, 'edit'])->name('producto.edit');
        Route::put('update/{codProducto}', [ProductoController::class, 'update'])->name('producto.update');
        Route::delete('eliminar/{codProducto}', [ProductoController::class, 'destroy'])->name('producto.destroy');
        Route::get('buscar', [ProductoController::class, 'buscar'])->name('producto.buscar');
    });

    Route::prefix('horario')->group(function () {
        Route::get('index', [HorarioController::class, 'index'])->name('horario.index');
        Route::get('create', [HorarioController::class, 'create'])->name('horario.create');
        Route::post('store', [HorarioController::class, 'store'])->name('horario.store');
        Route::get('edit/{codHorario}', [HorarioController::class, 'edit'])->name('horario.edit');
        Route::put('update/{codHorario}', [HorarioController::class, 'update'])->name('horario.update');
        Route::delete('eliminar/{codHorario}', [HorarioController::class, 'destroy'])->name('horario.destroy');
    });



    // Gestionar servicio
    Route::prefix('servicio')->group(function () {
        Route::get('index', [ServicioController::class, 'index'])->name('servicio.index');
        Route::get('create', [ServicioController::class, 'create'])->name('servicio.create');
        Route::post('store', [ServicioController::class, 'store'])->name('servicio.store');
        Route::get('edit/{codServicio}', [ServicioController::class, 'edit'])->name('servicio.edit');
        Route::put('update/{codServicio}', [ServicioController::class, 'update'])->name('servicio.update');
        Route::delete('eliminar/{codServicio}', [ServicioController::class, 'destroy'])->name('servicio.destroy');
    });
    Route::prefix('precioServicio')->group(function () {
        Route::get('index', [PrecioServicioController::class, 'index'])->name('precioServicio.index');
        Route::get('create', [PrecioServicioController::class, 'create'])->name('precioServicio.create');
        Route::post('store', [PrecioServicioController::class, 'store'])->name('precioServicio.store');
        Route::get('edit/{codPrecioServicio}', [PrecioServicioController::class, 'edit'])->name('precioServicio.edit');
        Route::put('update/{codPrecioServicio}', [PrecioServicioController::class, 'update'])->name('precioServicio.update');
        Route::delete('eliminar/{codPrecioServicio}', [PrecioServicioController::class, 'destroy'])->name('precioServicio.destroy');
    });
    Route::prefix('compra')->group(function () {
        Route::get('index', [CompraController::class, 'index'])->name('compra.index');
        Route::get('create', [CompraController::class, 'create'])->name('compra.create');
        Route::post('store', [CompraController::class, 'store'])->name('compra.store');
        Route::get('{codCompra}', [CompraController::class, 'show'])->name('compra.show');
        Route::get('edit/{codCompra}', [CompraController::class, 'edit'])->name('compra.edit');
        Route::put('update/{codCompra}', [CompraController::class, 'update'])->name('compra.update');
        Route::delete('eliminar/{codCompra}', [CompraController::class, 'destroy'])->name('compra.destroy');
        Route::get('/productos/buscar', [CompraController::class, 'buscarProductos'])->name('productos.buscar');
    });

    Route::prefix('membresia')->group(function() {
        Route::get('index', [MembresiaController::class, 'index'])->name('membresia.index');
        Route::get('/create', [MembresiaController::class, 'create'])->name('membresia.create');
        Route::post('/store', [MembresiaController::class, 'store'])->name('membresia.store');
        Route::get('show/{membresia}', [MembresiaController::class, 'show'])->name('membresia.show');
        Route::delete('destroy/{membresia}', [MembresiaController::class, 'destroy'])->name('membresia.destroy');
    });
    Route::get('/clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscare');
    Route::get('/servicios/buscar', [ServicioController::class, 'buscar'])->name('servicios.buscar');
    Route::get('/servicios/todos', [ServicioController::class, 'servicios']);


    Route::prefix('venta')->group(function () {
        Route::get('/index', [VentaController::class, 'index'])->name('venta.index');
        Route::get('create', [VentaController::class, 'create'])->name('venta.create');
        Route::get('{codVenta}', [VentaController::class, 'show'])->name('venta.show');
        Route::post('/store', [VentaController::class, 'store'])->name('venta.store');
        Route::put('update/{codVenta}', [VentaController::class, 'update'])->name('venta.update');
        Route::delete('destroy/{codVenta}', [VentaController::class, 'destroy'])->name('venta.destroy');
    });
    Route::get('/estadisticas', [EstadisticasController::class, 'index'])->name('encargado');
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');



   // Rutas para Menús
Route::get('/menus', [MenuDinamicoController::class, 'listarMenus'])->name('menus.listar');
Route::post('/menus', [MenuDinamicoController::class, 'crearMenu'])->name('menus.crear');
Route::put('/menus/{codMenu}', [MenuDinamicoController::class, 'actualizarMenu'])->name('menus.actualizar');
Route::delete('/menus/{codMenu}', [MenuDinamicoController::class, 'eliminarMenu'])->name('menus.eliminar');

// Rutas para Opciones
Route::get('/menus/{codMenu}/opciones', [MenuDinamicoController::class, 'listarOpciones'])->name('opciones.listar');
Route::post('/opciones', [MenuDinamicoController::class, 'crearOpcion'])->name('opciones.crear');
Route::put('/opciones/{codOpcion}', [MenuDinamicoController::class, 'actualizarOpcion'])->name('opciones.actualizar');
Route::delete('/opciones/{codOpcion}', [MenuDinamicoController::class, 'eliminarOpcion'])->name('opciones.eliminar');

// Rutas para Permisos
Route::get('/opciones/{codOpcion}/permisos', [MenuDinamicoController::class, 'listarPermisos'])->name('permisos.listar');
Route::post('/permisos', [MenuDinamicoController::class, 'crearPermiso'])->name('permisos.crear');
Route::delete('/permisos/{codPermiso}', [MenuDinamicoController::class, 'eliminarPermiso'])->name('permisos.eliminar');

// Rutas para Tipos de Usuario con Permisos
Route::get('/permisos/{codPermiso}/tiposUsuario', [MenuDinamicoController::class, 'listarTiposUsuario'])->name('tiposUsuario.listar');
Route::post('/permisos/{codPermiso}/tiposUsuario', [MenuDinamicoController::class, 'agregarTipoUsuario'])->name('tiposUsuario.agregar');
Route::delete('/permisos/{codPermiso}/tiposUsuario/{codTipoUsuario}', [MenuDinamicoController::class, 'quitarTipoUsuario'])->name('tiposUsuario.quitar');

    
    
});

Route::post('/login', [CustomLoginController::class, 'login'])->name('logine');
Route::post('/logout', [CustomLoginController::class, 'logout'])->name('logout');

