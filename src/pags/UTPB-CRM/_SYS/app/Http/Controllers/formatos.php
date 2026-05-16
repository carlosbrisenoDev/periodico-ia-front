<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class formatos extends Controller
{
  public function estado(Request $r, $id)
  {
    $area = \App\level::whereRAW('md5(id) = "'.$id.'"')->first();
    $formatos = \App\formato::where('archivo',$r->archivo)->where('level_id',$area->id)->get();
    return view('users.'.\Auth::user()->level->alias.".papeleria.misformatos",['formatos'=>$formatos,'area'=>$area]);
  }
  public function subir(Request $r,$id)
  {
    return view('users.'.\Auth::user()->level->alias.'.subir',['formato'=>\App\formato::whereRAW('md5(id) = "'.$id.'"')->first()]);
  }
  public function createfolio(Request $r)
  {
      $ref = \App\formato::where('level_id',\Auth::user()->level->id)->where('tipo','0')->orderBy('id','desc')->first();
      if($ref == null)
      {
        $ref = 1;
      } else {
        $ref = $ref->referencia + 1;
      }
      $d = \App\formato::create(['asunto'=>$r->asunto,'referencia'=>$ref,'documento'=>0,'level_id'=>\Auth::user()->level->id,'user_id'=>\Auth::user()->id,'destino'=>$r->destinatario]);
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha creado la ficha técnica ".$d->asunto." con referencia JUR-FT-".$ref."/".Date("Y")]);
      return redirect(\Auth::user()->level->alias."/ft")->with("status","Referencia generada");
  }
  public function eliminar(Request $r,$id)
  {
    $e = \App\formato::whereRAW('md5(id) = "'.$id.'"')->first();
    \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha eliminado el archivo de descarga ".$e->asunto]);
    $e->delete();
    return redirect(\Auth::user()->level->alias."/descargas")->with("status","Referencia de archivo eliminada");
  }
  public function createoficio(Request $r)
  {
      $ref = \App\formato::where('level_id',\Auth::user()->level->id)->where('tipo','1')->orderBy('id','desc')->first();
      if($ref == null)
      {
        $ref = 1;
      } else {
        $ref = $ref->referencia + 1;
      }
      $d = \App\formato::create(['asunto'=>$r->asunto,'tipo'=>'1','referencia'=>$ref,'documento'=>0,'level_id'=>\Auth::user()->level->id,'user_id'=>\Auth::user()->id,'destino'=>$r->destinatario]);
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha creado el oficio".$d->asunto." con referencia JUR-".$ref."/".Date("Y")]);
      return redirect(\Auth::user()->level->alias."/oficios")->with("status","Referencia generada");
  }
  public function createdescarga(Request $r)
  {
      $d = \App\formato::create(['asunto'=>$r->asunto,'tipo'=>'2','documento'=>0,'level_id'=>\Auth::user()->level->id,'user_id'=>\Auth::user()->id,'destino'=>$r->destinatario]);
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha creado el archivo de descarga".$d->asunto]);
      return redirect(\Auth::user()->level->alias."/descargas")->with("status","Referencia de archivo generado");
  }
}
