<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\documento;
use DB;
use Auth;
use Storage;
use Response;
use App\historial;
class documentos extends Controller
{
  public function save(Request $request)
  {
     ini_set('upload_max_filesize', '2G');
     ini_set('post_max_size', '4G');
     ini_set('max_execution_time', '5000000');
     ini_set('max_input_time', '5000000');
     ini_set('memory_limit', '200M');
    if($request->hasFile('documento')){
      $file = $request->file('documento');
      if(true)
      {
        foreach ($file as $ifile) {
          $data = explode('.',$ifile->getClientOriginalName());
          $name = "";
          for($k = 0; $k < count($data)-1;$k++)
          {
            $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
          }
          $ext = $data[count($data)-1];
          $document = documento::create(['size'=>$ifile->getSize(),'ext'=>$ext,'titulo'=>$name,'reporte_id'=>$request->id]);
          $ifile->move(storage_path(),md5($document->id).'.file');
          historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha subido el archivo $name de multiples"]);
        }
      } else {
        $data = explode('.',$file[0]->getClientOriginalName());
        $name = "";
        for($k = 0; $k < count($data)-1;$k++)
        {
          $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
        }
        $ext = $data[count($data)-1];
        $document = documento::create(['size'=>$file[0]->getSize(),'ext'=>$ext,'titulo'=>$name,'reporte_id'=>$request->id]);
        $file[0]->move(storage_path(),md5($document->id).'.file');
        historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha subido el archivo $name"]);
      }
      return redirect("/tareas/upload/".md5($request->id));
    } else {
      return redirect("/tareas/upload/".md5($request->id));
    }
  }
  public function subirventas(Request $r)
  {
    ini_set('upload_max_filesize', '2G');
    ini_set('post_max_size', '4G');
    ini_set('max_execution_time', '5000000');
    ini_set('max_input_time', '5000000');
    ini_set('memory_limit', '200M');
    if($r->hasFile('file')){
        $file = $r->file('file');
        $data = explode('.',$file->getClientOriginalName());
        $name = "";
        for($k = 0; $k < count($data)-1;$k++)
        {
         $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
        }
        $ext = $data[count($data)-1];
        $document = documento::create(['size'=>$file->getSize(),'ext'=>$ext,'titulo'=>$name,'empleado_id'=>$r->id]);
        $file->move(storage_path(),md5($document->id).'.file');
        historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha subido el archivo $name de multiples"]);
        echo JSON_encode($document);
      }
  }
  public function subircredit(Request $r)
  {
    ini_set('upload_max_filesize', '2G');
    ini_set('post_max_size', '4G');
    ini_set('max_execution_time', '5000000');
    ini_set('max_input_time', '5000000');
    ini_set('memory_limit', '200M');
    if($r->hasFile('file')){
        $file = $r->file('file');
        $data = explode('.',$file->getClientOriginalName());
        $name = "";
        for($k = 0; $k < count($data)-1;$k++)
        {
         $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
        }
        $ext = $data[count($data)-1];
        $document = documento::create(["type"=>"credit",'size'=>$file->getSize(),'ext'=>$ext,'titulo'=>$name,'empleado_id'=>$r->id]);
        $file->move(storage_path(),md5($document->id).'.file');
        historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha subido el archivo $name de multiples"]);
        echo JSON_encode($document);
      }
  }
  public function saveempleado(Request $request)
  {
     ini_set('upload_max_filesize', '2G');
     ini_set('post_max_size', '4G');
     ini_set('max_execution_time', '5000000');
     ini_set('max_input_time', '5000000');
     ini_set('memory_limit', '200M');
    if($request->hasFile('documento')){
      $file = $request->file('documento');
      if(true)
      {
        foreach ($file as $ifile) {
          $data = explode('.',$ifile->getClientOriginalName());
          $name = "";
          for($k = 0; $k < count($data)-1;$k++)
          {
            $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
          }
          $ext = $data[count($data)-1];
          $document = documento::create(['size'=>$ifile->getSize(),'ext'=>$ext,'titulo'=>$name,'empleado_id'=>$request->id]);
          $ifile->move(storage_path(),md5($document->id).'.file');
          historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha subido el archivo $name de multiples"]);
        }
      } else {
        $data = explode('.',$file[0]->getClientOriginalName());
        $name = "";
        for($k = 0; $k < count($data)-1;$k++)
        {
          $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
        }
        $ext = $data[count($data)-1];
        $document = documento::create(['size'=>$file[0]->getSize(),'ext'=>$ext,'titulo'=>$name,'empleado_id'=>$request->id]);
        $file[0]->move(storage_path(),md5($document->id).'.file');
        historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha subido el archivo $name"]);
      }
      return redirect("/empleados/informacion");
    } else {
      return redirect("/empleados/informacion");
    }
  }
  public function eliminar(Request $re)
    {
        foreach ($re->id as $id) {
          Storage::delete(storage_path('/'.md5($id).'.file'));
          $d = documento::find($id);
          historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha eliminado el archivo ".$d->title]);
          $d->delete();
        }
        return redirect($re->get('url'));
    }
    public function actualizar(Request $r)
    {
      $d = documento::find($r->get('id'));
      historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha modificado el archivo ".$d->title]);
      $all = $r->all();
      unset($all["url"]);
      $d->fill($all)->save();
      return redirect($r->get('url'));
    }
    public function download(Request $r, $code)
    {
      $file = documento::whereRAW("md5(id)='$code'")->first();
      return Response::download(storage_path($code.'.file'),$file->titulo.'.'.$file->ext);
    }
    public function watchar(Request $r, $code)
    {
      $file = documento::whereRAW("md5(id)='$code'")->first();
      return Response::file(storage_path($code.'.file'));
    }
    public function trash(Request $r,$code)
    {
        $documento = \App\documento::whereRAW("md5(id)='".$code."'")->first();
        return view('users.'.\Auth::user()->level->alias.".documentos.secure",['doc'=>$documento]);
    }
    public function trashe(Request $r)
    {
      $documento = \App\documento::whereRAW("md5(id)='".$r->id."'")->first();
      $documento->delete();
      return redirect("/empleados/informacion")->with('status','Documento eliminado');
    }
    public function delete(Request $r,$id){
      $n = "/empleados/informacion";
      $y = "/documentos/trashe";
      $w = "el documento seleccionado";
      return view('users.'.Auth::user()->level->alias.".secure_ask",["id"=>$id,"what"=>$w,"noroot"=>$n,"yesroot"=>$y]);
    }
    public function secure(Request $r)
    {
      $d = documento::find($r->get('id'));
      $reporte_id = $d->reporte->id;
      historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha modificado el archivo ".$d->title]);
      $d->delete();
      return redirect('/tareas/modify/'.md5($reporte_id).'/')->with('status','Documento eliminado con exito');
    }
}
