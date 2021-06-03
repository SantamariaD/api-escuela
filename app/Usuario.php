<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $fillable = [
        'nombre','id', 'email', 'contrasena', 'usuario', 'apellido', 'sexo','estudios', 'nacionalidad', 'pais',
    ];

    protected $hidden = [
        'contrasena',
    ];
}
