<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sucursal extends Model
{
    protected $guarded = [];
    protected $table = "sucursales";

    public function franquicia(){
      return $this->hasOne('\App\franquiciatario','id','franquiciatario_id');
    }
    public function franquiciatarios(){
      return $this->hasMany('\App\franquiciatarios_sucursales','sucursal_id','id');
    }
    public function usuario(){
      return $this->hasOne('\App\User','id','franquiciatario_id');
    }
    public function imagenes(){
      return $this->hasMany("\App\imagenes_sucursales","sucursal_id","id");
    }
}
