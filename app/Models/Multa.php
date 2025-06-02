<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Multa extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'Multas';
    protected $fillable = ['mensaje', 'departamento_id', 'usuario_id', 'fecha', 'monto', 'status', 'read'];
}