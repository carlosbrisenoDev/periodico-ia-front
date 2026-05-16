<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class notas_cliente extends Model
{
    protected $guarded = [];
    protected $table = "notas_cliente";
    public function full_fecha()
    {
      setlocale(LC_ALL, 'Spanish');
      return \Carbon\Carbon::parse($this->created_at)->formatLocalized('%A %d %B %Y');
    }
    public function usuario()
    {
      return $this->hasOne('\App\User','id','usuario_id');
    }
    public function cliente()
    {
      return $this->hasOne('\App\cliente','id','cliente_id');
    }
    public function likes()
    {
      return $this->hasMany('\App\notas_likes','nota_id','id');
    }
}
