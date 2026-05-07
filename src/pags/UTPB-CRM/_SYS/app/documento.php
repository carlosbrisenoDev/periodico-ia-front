<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class documento extends Model
{
    protected $guarded = [];
    public function fa()
   {
     switch($this->ext)
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
    public function fasm()
   {
     switch($this->ext)
     {
       case 'pdf':
         return "fa fa-file-pdf-o";
         break;
       case 'doc':
       case 'docx':
         return "fa fa-file-word-o";
         break;
       case 'png':
       case 'jpg':
       case 'jpeg':
       case 'gif':
       case 'bmp':
         return "fa fa-file-image-o";
         break;
       default:
         return "fa fa-file-o";
         break;
     }
   }
   public function reporte()
   {
     return $this->belongsTo('\App\reporte','reporte_id');
   }
}
