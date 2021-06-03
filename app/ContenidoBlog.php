<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContenidoBlog extends Model
{
    protected $table = 'contenido_blog';
    protected $fillable = [
        'id_blog', 'titulo_contenido', 'contenido','imagen',
    ];
}