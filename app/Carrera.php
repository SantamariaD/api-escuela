<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carreras';
    protected $fillable = [
      'id', 'area_id', 'nombre','imagen','descipcion','created_at','updated_at'
    ];

    public function cursos(){
      return $this->hasMany(Curso::class);
    }
    
}
