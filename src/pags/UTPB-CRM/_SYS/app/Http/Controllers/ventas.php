<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class ventas extends Controller
{
    public function nuevo(Request $r){
        $c = \App\cliente::create($r->all());
        $c->sede_id = Auth::user()->sede->sede_id;
        $c->save();
        return redirect('/ventas/cliente?cid='.md5($c->id))->with("status","Cliente añadido");
    }
    public function like(Request $r){
        $n = \App\notas_cliente::whereRAW("md5(id)='".$r->cid."'")->first();
        $c = \App\notas_likes::create(["usuario_id"=>Auth::user()->id,"nota_id"=>$n->id]);
        return redirect('/ventas/cliente?cid='.md5($n->cliente->id))->with("status","Has reaccionado");
    }
    public function unlike(Request $r){
        $n = \App\notas_cliente::whereRAW("md5(id)='".$r->cid."'")->first();
        $c = \App\notas_likes::where("nota_id",$n->id)->where("usuario_id",Auth::user()->id)->delete();
        return redirect('/ventas/cliente?cid='.md5($n->cliente->id))->with("status","Ya no estas reaccionando");
    }
    public function setlead(Request $r){
        $c = \App\cliente::whereRAW("md5(id)='".$r->cid."'")->first();
        $lead = \App\leads::find($r->lead_id);
        $c->recipient = $lead->recipient;
        $c->save();
        return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Lead asignado");
    }
    public function nota(Request $f){
      $n = \App\notas_cliente::create(
        [
          "usuario_id" => Auth::user()->id,
          "cliente_id" => $f->cliente_id,
          "nota" => $f->comentario
        ]
      );
      return redirect('/ventas/cliente?cid='.md5($n->cliente->id))->with("status","Nota");
    }

    public function removeClient($id){
      $c = \App\cliente::whereRAW("md5(id)='".$id."'")->first()->delete();
      return redirect('/home')->with("status","Cliente Borrado Correctamente");
      // dd($id);
    }

    public function cliente_tag(Request $r){
      // dd($r);
      $n = \App\cliente_tag::create(
        [
          "usuario_id" => Auth::user()->id,
          "cliente_id" => $r->cliente_id,
          "nota" => $r->nota,
          "nivel_nota" => $r->nivel_nota
        ]
      );
      return redirect('/ventas/cliente?cid='.md5($r->cliente_id))->with("status","Tag Creado");
    }
    public function remove_cliente_tag(Request $request){
      $c = \App\cliente_tag::whereRAW("md5(id)='".$request->tag_id."'")->first()->delete();
      return redirect('/ventas/cliente?cid='.$request->cliente_id)->with("status","Tag Borrado Correctamente");
    }

    public function agend(Request $request){
      $c = \App\cliente::where('id',$request->cliente_id)->first();
      $dateValue = strtotime($request->fecha); 
      $event = \App\evento::create([
        'evento' => auth()->user()->name,
        'nota' => $request->nota,
        'dia' => date("d", $dateValue),
        'mes' => date("m", $dateValue),
        'year' => date("Y", $dateValue),
        'hora' => date("H", $dateValue),
        'minuto' => date("i", $dateValue),
        'date' => date("Y-m-d H:i:s", $dateValue),
        'level_id' => auth()->user()->level_id,
        'cliente_id' => $c->id
      ]);
      return json_encode(["ok" => "ok","cid" => md5($c->id)]);
      // return redirect('/ventas/cliente?cid='.$request->cliente_id)->with("status","Tag Borrado Correctamente");
      // dd($id);
    }
}


