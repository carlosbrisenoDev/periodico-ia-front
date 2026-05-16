<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class suscripciones_ibrochures extends Model
{
    protected $guarded = [];
    public function ibrochure(){
        return $this->hasOne("\App\gaceta","id","ibrochure_id");
    }
    public function suscription(){
        return $this->hasOne("\App\suscripciones","id","suscripcion_id");
    }
}
