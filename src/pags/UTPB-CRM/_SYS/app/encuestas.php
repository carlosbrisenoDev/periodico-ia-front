<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class encuestas extends Model
{
  protected $guarded = [];
  protected $table = "encuestas";

  public function preguntas(){
    return $this->hasMany('\App\preguntas','encuesta_id','id');
  }
  public function cliente_encuesta(){
    return $this->hasOne('\App\cliente_encuesta','encuesta_id','id');
  }
}
