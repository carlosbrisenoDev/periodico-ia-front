<?php

namespace App\Http\Controllers;

use App\cliente_suscripciones_ibrochures;
use Illuminate\Http\Request;
use Auth;
use Storage;
use Response;
class gaceta extends Controller
{
  public function publicaciones(Request $r)
  {
    return view('users.'.Auth::user()->level->alias.".gaceta.publicaciones",["gaceta"=>\App\gaceta::orderBy("id","desc")->get()]);
  }
  public function nuevo(Request $r)
  {
    return view('users.'.Auth::user()->level->alias.".gaceta.nuevo");
  }
  public function editar(Request $r,$cid)
  {
    return view('users.'.Auth::user()->level->alias.".gaceta.editar",["pub"=>\App\gaceta::whereRAW("md5(id)='$cid'")->first()]);
  }
  public function create(Request $r)
  {
    $gac = \App\gaceta::create(["titulo" => $r->titulo,"contenido" => "Contenido de nuevo iBrochure"]);
    return json_encode(["ok" => "ok","cid" => md5($gac->id)]);
  }
  public function actualizar(Request $r)
  {
    $all = $r->all();
    unset($all["id"]);
    $gaceta = \App\gaceta::find($r->id);
    $gaceta->fill($all);
    $gaceta->grupos = json_encode($r->grupos ?? []);
    $gaceta->save();
    return redirect()->back()->with('status','Articulo actualizado');
  }
  public function send(Request $r)
  {
    $gaceta = $p = \App\gaceta::whereRAW("md5(id)='$r->ibrochure'")->first();
    $c = auth()->user();
    $asunto = null;
    $newdesc = str_replace("%NOMBRE%",$c->name,$p->contenido);
    $newdesc = str_replace("%APAT%",$c->apat,$newdesc);
    $newdesc = str_replace("%AMAT%",$c->amat,$newdesc);
    $newdesc = str_replace("%NOMBREASESOR%",$c->name,$newdesc);
    $newdesc = str_replace("%CARGOASESOR%",$c->cargo ?? "Sin cargo",$newdesc);
    $newdesc = str_replace("%TELEFONOASESOR%",$c->telefono ?? "Sin teléfono",$newdesc);
    $newdesc = str_replace("%CORREOASESOR%",$c->email ?? "Sin correo",$newdesc);

    $email = $r->email;
    $subject = $p->titulo;
    $filespath = \App\filesfrompath::where("pathname","crearnuevagaceta?cid=".md5($p->id))->get();
    // $asunto = $r->asunto;
    $files = [];
    $names = [];
    foreach($filespath as $fp){
      array_push($names,$fp->filename.".".$fp->documento->ext);
      array_push($files,\Cloud::url($fp->document_id));
    }
    \Mail::to($email)->send(new \App\Mail\informacionEmail($asunto,$newdesc,$files,$names,$subject,$c));

    return json_encode(["ok"=>"ok"]);
  }
  public function sendcliente(Request $r)
  {
    $asunto = null;
    $gaceta = $p = \App\gaceta::whereRAW("md5(id)='$r->ibrochure'")->first();
    $c = \App\cliente::find($r->cliente_id);
    $a = auth()->user();
    $newdesc = str_replace("%NOMBRE%",$c->nombre,$p->contenido);
    $newdesc = str_replace("%APAT%",$c->apat,$newdesc);
    $newdesc = str_replace("%AMAT%",$c->amat,$newdesc);
    $newdesc = str_replace("%NOMBREASESOR%",$a->name,$newdesc);
    $newdesc = str_replace("%CARGOASESOR%",$a->cargo ?? "Sin cargo",$newdesc);
    $newdesc = str_replace("%TELEFONOASESOR%",$a->telefono ?? "Sin teléfono",$newdesc);
    $newdesc = str_replace("%CORREOASESOR%",$a->email ?? "Sin correo",$newdesc);
    $asunto = $r->asunto;
    $email = $c->correo;
    $subject = $p->titulo;
    $filespath = \App\filesfrompath::where("pathname","crearnuevagaceta?cid=".md5($p->id))->get();
    $files = [];
    $names = [];
    foreach($filespath as $fp){
      array_push($names,$fp->filename.".".$fp->documento->ext);
      array_push($files,\Cloud::url($fp->document_id));
    }
    \Mail::to($email)->send(new \App\Mail\informacionEmail($asunto,$newdesc,$files,$names,$subject,$a));

    return json_encode(["ok"=>"ok"]);
  }

  static function sendClienteCSI($csi){
    $asunto = null;
    $gaceta = $p = $csi->ibrochure;
    $c = $csi->cliente_suscripcion->cliente;
    $a = $csi->cliente_suscripcion->cliente->agente;
    $newdesc = str_replace("%NOMBRE%",$c->nombre,$p->contenido);
    $newdesc = str_replace("%APAT%",$c->apat,$newdesc);
    $newdesc = str_replace("%AMAT%",$c->amat,$newdesc);
    $newdesc = str_replace("%NOMBREASESOR%",$a->name,$newdesc);
    $newdesc = str_replace("%CARGOASESOR%",$a->cargo ?? "Sin cargo",$newdesc);
    $newdesc = str_replace("%TELEFONOASESOR%",$a->telefono ?? "Sin teléfono",$newdesc);
    $newdesc = str_replace("%CORREOASESOR%",$a->email ?? "Sin correo",$newdesc);
    $asunto = $gaceta->asunto;
    $email = $c->correo;
    $subject = $p->titulo;
    $filespath = \App\filesfrompath::where("pathname","crearnuevagaceta?cid=".md5($p->id))->get();
    $files = [];
    $names = [];
    foreach($filespath as $fp){
      array_push($names,$fp->filename.".".$fp->documento->ext);
      array_push($files,\Cloud::url($fp->document_id));
    }
    \Mail::to($email)->send(new \App\Mail\informacionEmail($asunto,$newdesc,$files,$names,$subject,$a));
  }

  public function upload(Request $request){
    ini_set('upload_max_filesize', '2G');
    ini_set('post_max_size', '4G');
    ini_set('max_execution_time', '5000000');
    ini_set('max_input_time', '5000000');
    ini_set('memory_limit', '200M');
    if($request->hasFile('file')){
        $file = $request->file('file');
        $data = explode('.',$file->getClientOriginalName());
        $name = "";
        for($k = 0; $k < count($data)-1;$k++)
        {
         $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
        }
        $ext = $data[count($data)-1];
        $file->move(storage_path()."/gaceta/",md5($name).'.file');
        echo $name.",".md5($name);
     }

   }
   public function watchar(Request $r,$cid)
   {
     return Response::file(storage_path()."/gaceta/".$cid.'.file');
   }
   public function trash(Request $r)
   {
     $d = \App\gaceta::whereRAW("md5(id)='$r->id'")->first();
     \App\historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha eliminado el articulo ".$d->titulo]);
     $d->delete();
     return redirect('/gaceta/publicaciones/lista')->with('status','Articulo eliminado');
   }
   public function delete(Request $r,$id){
     $n = "/gaceta/publicaciones/lista";
     $y = "/gaceta/trash";
     $w = "el elemento seleccionado";
     return view('users.'.Auth::user()->level->alias.".secure_ask",["id"=>$id,"what"=>$w,"noroot"=>$n,"yesroot"=>$y]);
   }
}
