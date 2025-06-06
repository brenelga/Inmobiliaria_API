<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Multa extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'Multas';

    protected $fillable = [
        'departamento_id',
        'mensaje',
        'monto',
        'fecha',
        'status',
        'read',
    ];

    protected $appends = ['id_str'];

    public function getIdStrAttribute()
    {
        return (string) $this->_id;
    }
}
