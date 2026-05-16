<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cartera extends Model
{
  protected $guarded = [];
  protected $table = "cartera";

  public function cliente(){
    return $this->hasOne('\App\cliente','id','cliente_id');
  }
  public function hasFirma(){
    return $this->hasOne('\App\firma','cartera_id','id');
  }
  public function tablapagos(){
    return $this->hasOne('\App\tablapagos','cartera_id','id');
  }
}
