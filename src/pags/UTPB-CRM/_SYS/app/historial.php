<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class historial extends Model
{
    protected $table = "historial";
    protected $guarded = [];

    public function usuario()
    {
      return $this->hasOne('\App\User','id','usuario_id');
    }

    public function full_fecha()
    {
      return \Carbon\Carbon::parse($this->created_at)->formatLocalized('%d %B %Y');
    }
}
