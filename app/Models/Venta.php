<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;


    protected $table = 'Venta';


    protected $primaryKey = 'codVenta';


    public $incrementing = true;


    protected $keyType = 'int';


    protected $fillable = [
        'fechaVenta',
        'montoTotal',
        'codEncargadoF',
        'codClienteF',
        'codPagoF',
    ];


    protected $casts = [
        'fechaVenta' => 'date',
        'montoTotal' => 'float',
        'codEncargadoF' => 'integer',
        'codClienteF' => 'integer',
        'codPagoF' => 'integer',
    ];


    public function encargado()
    {
        return $this->belongsTo(Encargado::class, 'codEncargadoF', 'carnetIdentidad');
    }


    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'codClienteF', 'carnetIdentidad');
    }


    public function pago()
    {
        return $this->belongsTo(Pago::class, 'codPagoF', 'codPago');
    }
    
    public function detalleVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'codVenta', 'codVenta');
    }

    public $timestamps = false;
}
