<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class levels extends Controller
{
    public function modify(Request $r, $id){
      return view('users.'.Auth::user()->level->alias.".levels.modify",["level"=>\App\level::whereRAW("md5(id)='$id'")->first()]);
    }
    public function modifymodule(Request $r, $id){
      return view('users.'.Auth::user()->level->alias.".levels.modifymodule",["level"=>\App\level::whereRAW("md5(id)='$id'")->first()]);
    }
    public function delete(Request $r, $id){
      $n = "/".Auth::user()->level->alias.'/areas';
      $y = "/levels/trash";
      $w = "el àrea seleccionada";
      return view('users.'.Auth::user()->level->alias.".secure_ask",["id"=>$id,"what"=>$w,"noroot"=>$n,"yesroot"=>$y]);
    }
    public function trash(Request $r){
      \App\level::whereRAW("md5(id)='".$r->id."'")->first()->delete();
      return redirect(Auth::user()->level->alias.'/areas')->with('status','Área elimina');
    }
    public function nuevo(Request $r){
      return view('users.'.Auth::user()->level->alias.".levels.nuevo");
    }
    public function create(Request $r){
      $all = $r->all();
      unset($all["_token"]);
      \App\level::create($all);
      return redirect(Auth::user()->level->alias.'/areas')->with('status','Área creada');
    }
    public function update(Request $r){
      unset($r["_token"]);
      $level = \App\level::find($r->id);
      $level->fill($r->all());
      $level->save();
      return redirect(Auth::user()->level->alias.'/areas')->with('status','Área modificada');
    }
}
