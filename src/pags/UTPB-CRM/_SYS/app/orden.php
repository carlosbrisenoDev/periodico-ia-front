<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class orden extends Model
{
  protected $table = "ordenes";
  protected $guarded = [];

  public function usuario(){
    return $this->hasOne('\App\User','id','usuario_id');
  }

  public function estado(){
    return $this->hasOne('\App\estado_pedido','id','status');
  }
  public function direccion(){
    return $this->hasOne('\App\direccion','id','direccion_id');
  }
  public function sucursal(){
    return $this->hasOne('\App\sucursal','id','sucursal_id');
  }

}
