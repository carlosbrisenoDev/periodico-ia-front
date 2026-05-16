<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class firma extends Model
{
  protected $guarded = [];
  protected $table = "firma";

  public function hasVideo(){
    return $this->hasOne("\App\documento","video_id","id");
  }
  public function cliente(){
    return $this->hasOne('\App\cliente','id','cliente_id');
  }
  public function cartera(){
    return $this->hasOne('\App\cartera','id','cartera_id');
  }
}
