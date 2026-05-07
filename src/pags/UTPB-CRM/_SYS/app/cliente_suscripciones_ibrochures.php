<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cliente_suscripciones_ibrochures extends Model
{
    protected $table = "clientes_suscripciones_ibrochures";
    protected $guarded = [];

    public function ibrochure(){
        return $this->hasOne("\App\gaceta","id","ibrochure_id");
    }

    public function cliente_suscripcion(){
        return $this->hasOne("\App\clientes_suscripciones","id","cliente_suscripcion_id");
    }
}
