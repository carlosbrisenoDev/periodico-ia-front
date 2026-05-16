<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class encuestas extends Controller
{
  public function crear(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='".$r->cid."'")->first();
    if(count($c->encuestas)>0){
        foreach ($c->encuestas as $cr) {
          if($cr->encuesta_id == $r->encuesta && $cr->cliente_id == $c->id){
            return redirect("/ventas/rollcenter?c=".$c->id)->with('status','No se puede aplicar dos veces la misma encuesta');
          }
        }
    }
    \App\cliente_encuesta::create(["cliente_id"=>$c->id,"encuesta_id"=>$r->encuesta]);
    return redirect("/ventas/rollcenter?c=".$c->id)->with('status','Encuesta creada');
  }

  public function save(Request $r){
    $c_e = \App\cliente_encuesta::whereRAW("md5(id)='".$r->cliente_encuesta_id."'")->first();
    $preguntas = [];
    foreach ($r->all() as $key => $value) {
      if(strstr($key,"p_")){
        \App\respuestas::create(["cliente_encuesta_id"=>$c_e->id,"pregunta_id"=>str_replace("p_","",$key),"respuesta"=>$value]);
      }
    }
    $c_e->respondida = 1;
    $c_e->save();
    return redirect("/ventas/rollcenter?c=".$c_e->cliente->id)->with('status','Guardado');
  }
}
