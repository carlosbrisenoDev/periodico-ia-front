<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class estado extends Model
{
  protected $table = "estados_reportes";
  protected $guarded = [];

  public function reportes()
  {
    return $this->hasMany('\App\reporte','estado_id','id');
  }
}
