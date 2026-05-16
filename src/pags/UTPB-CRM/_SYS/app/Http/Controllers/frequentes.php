<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
class frequentes extends Controller
{
  public function nuevo(Request $r){
    \App\frecuente::create(["mensaje"=>$r->mensaje,"usuario_id"=>\Auth::user()->id]);
    return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Frecuente creado");
  }
  public function del(Request $r){
    \App\frecuente::whereRAW("md5(id)='".$r->cidd."'")->first()->delete();
    return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Frecuente eliminado");

  }
}
