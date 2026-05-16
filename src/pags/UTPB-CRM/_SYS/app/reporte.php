<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class reporte extends Model
{
  protected $guarded = [];
  public function level()
  {
    return $this->hasOne('\App\level','id','level_id');
  }
  public function prioridad()
  {
    return $this->hasOne('\App\prioridad','id','prioridad_id');
  }
  public function estado()
  {
    return $this->hasOne('\App\estado','id','estado_id');
  }
  public function ciudadano()
  {
    return $this->hasOne('\App\ciudadano','id','ciudadano_id');
  }
  public function usuario()
  {
    return $this->hasOne('\App\user','id','user_id');
  }
  public function documentos()
  {
    return $this->hasMany('\App\documento','reporte_id','id');
  }
  public function full_fecha()
  {
    setlocale(LC_ALL, 'Spanish');
    return \Carbon\Carbon::parse($this->created_at)->formatLocalized('%d %B %Y');
  }
}
