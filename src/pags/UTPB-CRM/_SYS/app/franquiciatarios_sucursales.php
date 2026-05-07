<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class franquiciatarios_sucursales extends Model
{
  protected $table = "franquiciatarios_sucursales";
  protected $guarded = [];

  public function sucursal(){
    return $this->hasOne("\App\sucursal","id","sucursal_id");
  }
  public function usuario(){
    return $this->hasOne("\App\User","id","usuario_id");
  }
}
