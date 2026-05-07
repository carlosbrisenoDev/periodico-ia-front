<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class respuestas_reportes extends Model
{
    protected $table = "respuestas_reportes";
    protected $guarded = [];

    public function user(){
        return $this->hasOne('\App\User','id','usuario');
    }
    
    public function full_fecha()
    {
      setlocale(LC_ALL, 'Spanish');
      return \Carbon\Carbon::parse($this->created_at)->formatLocalized('%A %d %B %Y');
    }
    
}
