<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\imagenes_sucursales;
use Auth;
class sucursales extends Controller
{
    public function actualizar(Request $r){
      $suc = \App\sucursal::where('id',$r->sucursal_id)->first();
      $all = $r->all();
      if(!isset($all["visible"]))
        $all["visible"] = 0;
      unset($all["sucursal_id"]);
      $suc->fill($all)->save();
      return redirect('/'.Auth::user()->level->alias.'/informacion')->with('status','Información actualizada');
    }
    public function agregarfranquiciatario(Request $r){
      $suc = \App\sucursal::whereRAW("md5(id)='$r->cid'")->first();
      $fs = \App\franquiciatarios_sucursales::create(["sucursal_id"=>$suc->id,"usuario_id"=>$r->franq]);
      return \App\User::find($fs->usuario_id)->name;
    }
    public function quitar(Request $r){
      $fs = \App\franquiciatarios_sucursales::find($r->cid)->delete();;
      return "ok";
    }
    public function actualizardos(Request $r){
      $suc = \App\sucursal::where('id',$r->sucursal_id)->first();
      $all = $r->all();
      if(!isset($all["visible"]))
        $all["visible"] = 0;
      if(!isset($all["domicilio"]))
        $all["domicilio"] = 0;
      unset($all["sucursal_id"]);
      $suc->fill($all)->save();
      return redirect('/sucursales/sucursal/'.md5($suc->id))->with('status','Información actualizada');
    }
    public function lista(Request $r){
      return view('users.'.\Auth::user()->level->alias.".sucursales.lista",["sucursales"=>\App\sucursal::all()]);
    }
    public function franquiciatario(Request $r){
      return view('users.'.\Auth::user()->level->alias.".sucursales.lista",["sucursales"=>Auth::user()->sucursales]);
    }
    public function sucursal(Request $r,$cid){
      return view('users.'.\Auth::user()->level->alias.".sucursales.sucursal",["sucursal"=>\App\sucursal::whereRAW("md5(id)='$cid'")->first()]);
    }
    public function guardar(Request $r){
      $sucursal = \App\sucursal::create();
      $sucursal->franquiciatario_id = $r->usuario_id;
      $sucursal->nombre = $r->nombre;
      $sucursal->save();
      return redirect('/sucursales/sucursal/'.md5($sucursal->id))->with('status','Sucursal creada');
    }

    public function pi(Request $r){
      $cid = $r->cid;
      $sucursal = \App\sucursal::whereRAW("md5(id) = '$cid'")->first();
      foreach($r->imagenes as $imagen){
        imagenes_sucursales::create(["imagen_id"=>$imagen,"sucursal_id"=>$sucursal->id]);
      }
      return redirect("/sucursales/sucursal/$cid");
    }
    public function trasheimagen(Request $r)
    {
      $imagen = \App\imagenes_sucursales::whereRAW("md5(id)='".$r->id."'")->first();
      $id = $imagen->sucursal_id;
      $imagen->delete();
      return redirect("/sucursales/sucursal/".md5($id))->with('status','Imagen retirada de la sucursal');
    }
    public function deleteimagen(Request $r,$id){
      $n = "/sucursales/sucursal/$id";
      $y = "/sucursales/trasheimagen";
      $w = "la imagen seleccionada";
      return view('users.'.Auth::user()->level->alias.".secure_ask",["id"=>$id,"what"=>$w,"noroot"=>$n,"yesroot"=>$y]);
    }
    public function trash(Request $r)
    {
      $suc = \App\sucursal::whereRAW("md5(id)='".$r->id."'")->first();
      $id = $suc->id;
      $suc->delete();
      return redirect("/sucursales/sucursal/".md5($id))->with('status','Sucursal eliminada');
    }
    public function delete(Request $r,$id){
      $n = "/sucursales/sucursal/$id";
      $y = "/sucursales/trash";
      $w = "la sucursal seleccionada";
      return view('users.'.Auth::user()->level->alias.".secure_ask",["id"=>$id,"what"=>$w,"noroot"=>$n,"yesroot"=>$y]);
    }
}
