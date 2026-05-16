<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class paypal extends Model
{
    protected $guarded = [];
    protected $table = "paypal";
    public function usuario()
    {
      return $this->hasOne('\App\User','id','user_id');
    }

}
