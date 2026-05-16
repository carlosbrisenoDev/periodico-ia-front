<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\imagen;
use App\historial;
use DB;
use Storage;
use Response;
class imagenes extends Controller
{
    public function lista(Request $r){
      return view('users.'.Auth::user()->level->alias.".imagenes.lista",["imagenes"=>\App\imagen::orderBy("id","desc")->get()]);
    }
    public function guardar(Request $request)
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
            $document = imagen::create(['size'=>$ifile->getSize(),'ext'=>$ext,'titulo'=>$name]);
            $ifile->move(storage_path()."/imagenes/",md5($document->id).'.file');
            historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha subido la imagen $name de multiples"]);
          }
        } else {
          $data = explode('.',$file[0]->getClientOriginalName());
          $name = "";
          for($k = 0; $k < count($data)-1;$k++)
          {
            $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
          }
          $ext = $data[count($data)-1];
          $document = imagen::create(['size'=>$file[0]->getSize(),'ext'=>$ext,'titulo'=>$name]);
          $file[0]->move(storage_path()."imagenes/",md5($document->id).'.file');
          historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha subido la imagen $name"]);
        }

        $this->gen($request,md5($document->id));
      } else {
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
        $file = imagen::whereRAW("md5(id)='$code'")->first();
        return Response::download(storage_path("imagenes/".$code.'.file'),$file->titulo.'.'.$file->ext);
      }
      public function watchar(Request $r, $code)
      {
        $file = imagen::whereRAW("md5(id)='$code'")->first();
        return Response::file(storage_path("imagenes/".$code.'.file'));
      }
      public function watcharlittle(Request $r, $code)
      {
        define('DEBUG', true);

        error_reporting(E_ALL);
        ini_set('display_errors', DEBUG ? 'On' : 'Off');
        ini_set('memory_limit', '-1');
        if(!file_exists(storage_path()."/imagenes/".$code.'.file_tiny'))
        {
          $this->gen($r,$code);
        }

        return Response::file(storage_path("imagenes/".$code.'.file_tiny'));
      }

      public function gen(Request $r, $code)
      {
        $file = imagen::whereRAW("md5(id)='$code'")->first();
        $maxDim = 200;
        $file_name = storage_path("/imagenes/".$code.'.file');
        list($width, $height, $type, $attr) = getimagesize( $file_name );
        if ( $width > $maxDim || $height > $maxDim ) {
            $target_filename = $file_name;
            $ratio = $width/$height;
            if( $ratio > 1) {
                $new_width = $maxDim;
                $new_height = $maxDim/$ratio;
            } else {
                $new_width = $maxDim*$ratio;
                $new_height = $maxDim;
            }
            $src = imagecreatefromstring(file_get_contents($file_name));
            $dst = imagecreatetruecolor( $new_width, $new_height );
            imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

            if(imagepng( $dst, storage_path()."/imagenes/".$code.'.file_tiny')){
              return true;
            }
            imagedestroy( $dst );
          }
      }
      public function fixTiny(Request $r, $code)
      {
        define('DEBUG', true);

        error_reporting(E_ALL);
        ini_set('display_errors', DEBUG ? 'On' : 'Off');
        ini_set('memory_limit', '-1');
        if(file_exists(storage_path()."/imagenes/".$code.'.file_tiny'))
          unlink(storage_path()."/imagenes/".$code.'.file_tiny');

        if($this->gen($r,$code))
        {
          return back()->with(["status"=>"Imagen reparada"]);
        }

      }
      public function trash(Request $r,$code)
      {
          $documento = \App\documento::whereRAW("md5(id)='".$code."'")->first();
          return view('users.'.\Auth::user()->level->alias.".documentos.secure",['doc'=>$documento]);
      }
      public function trashe(Request $r)
      {
        $documento = \App\imagen::whereRAW("md5(id)='".$r->id."'")->first();
        $documento->delete();
        return redirect("/imagenes/lista/ver")->with('status','Imagen eliminada');
      }
      public function delete(Request $r,$id){
        $n = "/imagenes/lista/ver";
        $y = "/imagenes/trashe";
        $w = "la imagen seleccionada";
        return view('users.'.Auth::user()->level->alias.".secure_ask",["id"=>$id,"what"=>$w,"noroot"=>$n,"yesroot"=>$y]);
      }
      public function secure(Request $r)
      {
        $d = documento::find($r->get('id'));
        $reporte_id = $d->reporte->id;
        historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha elimiinado la imagen ".$d->title]);
        $d->delete();
        return redirect('/tareas/modify/'.md5($reporte_id).'/')->with('status','Imagen eliminado con exito');
      }
}
