<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';
    public $timestamps = false;
    protected $fillable = ['descNino', 'descMiercoles', 'descSocio'];
}