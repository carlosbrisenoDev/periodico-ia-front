<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ciudadano extends Controller
{
  public function create(Request $r)
  {
    $data = $r->all();
    unset($data["_token"]);
    $u = \App\ciudadano::create($data);
    $ciudadano = $u;
    $u->codigo = "UOVA".\Carbon\Carbon::parse($ciudadano->created_at)->year.$ciudadano->id;
    $u->save();
    \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha creado al ciudadano ".$u->full_name()]);
    return redirect('/ciudadano/modify/'.md5($u->id))->with('status','Ciudadano agregado');
  }
  public function refresh(Request $r)
  {
    $data = $r->all();
    unset($data["_token"]);
    $u = \App\ciudadano::find($r->id)->fill($data);
    $u->save();
    \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha actualizado al ciudadano".$u->full_name()]);
    return redirect('/ciudadano/modify/'.md5($r->id))->with('status','Ciudadano actualizado');
  }
  public function trash(Request $r)
  {
    return view('users.'.\Auth::user()->level->alias.".secure",['id'=>$r->id]);
  }
  public function secure(Request $r)
  {
    $ciudadano = \App\ciudadano::find($r->get('id'));
    \App\reporte::where('ciudadano_id',$ciudadano->id)->delete();
    \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha eliminado al ciudadano ".$ciudadano->full_name()]);
    $ciudadano->delete();
    return redirect('/'.\Auth::user()->level->alias.'/buscar/')->with('status','Ciudadano eliminado con exito');
  }
  public function modify(Request $r,$id)
  {
    return view('users.'.\Auth::user()->level->alias.".modify",['user'=>\App\ciudadano::whereRAW("md5(id)='$id'")->first()]);
  }
  public function modifyc(Request $r,$id)
  {
    return view('users.'.\Auth::user()->level->alias.".modifyc",['ciudadano'=>\App\ciudadano::whereRAW("md5(id)='$id'")->first()]);
  }
  public function searchd(Request $r)
  {
    return view('users.'.\Auth::user()->level->alias.".list2",['ciudadanos'=>\App\ciudadano::whereRAW("codigo like '%".$r->search."%' or concat(concat(concat(nombre,' '),concat(apellidopat,' ')),apellidomat) like '%".$r->get('search')."%' or email like '%".$r->get('search')."%' or curp like '%".$r->get('search')."%'")->get()]);
  }
  public function search(Request $r)
  {
    return view('users.'.\Auth::user()->level->alias.".list",['ciudadanos'=>\App\ciudadano::whereRAW("codigo like '%".$r->search."%' or concat(concat(concat(nombre,' '),concat(apellidopat,' ')),apellidomat) like '%".$r->get('search')."%' or email like '%".$r->get('search')."%' or curp like '%".$r->get('search')."%'")->get()]);
  }
  public function reportes(Request $r,$id)
  {
    $u = \App\ciudadano::whereRAW("md5(id) = '$id'")->first();
    return view('users.'.\Auth::user()->level->alias.".reportes",['reportes'=>\App\reporte::where('ciudadano_id',$u->id)->get(),'ciudadano'=>$u]);
  }
}
