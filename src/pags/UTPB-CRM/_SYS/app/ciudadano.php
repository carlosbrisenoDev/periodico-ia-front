<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ciudadano extends Model
{
  protected $guarded = [];
  public function full_name()
  {
    return $this->nombre." ".$this->apellidopat." ".$this->apellidomat;
  }
}
