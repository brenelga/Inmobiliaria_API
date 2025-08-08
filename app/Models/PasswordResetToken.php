<?php
namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'password_reset_tokens';
    
    protected $fillable = [
        'email',
        'token',
        'created_at',
        'user_id'
    ];
    
    protected $dates = ['created_at'];
    
    public $timestamps = false;
}