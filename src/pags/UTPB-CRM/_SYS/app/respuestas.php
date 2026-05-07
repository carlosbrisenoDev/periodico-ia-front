<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class respuestas extends Model
{
  protected $guarded = [];
  protected $table = "respuestas";
  
  public function full_fecha()
    {
      setlocale(LC_ALL, 'Spanish');
      return \Carbon\Carbon::parse($this->created_at)->formatLocalized('%A %d %B %Y');
    }
}
