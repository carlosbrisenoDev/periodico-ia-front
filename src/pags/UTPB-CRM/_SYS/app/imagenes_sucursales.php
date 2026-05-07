<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class imagenes_sucursales extends Model
{
  protected $table = "imagenes_sucursales";
  protected $guarded = [];

  public function imagen(){
    return $this->hasOne("\App\imagen","id","imagen_id");
  }

}
