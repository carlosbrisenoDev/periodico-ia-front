<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class suscripciones extends Model
{
    protected $table = "suscripciones";
    protected $guarded = [];

    public function ibrochures(){
        return $this->hasMany("\App\suscripciones_ibrochures","suscripcion_id","id");
    }
}
