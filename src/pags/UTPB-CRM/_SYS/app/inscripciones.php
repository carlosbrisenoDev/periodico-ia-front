<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class inscripciones extends Model
{
    protected $table = "inscripciones";
    protected $guarded = [];

    public function cliente(){
      return $this->hasOne('\App\cliente','id','cliente_id');
    }
}
