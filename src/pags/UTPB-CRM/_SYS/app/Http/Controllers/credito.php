<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use \App\Mail\PreAprobarCliente;
use \App\Mail\RechazarCliente;
use \App\Mail\FirmarCliente;

class credito extends Controller
{
  public function seto(Request $r){
    $c = \App\credito::whereRAW("md5(id)='$r->cid'")->first();
    eval("\$c->".$r->seto."='".$r->v."';");
    $c->save();
    return "Ok";
  }
  public function reject(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    $c->credito_info->status = null;
    $c->credito_info->save();
    $c->credito = NULL;
    if($c->agente != null){
      \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,$c->agente->name=>$c->agente->email])->send(new RechazarCliente($c,$r->razon));
    } else {
      \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,\Auth::user()->name=>\Auth::user()->email])->send(new RechazarCliente($c,$r->razon));
    }
    $c->save();
    return redirect("/creditos/solicitud?cid=".md5($c->credito_info->id))->with("status","Solicitud rechazada");
  }
  public function requestsign(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    $c->ccredito()->status = 2;
    $c->ccredito()->save();
    if($c->agente != null){
      \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,$c->agente->name=>$c->agente->email])->send(new FirmarCliente($c,$r->razon));
    } else {
      \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,\Auth::user()->name=>\Auth::user()->email])->send(new FirmarCliente($c,$r->razon));
    }
    $c->save();
    return redirect("/creditos/solicitud?cid=".md5($c->credito_info->id))->with("status","Solicitud rechazada");
  }
  public function aprobar(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    $c->credito_info->status = "preaprobado";
    if($c->agente != null){
      \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,$c->agente->name=>$c->agente->email])->send(new PreAprobarCliente($c,$r->razon));
    }else{
      \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,\Auth::user()->name=>\Auth::user()->email])->send(new PreAprobarCliente($c,$r->razon));
    }
    $c->credito_info->save();
    return redirect("/creditos/solicitud?cid=".md5($c->credito_info->id))->with("status","Solicitud Pre aprobada");
  }
  public function actualizar(Request $r){
    $c =  Auth::user()->cliente->cinfo();
    $all = $r->all();
    unset($all["nombre"]);
    unset($all["edad"]);
    unset($all["relacion"]);
    unset($all["telefono"]);
    unset($all["horario"]);
    $c->fill($all);
    $c->status = "enviado";
    //dd($r->nombre);
    if(isset($r->nombre)){
      for ($i = 0; $i < count($r->nombre);$i++) {
        \App\familiares::create(["nombre"=>$r->nombre[$i],"horario"=>$r->horario[$i],"telefono"=>$r->telefono[$i],"edad"=>$r->edad[$i],"relacion"=>$r->relacion[$i],"credito_info_id"=>$c->id]);
      }
    }
    $c->save();
    return redirect("/alumnos/credito")->with("status","Solicitud de crédito enviada.");
  }
}
