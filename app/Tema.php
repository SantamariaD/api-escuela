<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tema extends Model
{
    protected $table = 'temas';
    protected $fillable = [
        'curso_id', 'numero', 'nombre',
    ];

    public function videos(){
        return $this->hasMany(Videos::class);
      }
}
