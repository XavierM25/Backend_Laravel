<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Usuario;
use App\Models\Admin\Activities\Categoria;
use App\Models\Admin\Activities\Subcategoria;
use App\Models\Admin\Activities\Horario;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';

    protected $fillable = [
        'usuario_id', 
        'categoria_id', 
        'subcategoria_id', 
        'horario_id', 
        'estado', 
        'numero_vacante', 
        'comentarios'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }
}
