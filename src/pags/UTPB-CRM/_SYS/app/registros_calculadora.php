<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class registros_calculadora extends Model
{
    protected $guarded = [];
    protected $table = "registros_calculadora";

    public function usuario(){
        return $this->hasOne('\App\User','id','usuario_id');
      }
}
