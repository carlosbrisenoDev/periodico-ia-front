<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class finanzas extends Model
{
  protected $guarded = [];
  protected $table = "finanzas";

  public function user()
  {
    return $this->hasOne('\App\User','id','user_id');
  }
}
