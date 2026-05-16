<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class frecuente extends Model
{
  protected $table = "frecuentes";
  protected $guarded = [];

  public function usuario(){
    return $this->hasOne('\App\User','id','usuario_id');
  }

}
