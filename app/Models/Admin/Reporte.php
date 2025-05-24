<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'modulo', 'formato', 'fecha_inicio', 'fecha_fin', 'ordenar_por', 'orden', 'filtros', 'file_path'
    ];
    
}

