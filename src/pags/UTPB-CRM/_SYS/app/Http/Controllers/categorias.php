<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\categoria;
class categorias extends Controller
{
  public function lista(Request $r){
    return view('users.'.Auth::user()->level->alias.".menu.categorias",["categorias"=>\App\categoria::all()]);
  }
  public function nueva(Request $r){
    return view('users.'.Auth::user()->level->alias.".menu.nueva");
  }
  public function guardar(Request $r){
    $c = categoria::create();
    $c->titulo = $r->titulo;
    $c->save();
    return redirect("/categorias/lista/ver");
  }
  public function trashe(Request $r)
  {
    $documento = \App\categoria::whereRAW("md5(id)='".$r->id."'")->first();
    $documento->delete();
    return redirect("/categorias/lista/ver")->with('status','Categoria eliminada');
  }
  public function delete(Request $r,$id){
    $n = "/categorias/lista/ver";
    $y = "/categorias/trashe";
    $w = "la categoria seleccionada";
    return view('users.'.Auth::user()->level->alias.".secure_ask",["id"=>$id,"what"=>$w,"noroot"=>$n,"yesroot"=>$y]);
  }
}
