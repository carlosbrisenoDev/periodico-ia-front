<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class roles_cliente extends Model
{
  protected $guarded = [];
  protected $table = "clientes_roles";

  public function role()
  {
    return $this->hasOne('\App\role','id','role_id');
  }
  public function user()
  {
    return $this->hasOne('\App\User','id','cliente_id');
  }
}
