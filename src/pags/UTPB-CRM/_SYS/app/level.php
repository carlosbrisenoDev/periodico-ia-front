<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class level extends Model
{
  protected $guarded = [];

  public function reportes()
  {
    return $this->hasMany('\App\reporte','level_id','id');
  }
  public function formatos()
  {
    return $this->hasMany('\App\formato','level_id');
  }
  public function usuarios()
  {
    return $this->hasMany('\App\User','level_id');
  }
}
