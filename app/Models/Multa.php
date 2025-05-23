<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model;

class Multa extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'Multas';
    protected $fillable = ['mensaje', 'departamento_id', 'usuario_id', 'fecha', 'monto', 'status'];
}