<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table = 'cursos';
    protected $fillable = [
      'id_curso', 'area_id', 'nombre','carrera', 'profesor_id','imagen', 'calificacion', 'duracion','descipcion','created_at','updated_at'
    ];
}