<?php

namespace App\Models;

use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\NewAccessToken;
use Illuminate\Support\Str;
use App\Models\VehiculoModel;

class UsuarioModel extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'Usuario';

    protected $fillable = [
        'nombre_usuario',
        'contrasena',
        'correo',
        'telefono',
        'datos_pago',
        'facturacion',
        '__v'
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected function asJson($value)
    {
        if (is_array($value) || is_object($value)) {
            return $value;
        }
        return parent::asJson($value);
    }

    public function setNombreUsuarioAttribute($value)
    {
        $this->attributes['nombre_usuario'] = is_array($value) ? $value : (array)$value;
    }

    public function setCorreoAttribute($value)
    {
        $this->attributes['correo'] = is_array($value) ? $value : [$value];
    }

    public function setTelefonoAttribute($value)
    {
        $this->attributes['telefono'] = is_array($value) ? $value : [$value];
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, ['nombre_usuario', 'correo', 'telefono', 'datos_pago', 'facturacion'])) {
            if (is_string($value)) {
                $value = json_decode($value, true);
            }
            $this->attributes[$key] = $value;
            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    protected $appends = ['id_str'];

    public function getIdStrAttribute()
    {
        return (string) $this->_id;
    }

    public function tokens()
    {
        return $this->morphMany(\Laravel\Sanctum\PersonalAccessToken::class, 'tokenable');
    }

    public function createToken(string $name, array $abilities = ['*'])
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(40)),
            'abilities' => $abilities,
        ]);

        return new NewAccessToken($token, $plainTextToken);
    }

    public function vehiculos()
{
    return $this->hasMany(VehiculoModel::class, 'propietario');
}

}
