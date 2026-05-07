<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class paypals extends Controller
{
    public function list(Request $r, $id)
    {
      return view('users.'.Auth::user()->level->alias.'.sucursales.paypal');
    }
    public function edit(Request $r, $id)
    {
      return view('users.'.Auth::user()->level->alias.'.sucursales.paypaledit',["p"=>\App\paypal::whereRAW("md5(id)='$id'")->first()]);
    }
    public function guardar(Request $r)
    {
      $all = $r->all();
      unset($all["_token"]);
      $pay = \App\paypal::create($all);
      $pay->usuario_id = Auth::user()->id;
      $pay->save();
      return redirect('/paypals/list/nueva')->with('status','Cuenta guardada');
    }
    public function default($r){
      Auth::user()->defecto = $r->cuenta_id;
      Auth::user()->save();
      return redirect('/paypals/list/nueva')->with('status','Cuenta configurada por defecto');
    }
    public function refresh(Request $r)
    {
      $linea = \App\paypal::find($r->id);
      $linea->fill($r->all());
      $linea->save();
      return redirect('/paypals/edit/'.md5($linea->id))->with('status','Cuenta actualizada');
    }
    public function delete(Request $r,$id){
      $n = "/paypals/edit/".md5($id);
      $y = "/paypals/trash";
      $w = "la cuenta seleccionada";
      return view('users.'.Auth::user()->level->alias.".secure_ask",["id"=>$id,"what"=>$w,"noroot"=>$n,"yesroot"=>$y]);
    }
    public function trash(Request $r)
    {
      \App\paypal::find($r->id)->delete();
      return redirect("/paypals/list/ver")->with('status','Cuenta eliminada');
    }
}
