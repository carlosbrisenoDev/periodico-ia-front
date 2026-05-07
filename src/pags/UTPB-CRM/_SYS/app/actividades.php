<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class actividades extends Model
{
    protected $guarded = [];
    protected $table = "actividades";

    public function cliente(){
        return $this->hasOne('\App\cliente','id','cliente_id');
    }

    public function catalogo_actividades(){
        return $this->hasOne('\App\catalogo_actividades','id','catalogo_actividad_id');
    }

    public function usuario(){
        return $this->hasOne('\App\User','id','usuario_id');
    }

    public function actSel($id){
        $sel = '';
        if($id==$this->catalogo_actividad_id){
            $sel = 'checked';
        }
        return $sel;
    }
}
