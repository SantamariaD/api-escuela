<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinCursoProfesor extends Model
{
    protected $table = 'join_curso_profesores';
    protected $fillable = [
        'curso_id', 'profesor_id', 'area_id',
    ];
}
