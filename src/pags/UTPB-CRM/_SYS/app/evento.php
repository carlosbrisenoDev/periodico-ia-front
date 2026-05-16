<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class evento extends Model
{
    protected $table = "eventos";
    protected $guarded = [];

    public function cliente(){
      return $this->hasOne('\App\cliente','id','cliente_id');
    }
}
