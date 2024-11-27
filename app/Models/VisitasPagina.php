<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitasPagina extends Model
{
    use HasFactory;

    // La tabla asociada al modelo
    protected $table = 'visitaPagina';

    // Clave primaria compuesta
    protected $primaryKey = ['codUsuario', 'codPagina'];
    public $incrementing = false; // No usar auto-incremento para claves compuestas

    // Tipo de clave primaria compuesta
    protected $keyType = 'string'; // Asegúrate de que es el tipo correcto, 'string' si es una cadena

    // Definir los campos que son asignables
    protected $fillable = [
        'codUsuario',
        'codPagina',
        'cantidad',
    ];

    // Relación con usuarios
    public function usuario()
    {
        return $this->belongsTo(user::class, 'codUsuario', 'codUsuario');
    }

    // Relación con páginas
    public function pagina()
    {
        return $this->belongsTo(Pagina::class, 'codPagina', 'id');
    }

    // Método para obtener un registro basado en las claves compuestas
    public static function getByCodUsuarioAndCodPagina($codUsuario, $codPagina)
    {
        return self::where('codUsuario', $codUsuario)
                   ->where('codPagina', $codPagina)
                   ->first();
    }

    // Sobrescribir el método `save` para manejar claves compuestas
    public function setKeysForSaveQuery($query)
    {
        return $query->where('codUsuario', $this->codUsuario)
                     ->where('codPagina', $this->codPagina);
    }

    // Mejorando la creación o actualización de visitas
    public static function incrementaVisita($codUsuario, $codPagina)
    {
        // Buscar si ya existe una visita para esa combinación
        $registro = self::firstOrCreate(
            ['codUsuario' => $codUsuario, 'codPagina' => $codPagina],
            ['cantidad' => 0] // Si no existe, lo inicializamos con 0
        );

        // Incrementamos la cantidad
        $registro->cantidad++;
        $registro->save();

        return $registro; // Retorna el registro actualizado
    }
}
