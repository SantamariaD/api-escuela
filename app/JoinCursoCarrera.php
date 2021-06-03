<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinCursoCarrera extends Model
{
    protected $table = 'join_cursos_carrera';
    protected $fillable = [
        'curso_id', 'carrera_id', 'nombre_curso','nombre_carrera',
    ];
}
