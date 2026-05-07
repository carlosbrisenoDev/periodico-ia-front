<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class imagenes_platillos extends Model
{
  protected $table = "imagenes_platillos";
  protected $guarded = [];

  public function imagen(){
    return $this->hasOne("\App\imagen","id","imagen_id");
  }

}
