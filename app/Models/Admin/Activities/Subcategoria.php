<?php

namespace App\Models\Admin\Activities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'categoria_id', 'vacantes'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}