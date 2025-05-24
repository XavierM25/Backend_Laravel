<?php

namespace App\Models\Admin\Activities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = ['subcategoria_id', 'dia', 'hora_inicio', 'hora_fin'];

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }
}
