<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class permisa extends Model
{
    protected $table = "permisa";
    protected $guarded = [];

    public function level()
    {
      return $this->hasOne('\App\level','id','level_id');
    }
    public function usuario()
    {
      return $this->hasOne('\App\level','id','current_id');
    }
}
