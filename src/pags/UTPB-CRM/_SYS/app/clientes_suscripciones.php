<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class clientes_suscripciones extends Model
{
    protected $guarded = [];

    public function cliente(){
        return $this->hasOne("\App\cliente","id","cliente_id");
    }
    public function suscripcion(){
        return $this->hasOne("\App\suscripciones","id","suscripcion_id");
    }

    public function cliente_suscripciones_ibrochures(){
        return $this->hasMany("\App\cliente_suscripciones_ibrochures","cliente_suscripcion_id","id");

    }
}
