<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoUsuarioPermiso extends Model
{
    use HasFactory;

    protected $table = 'tipoUsuarioPermiso'; 
    public $incrementing = false; // Indicar que no hay auto-incremento
    public $timestamps = true; // Si las migraciones tienen timestamps

    protected $fillable = ['codTipoUsuarioF', 'codPermisoF']; // Especifica campos que se pueden rellenar

    // Deshabilitar la clave primaria predeterminada
    protected $primaryKey = null;

    /**
     * Relación con TipoUsuario
     */
    public function tipoUsuario()
    {
        return $this->belongsTo(TipoUsuario::class, 'codTipoUsuarioF', 'codTipoUsuario');
    }

    /**
     * Relación con Permiso
     */
    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'codPermisoF', 'codPermiso');
    }
}
