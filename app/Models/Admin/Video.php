<?php

// Video.php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Activities\Categoria;
use App\Models\Admin\Activities\Subcategoria;
use App\Models\Admin\Like;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo', 
        'descripcion', 
        'campus', 
        'categoria_id', 
        'subcategoria_id', 
        'visibilidad', 
        'archivo_video', 
        'likes',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
