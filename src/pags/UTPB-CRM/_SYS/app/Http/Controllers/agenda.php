<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class agenda extends Controller
{
    public function guardar(Request $r)
    {
      $all = $r->all();
      \Auth::user()->level_id;

      \App\evento::create($all);
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha programado una cita para ".\Auth::user()->name." a las ".$all["dia"]." de ".$all["mes"]." del ".$all["year"]]);
      return redirect(\Auth::user()->level->alias."/agenda?oyear=".$r->year."&omes=".$r->mes."&d=".$r->dia)->with("status","Evento programado");
    }
    public function guardarcliente(Request $r)
    {
      $all = $r->all();
      \Auth::user()->level_id;
      unset($all["fecha"]);
      $fecha = \Carbon\Carbon::parse($r->fecha);
      $dia = $fecha->format("d");
      $anio = $fecha->format("Y");
      $mes = $fecha->format("m");
      $all["mes"] = $mes;
      $all["year"] = $anio;
      $all["dia"] = $dia;
      $all["level_id"] = \Auth::user()->level_id;;
      $all["minuto"] = $fecha->minute;
      \App\evento::create($all);
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha programado una cita para ".\Auth::user()->name." a las ".$dia." de ".$mes." del ".$anio]);
      return redirect("/ventas/cliente?cid=".md5($r->cliente_id))->with("status","Evento programado");
    }
    public function asignar(Request $r)
    {
      \App\permisa::create(['current_id'=>$r->level_id,'level_id'=>\Auth::user()->level->id]);
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Asigno privilegios de agenda a el área ".\App\level::find($r->level_id)->name]);
      return redirect(\Auth::user()->level->alias."/permisos")->with("status","Permiso asignado");
    }
    public function zap(Request $r,$id)
    {
      $p = \App\permisa::whereRAW("md5(id) = '".$id."'")->first();
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Elimino el permiso de agenda de ".$p->usuario->name]);
      $p->delete();
      return redirect(\Auth::user()->level->alias."/permisos")->with("status","Permiso eliminado");
    }
    public function trash(Request $r,$id)
    {
      return view('users.'.\Auth::user()->level->alias.".secures",['id'=>$id]);
    }
    public function secure(Request $r)
    {
      $evento = \App\evento::whereRAW('md5(id)="'.$r->id.'"')->first();
      $plus = "?oyear=".$evento->year."&omes=".$evento->mes."&d=".$evento->dia."&us=".md5($evento->level_id);
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha eliminado el evento ".$evento->evento]);
      $evento->delete();
      return redirect('/general/agenda'.$plus)->with('status','Evento programado eliminado con exito');
    }
}
