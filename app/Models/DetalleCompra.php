<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'DetalleCompra';

    // Desactiva las claves primarias por defecto
    protected $primaryKey = null;

    // Clave no incremental
    public $incrementing = false;

    // Desactiva las marcas de tiempo
    public $timestamps = false;

    // Atributos que se pueden asignar en masa
    protected $fillable = [
        'precioC',
        'cantidad',
        'codCompra',
        'codProducto',
    ];

    // Conversión de atributos
    protected $casts = [
        'codProducto' => 'string', // Garantiza que siempre se trate como string
        'precioC' => 'float',
        'cantidad' => 'integer',
        'codCompra' => 'integer',
    ];

    // Relación con Compra
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'codCompra', 'codCompra');
    }

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'codProducto', 'codProducto');
    }

    // Sobreescribe el método `getKey` para manejar la clave primaria compuesta
    public function getKey()
    {
        return [$this->codCompra, $this->codProducto];
    }

    // Define un método para encontrar registros usando claves compuestas
    public static function findComposite($codCompra, $codProducto)
    {
        return self::where('codCompra', $codCompra)
            ->where('codProducto', $codProducto)
            ->first();
    }
}
