<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class credito_info extends Model
{
  protected $guarded = [];
  protected $table = "credito_info";

  public function familiares(){
    return $this->hasMany('\App\familiares',"credito_info_id","id");
  }

  public function cliente(){
    return $this->hasOne('\App\cliente','id','cliente_id');
  }
}
