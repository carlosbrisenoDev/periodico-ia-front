<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class preguntas extends Model
{
  protected $guarded = [];
  protected $table = "preguntas";

  public $cliente_encuesta_id = 0;

  public function respuesta(){
    return $this->hasOne('\App\respuestas','pregunta_id',"id")->where("cliente_encuesta_id",$this->cliente_encuesta_id);
  }
  public function encuesta(){
    return $this->hasOne('\App\encuestas','id',"encuesta_id");
  }
}
