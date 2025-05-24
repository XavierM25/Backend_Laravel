<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Usuario;
use App\Models\Admin\Activities\Categoria;
use App\Models\Admin\Activities\Subcategoria;

class Habilidades extends Model
{
    use HasFactory;

    protected $fillable = ['usuario_id', 'categoria_id', 'subcategoria_id', 'puntos', 'estado'];

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
}

