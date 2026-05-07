<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class direccion extends Model
{
  protected $table = "direcciones";
  protected $guarded = [];

  public function usuario(){
    return $this->hasOne('\App\User','id','usuario_id');
  }

}
