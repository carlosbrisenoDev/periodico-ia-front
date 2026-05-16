<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class imagen extends Model
{
    protected $table = "imagenes";
    protected $guarded = [];

    public function fa(){
      return "fa-image fa-5x";
    }
}
