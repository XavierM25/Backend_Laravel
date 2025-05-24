<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Usuario;
use App\Models\Admin\Activities\Subcategoria;

class Ranking extends Model
{
    use HasFactory;

    protected $fillable = ['usuario_id', 'subcategoria_id'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }
}

