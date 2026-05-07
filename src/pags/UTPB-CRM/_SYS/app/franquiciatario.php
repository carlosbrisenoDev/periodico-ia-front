<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class franquiciatario extends Model
{
  protected $table = "franquiciatarios";
  protected $guarded = [];

  public function usuario(){
    return $this->hasOne('\App\User','id','usuario_id');
  }
  public function sucursal(){
    return $this->hasOne('\App\sucursal','franquiciatario_id','id');
  }
  public function suspendido(){
    return ($this->status == 4);
  }
}
