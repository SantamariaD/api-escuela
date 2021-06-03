<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinCursoUsuario extends Model
{
    protected $table = 'join_cursos_usuarios';
    protected $fillable = [
        'id_usuario', 'id_curso', 'nombre_usuario','nombre_curso',
    ];
}
