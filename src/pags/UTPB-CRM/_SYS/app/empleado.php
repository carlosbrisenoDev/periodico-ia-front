<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class empleado extends Model
{
    protected $guarded = [];
    protected $table = "empleados";

    public function documentos()
    {
      return $this->hasMany('\App\documento','empleado_id','id');

    }
    public function usuario(){
      return $this->hasOne('\App\User','id','usuario_id');
    }
}
