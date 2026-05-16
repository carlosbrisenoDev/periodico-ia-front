<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class tareas extends reportes
{
    public function list(Request $r){
      return view('users.'.\Auth::user()->level->alias.".tareas.lista");
    }
}
