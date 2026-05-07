<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class historial extends Controller
{
    public function area(Request $r,$id)
    {
      $area = \App\level::whereRAW('md5(id)="'.$id.'"')->first();
      return view('users.'.\Auth::user()->level->alias.".historial.usuario",['area'=>$area]);
    }
    public function calificar(Request $r)
    {
      $area = \App\level::find($r->id);
      $area->calificacion = $r->calificacion;
      $area->save();
      return redirect("/historial/area/".md5($area->id))->with('status','Calificación otorgada');
    }
}
