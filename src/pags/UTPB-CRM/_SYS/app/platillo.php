<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class platillo extends Model
{
  protected $table = "platillos";
  protected $guarded = [];

  public function categoria(){
    return $this->hasOne("\App\categoria","id","categoria_id");
  }
  public function imagenes(){
    return $this->hasMany("\App\imagenes_platillos","platillo_id","id");
  }
}
