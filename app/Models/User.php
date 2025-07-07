<?php

namespace App\Models;

use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\NewAccessToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @method \Laravel\Sanctum\NewAccessToken createToken(string $name)
 */
class User extends Authenticatable
{
    protected $connection = 'mongodb';
    use HasApiTokens, HasFactory, Notifiable;
   public function tokens()
{
    return $this->morphMany(MongoSanctum::class, 'tokenable');
}

    protected $fillable = [
        'username',
        'tipo',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['id_str'];

    public function getIdStrAttribute() {
        return (string) $this->_id;
    }

    public function createToken(string $name, array $abilities = ['*'])
{
    $token = $this->tokens()->create([
        'name' => $name,
        'token' => hash('sha256', $plainTextToken = Str::random(40)),
        'abilities' => $abilities,
        'tokenable_type' => get_class($this), 
    ]);

    return new NewAccessToken($token, $plainTextToken);
}
}
