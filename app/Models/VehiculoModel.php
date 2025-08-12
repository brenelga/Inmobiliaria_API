<?php

namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model;


class VehiculoModel extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'Vehiculo';

    protected $fillable = [
        'marca',
        'modelo',
        'placas',
        'color',
        'anio',
        'vin',
        'propietario'
    ];

    protected $casts = [
        'anio' => 'integer'
    ];
}