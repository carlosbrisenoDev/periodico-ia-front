<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pagos extends Model
{
  protected $guarded = [];
  protected $table = "pagos";

  public function tabla(){
    return $this->hasOne('\App\tablapagos','id','tabla_id');
  }
  public function documento(){
    return $this->hasOne('\App\documento','id','pagado');
  }
}
