<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $primaryKey = 'codUsuario'; 
    protected $keyType = 'int';
    public $incrementing = true; 
    protected $fillable = [
        'name',
        'email',
        'password',
        'estadoBloqueado',
        'codTipoUsuarioF', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];
    public function tipoUsuario()
    {
        return $this->belongsTo(TipoUsuario::class, 'codTipoUsuarioF'); 
    }
    public function encargado()
    {
        return $this->hasOne(Encargado::class, 'codUsuarioF', 'codUsuario');
    }
    protected $appends = [
        'profile_photo_url',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
