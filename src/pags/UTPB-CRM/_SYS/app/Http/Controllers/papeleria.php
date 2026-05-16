<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class papeleria extends Controller
{
    public function documentos()
    {
      $dir = storage_path()."/formatos";
      $odir = opendir($dir);
      $documentos = null;
      $i = 0;
      while(($file=readdir($odir)) != null)
        if($file != "." && $file != "..")
          {
            $documento = explode(".",$file);
            $documentos[$i++] = ["nombre"=>$documento[0],"formato"=>$documento[1]];
          }
      return $documentos;
    }
    public function get($id)
    {
      $doc = null;
      foreach ($this->documentos() as $key => $value) {
        if($id == md5($key))
          $doc = $value;
      }
      return $doc;
    }
    public function create(Request $r)
    {
        $d = \App\formato::create(['asunto'=>$r->asunto,'documento'=>$r->id,'level_id'=>\Auth::user()->level->id,'user_id'=>\Auth::user()->id,'destino'=>$r->destinatario]);
        \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha creado el formato ".$d->asunto." con referencia ".Date("Y")."/".$d->id]);
        return redirect("/papeleria/formatos/ver")->with("statu","Referencia generada");
    }
    public function formatos(Request $r)
    {
      return view('users.papeleria.misformatos',['documentos'=>\App\formato::where('level_id',\Auth::user()->level->id)->get()]);
    }
    public function nuevo(Request $r,$id)
    {
      return view('users.papeleria.nuevo',['formato'=>$this->get($id),'id'=>$id]);
    }
    public function listar(Request $r)
    {
      return view('users.papeleria.descargar',['documentos'=>$this->documentos()]);
    }
    public function subir(Request $r,$id)
    {
      return view('users.papeleria.subir',['formato'=>\App\formato::whereRAW('md5(id) = "'.$id.'"')->first()]);
    }
    public function save(Request $request)
    {
       ini_set('upload_max_filesize', '2G');
       ini_set('post_max_size', '4G');
       ini_set('max_execution_time', '5000000');
       ini_set('max_input_time', '5000000');
       ini_set('memory_limit', '200M');
      if($request->hasFile('documento')){
        $file = $request->file('documento');
          $data = explode('.',$file[0]->getClientOriginalName());
          $name = "";
          for($k = 0; $k < count($data)-1;$k++)
          {
            $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
          }
          $ext = $data[count($data)-1];
          \App\formato::find($request->id)->fill(["archivo"=>'1','formato'=>$ext])->save();
          $file[0]->move(storage_path()."\\escans\\",md5($request->id).'.file');
          \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha subido el documento escaneado $name"]);
        return redirect("/papeleria/subir/".md5($request->id));
      }
    }
    public function download(Request $r, $code)
    {
      $file = \App\formato::whereRAW("md5(id)='$code'")->first();
      return \Response::download(storage_path('escans\\'.$code.'.file'),$file->asunto.'.'.$file->formato);
    }
    public function descargar(Request $r, $code)
    {
      $file = \App\formato::whereRAW("md5(id)='$code'")->first();
      return \Response::download(storage_path('formatos\\'.$file->nombrefull()),$file->nombrefull());
    }
    public function watchar(Request $r, $code)
    {
      $file = \App\formato::whereRAW("md5(id)='$code'")->first();
      return \Response::file(storage_path('escans\\'.$code.'.file'));
    }
}
