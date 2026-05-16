<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class lineas extends Controller
{
    public function editar(Request $r, $id)
    {
      $linea = \App\linea::whereRAW('md5(id)="'.$id.'"')->first();
      return view('users.'.Auth::user()->level->alias.'.linea_editar',['linea'=>$linea]);
    }
    public function create(Request $r)
    {
      $all = $r->all();
      unset($all["_token"]);
      \App\linea::create($all);
      return redirect('/'.Auth::user()->level->alias.'/lineas')->with('status','Linea guardada');
    }
    public function refresh(Request $r)
    {
      $linea = \App\linea::find($r->id);
      $linea->fill($r->all());
      $linea->save();
      return redirect('/lineas/editar/'.md5($linea->id))->with('status','Linea guardada');
    }
    public function trash(Request $r)
    {
      return view('users.'.\Auth::user()->level->alias.".secure_linea",['id'=>$r->id]);
    }
    public function secure(Request $r)
    {
      \App\linea::find($r->id)->delete();
      return redirect('/'.\Auth::user()->level->alias.'/lineas/')->with('status','Linea eliminada');
    }
}
