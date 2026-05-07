<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class reportes extends Controller
{
  public function nuevo(Request $r,$id)
  {
    $u = \App\User::whereRAW("md5(id) = '$id'")->first();
    return view('users.'.\Auth::user()->level->alias.".tareas.nuevo",['user'=>$u]);
  }
  public function nuevor(Request $r,$id)
  {
    $u = \App\ciudadano::whereRAW("md5(id) = '$id'")->first();
    return view('users.'.\Auth::user()->level->alias.".reportes.nuevo",["ciudadano"=>$u]);
  }
  public function create(Request $r)
  {
    $data = $r->all();
    unset($data["_token"]);
    $u = \App\reporte::create($data);
    \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha creado el reporte ".$u->nombre." para el àrea "]);
    return redirect('/tareas/modify/'.md5($u->id))->with('status','Tarea dada de alta');
  }
  public function creater(Request $r)
  {
    $data = $r->all();
    unset($data["_token"]);
    $u = \App\reporte::create($data);
    \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha creado el reporte ".$u->nombre." del ciudadano ".$u->ciudadano->full_name()]);
    return redirect('/reportes/modifyr/'.md5($u->id))->with('status','Reporte dado de alta');
  }
  public function refresh(Request $r)
  {
    $u = \App\reporte::find($r->id);
    $u->fill($r->all());
    $u->save();
    \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha ha actualizado el reporte  ".$u->nombre]);
    return redirect('/tareas/modify/'.md5($r->id))->with('status','Reporte actualizado');
  }
  public function write(Request $r)
  {
    $data = $r->all();
    $data["user_id"] = \Auth::user()->id;
    unset($data["_token"]);
    $nota = \App\nota::create($data);
    \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha creado una nota en el reporte ".$nota->reporte->nombre]);
    return redirect('/tareas/notas/'.md5($r->reporte_id))->with('status','Nota agregada');
  }
  public function trash(Request $r)
  {
    return view('users.'.\Auth::user()->level->alias.".tareas.secure",['id'=>$r->id]);
  }
  public function imprimir(Request $r,$id)
  {
    $u = \App\reporte::whereRAW('md5(id) = "'.$id.'"')->first();
    return view('users.'.\Auth::user()->level->alias.".tareas.imprimir",['reporte'=>$u]);
  }
  public function estado(Request $r, $id)
  {
    $estado = \App\estado::whereRAW('md5(id) = "'.$id.'"')->first();
    $area = \App\level::whereRAW('md5(id) = "'.$r->area.'"')->first();
    $tareas = \App\reporte::where('estado_id',$estado->id)->where('level_id',$area->id)->get();
    return view('users.'.\Auth::user()->level->alias.".tareas.estados",['reportes'=>$tareas,'area'=>$area]);
  }
  public function satisfactorios(Request $r, $id)
  {
    $area = \App\level::whereRAW('md5(id) = "'.$id.'"')->first();
    $tareas = \App\reporte::where('procedio',"1")->where('estado_id',\App\estado::where('nombre','Finalizado')->first()->id)->where('level_id',$area->id)->get();
    return view('users.'.\Auth::user()->level->alias.".tareas.estados",['reportes'=>$tareas,'area'=>$area,'title'=>'satisfactorios']);
  }
  public function insatisfactorios(Request $r, $id)
  {
    $area = \App\level::whereRAW('md5(id) = "'.$id.'"')->first();
    $tareas = \App\reporte::where('procedio',"0")->where('estado_id',\App\estado::where('nombre','Finalizado')->first()->id)->where('level_id',$area->id)->get();
    return view('users.'.\Auth::user()->level->alias.".tareas.estados",['reportes'=>$tareas,'area'=>$area,'title'=>'insatisfactorios']);
  }
  public function secure(Request $r)
  {
    $reporte = \App\reporte::find($r->get('id'));
    $ciudadano_id = $reporte->ciudadano_id;
    \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha eliminado el reporte ".$reporte->full_name()]);
    $reporte->delete();
    return redirect('/ciudadano/tareas/'.md5($ciudadano_id).'/')->with('status','Reporte eliminado con exito');
  }
  public function search(Request $r)
  {
    $reporte = \App\reporte::whereRAW('md5(id)="'.md5($r->folio).'"')->first();
    if($reporte != null)
    {
      if($reporte->level->id == \Auth::user()->level->id || \Auth::user()->level->name == "Atención ciudadana" || \Auth::user()->level->name == "Alcalde")
      {
        return view('users.'.\Auth::user()->level->alias.".tareas.modify",['reporte'=>$reporte]);
      } else {
        return redirect('/general/buscar')->with('status','Este reporte no pertenece a tu área');
      }
    } else {
      return redirect('/general/buscar')->with('error','El folio '.$r->folio." no existe");
    }
  }
  public function modify(Request $r,$id)
  {
    return view('users.'.\Auth::user()->level->alias.".tareas.modify",['reporte'=>\App\reporte::whereRAW("md5(id)='$id'")->first()]);
  }
  public function modifyr(Request $r,$id)
  {
    return view('users.'.\Auth::user()->level->alias.".reportes.modify",['reporte'=>\App\reporte::whereRAW("md5(id)='$id'")->first()]);
  }
  public function notas(Request $r,$id)
  {
    return view('users.'.\Auth::user()->level->alias.".tareas.notas",['reporte'=>\App\reporte::whereRAW("md5(id)='$id'")->first()]);
  }
  public function upload(Request $r,$id)
  {
    return view('users.'.\Auth::user()->level->alias.".tareas.upload",['reporte'=>\App\reporte::whereRAW("md5(id)='$id'")->first()]);
  }
}
