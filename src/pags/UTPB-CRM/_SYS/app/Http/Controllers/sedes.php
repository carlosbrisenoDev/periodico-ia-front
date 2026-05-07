<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class sedes extends Controller
{
  public function crear(Request $r){
    \App\sedes::create(["sede"=>$r->sede]);
    return redirect("/administrador/sedes")->with("status","Sede creada");
  }
  public function eliminar(Request $r){
    \App\sedes::whereRAW("md5(id)='".$r->cid."'")->first()->delete();
    return redirect("/administrador/sedes")->with("status","Sede eliminada");
  }
  public function set(Request $r){
    $u = \App\user::whereRAW("md5(id)='".$r->cid."'")->first();
    $u->sede_id = $r->sede_id;
    $u->save();
    return redirect("/user/modify/".$r->cid)->with("status","Usuario modificado");

  }
}
