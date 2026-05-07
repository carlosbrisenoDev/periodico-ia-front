<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cliente_encuesta extends Model
{
  protected $guarded = [];
  protected $table = "cliente_encuestas";

  public function encuesta(){
    return $this->hasOne('\App\encuestas','id','encuesta_id');
  }
  public function cliente(){
    return $this->hasOne('\App\cliente','id','cliente_id');
  }
  public function set_cliente_encuesta_id($id){
    $this->cliente_encuesta_id = $id;
  }
}
