<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\platillo;
use App\imagenes_platillos;
class platillos extends Controller
{
  public function lista(Request $r){
    return view('users.'.Auth::user()->level->alias.".menu.platillos",["platillos"=>\App\platillo::all()]);
  }
  public function nuevo(Request $r){
    return view('users.'.Auth::user()->level->alias.".menu.nuevoplatillo");
  }
  public function edit(Request $r,$cid){
    return view('users.'.Auth::user()->level->alias.".menu.edit",["platillo"=>\App\platillo::whereRAW("md5(id) = '$cid'")->first()]);
  }
  public function imagenes(Request $r,$cid){
    return view('users.'.Auth::user()->level->alias.".menu.platillo",["platillo"=>\App\platillo::whereRAW("md5(id) = '$cid'")->first()]);
  }
  public function guardar(Request $r){
    $c = platillo::create();
    $c->fill($r->all());
    $c->save();
    return redirect("/platillos/lista/ver");
  }
  public function actualizar(Request $r){
    $platillo = \App\platillo::whereRAW("md5(id) = '$r->cid'")->first();
    $all = $r->all();
    if(!isset($all["visible"]))
      $all["visible"] = 0;
    if(!isset($all["envio"]))
      $all["envio"] = 0;
    unset($all["cid"]);
    $platillo->fill($all);
    $platillo->save();
    return redirect("/platillos/lista/ver");
  }
  public function pi(Request $r){
    $cid = $r->cid;
    $platillo = \App\platillo::whereRAW("md5(id) = '$cid'")->first();
    foreach($r->imagenes as $imagen){
      imagenes_platillos::create(["imagen_id"=>$imagen,"platillo_id"=>$platillo->id]);
    }
    return redirect("/platillos/imagenes/$cid");
  }
  public function trasheimagen(Request $r)
  {
    $imagen = \App\imagenes_platillos::whereRAW("md5(id)='".$r->id."'")->first();
    $id = $imagen->platillo_id;
    $imagen->delete();
    return redirect("/platillos/imagenes/".md5($id))->with('status','Imagen retirada del platillo');
  }
  public function deleteimagen(Request $r,$id){
    $n = "/platillos/imagenes/$id";
    $y = "/platillos/trasheimagen";
    $w = "la imagen seleccionada";
    return view('users.'.Auth::user()->level->alias.".secure_ask",["id"=>$id,"what"=>$w,"noroot"=>$n,"yesroot"=>$y]);
  }

  public function trashe(Request $r)
  {
    $imagen = \App\platillo::whereRAW("md5(id)='".$r->id."'")->first();
    $id = $imagen->id;
    $imagen->delete();
    return redirect("/platillos/lista/ver")->with('status','Platillo eliminado');
  }
  public function delete(Request $r,$id){
    $n = "/platillos/imagenes/$id";
    $y = "/platillos/trashe";
    $w = "el platillo seleccionada";
    return view('users.'.Auth::user()->level->alias.".secure_ask",["id"=>$id,"what"=>$w,"noroot"=>$n,"yesroot"=>$y]);
  }
}
