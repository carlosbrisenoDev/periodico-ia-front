<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class categoria extends Model
{
    protected $table = "categorias";
    protected $guarded = [];

    public function platillos(){
      return $this->hasMany("\App\platillo","categoria_id","id");
    }
}
