<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'Producto';

    // Clave primaria
    protected $primaryKey = 'codProducto';

    // Clave no incremental
    public $incrementing = false;

    // Atributos que se pueden asignar en masa
    protected $fillable = [
        'codProducto', 
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'imagen_url', 
        'codCategoriaF',
    ];

    // Conversión de atributos
    protected $casts = [
        'codProducto' => 'string', // Garantiza que siempre se trate como string
        'precio' => 'float',
        'stock' => 'integer',
        'codCategoriaF' => 'integer', 
    ];

    // Relación con Categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'codCategoriaF', 'codCategoria'); 
    }

    // Relación con DetalleVenta
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'codProducto', 'codProducto');
    }

    // Desactiva las marcas de tiempo
    public $timestamps = false;
}
