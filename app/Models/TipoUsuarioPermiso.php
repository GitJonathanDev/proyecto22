<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoUsuarioPermiso extends Model
{
    use HasFactory;

    protected $table = 'tipoUsuarioPermiso'; 
    public $incrementing = false; 
    public $timestamps = true; 

    protected $fillable = ['codTipoUsuarioF', 'codPermisoF']; 

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
