<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class files extends Controller
{
    public function uploadimage(Request $r){
        if($r->hasFile("file")){
          $file = $r->file("file");
          $filedata = file_get_contents($file->getRealPath());
          $data = explode('.',$file->getClientOriginalName());
          $name = "";
          for($k = 0; $k < count($data)-1;$k++)
          {
           $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
          }
          $ext = $data[count($data)-1];
          $document = \App\documento::create(['size'=>$file->getSize(),'ext'=>$ext,'titulo'=>$name,'empleado_id'=>\Auth::user()->id]);
          \Storage::cloud('s3')->put(md5($document->id).".file",$filedata);
          $datos["location"] = \Cloud::url($document->id);
          return json_encode($datos);
        }
    }
    public function postpath(Request $r){
        $document_id = 0;
        if($r->hasFile('file')){
          $file = $r->file('file');
          $filedata = file_get_contents($file->getRealPath());
          $data = explode('.',$file->getClientOriginalName());
          $name = "";
          for($k = 0; $k < count($data)-1;$k++)
          {
           $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
          }
          $ext = $data[count($data)-1];
          $document = \App\documento::create(['size'=>$file->getSize(),'ext'=>$ext,'titulo'=>$name,'empleado_id'=>\Auth::user()->id]);
          //$file->move(storage_path()."/pathname/",md5($document->id).'.file');
          $document_id = $document->id;
          $rd = \App\filesfrompath::create([
            "document_id" => $document_id,
            "filename" => $name,
            "pathname" => $r->pathname
          ]);
          $data["cid"] = md5($rd->id);
          $data["document_id"] = md5($rd->document_id);
          $data["filename"] = $rd->filename;
          \Storage::cloud("s3")->put(md5($document->id).".file",$filedata);
          $data["url"] = \Cloud::url($document->id);
        }
        $data["document_id"] = md5($document_id);
        return json_encode($data);
    }
    public function getfrompath(Request $r){
        $files = \App\filesfrompath::selectRAW("*, md5(document_id) as cid")->where("pathname",$r->pathname);
        $datos = [];
        $files->each(function($item) use(&$datos){
          array_push($datos,[
            "document_id" => $item->document_id,
            "filename" => $item->filename,
            "pathname" => $item->pathname,
            "md5" => md5($item->id),
            "url" => \Cloud::url($item->document_id)
          ]);
        });
        return json_encode($datos);
    }
}
