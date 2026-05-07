<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class finanzas extends Controller
{
  public function guardar(Request $r){
    $all = $r->all();
    $all["user_id"] = \Auth::user()->id;
    \App\finanzas::create($all);
    return redirect("/r/finanzas")->with("status","Finanza salvada");
  }
  public function eliminar(Request $r){
    \App\finanzas::whereRAW("md5(id) = '".$r->cid."'")->first()->delete();
    if($r->tipo == "Entrada"){
      return redirect("/r/finanzas")->with("status","Finanza eliminada");
    } else {
      return redirect("/r/finanzas?salida=1")->with("status","Finanza eliminada");

    }
  }
}
