<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class nota extends Model
{
    protected $guarded = [];
    public function full_fecha()
    {
      setlocale(LC_ALL, 'Spanish');
      return \Carbon\Carbon::parse($this->created_at)->formatLocalized('%A %d %B %Y');
    }
    public function usuario()
    {
      return $this->hasOne('\App\User','id','user_id');
    }
    public function reporte()
    {
      return $this->hasOne('\App\reporte','id','reporte_id');
    }
}
