<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Video;
use App\Models\Usuario;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'video_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class); // Usa la clase Video correctamente importada
    }
}
