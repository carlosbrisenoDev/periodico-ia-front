<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class notas_likes extends Model
{
    protected $guarded = [];
    protected $table = "notas_like";
    public function full_fecha()
    {
      setlocale(LC_ALL, 'Spanish');
      return \Carbon\Carbon::parse($this->created_at)->formatLocalized('%A %d %B %Y');
    }
    public function usuario()
    {
      return $this->hasOne('\App\User','id','usuario_id');
    }
    public function nota()
    {
      return $this->hasOne('\App\notas_cliente','id','nota_id');
    }
}
