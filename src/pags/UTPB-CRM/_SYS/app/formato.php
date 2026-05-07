<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class formato extends Model
{
  protected $guarded = [];

  public function get($id)
  {
    $doc = null;
    foreach ($this->documentos() as $key => $value) {
      if($id == md5($key))
        $doc = $value;
    }
    return $doc;
  }
  public function fa()
  {
   switch($this->formato)
   {
     case 'pdf':
       return "fa fa-file-pdf-o fa-5x";
       break;
     case 'doc':
     case 'docx':
       return "fa fa-file-word-o fa-5x";
       break;
     case 'png':
     case 'jpg':
     case 'jpeg':
     case 'gif':
     case 'bmp':
       return "fa fa-file-image-o fa-5x";
       break;
     default:
       return "fa fa-file-o fa-5x";
       break;
   }
  }
  public function nombre()
  {
    return ($this->get($this->documento))["nombre"];
  }
  public function nombrefull()
  {
    return ($this->get($this->documento))["nombre"].".".($this->get($this->documento))["formato"];
  }
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
  public function full_fecha()
  {
    setlocale(LC_ALL, 'Spanish');
    return \Carbon\Carbon::parse($this->created_at)->formatLocalized('%d %B %Y');
  }
}
