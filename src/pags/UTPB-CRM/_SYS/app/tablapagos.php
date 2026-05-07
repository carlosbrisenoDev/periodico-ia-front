<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tablapagos extends Model
{
  protected $guarded = [];
  protected $table = "tablapagos";

  public function pagos(){
    return $this->hasMany('\App\pagos','tabla_id','id')->orderBy("id","asc");
  }
  public function pagados(){
    return $this->hasMany('\App\pagos','tabla_id','id')->where("status",1)->orderBy("id","asc");
  }
  public function nopagados(){
    return $this->hasMany('\App\pagos','tabla_id','id')->where("pagado",NULL)->whereRAW("str_to_date(concat(anio,'-',mes_en,'-1'),'%Y-%b-%d') < date('".\Carbon\carbon::now()->format("Y-m-d")."')")->orderBy("id","asc");
  }
  public function cartera(){
    return $this->hasOne('\App\cartera','id','cartera_id');
  }
  public function cliente(){
    return $this->hasOne('\App\cliente','id','cliente_id');
  }
}
