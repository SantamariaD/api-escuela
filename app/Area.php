<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'areas';
    protected $fillable = [
      'id', 'nombre','imagen','descipcion','created_at','updated_at'
    ];

    public function carreras(){
      return $this->hasMany(Carrera::class);
    }
}
