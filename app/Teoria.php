<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teoria extends Model
{
    protected $table = 'teoria';
    protected $fillable = [
      'video_id', 'titulo', 'informacion','archivo',
    ];
}
