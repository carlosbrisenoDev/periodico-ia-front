<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class tarjeta extends Controller
{
  public function generar(Request $request, $id)
  {
      $ciudadano = \App\ciudadano::whereRAW('md5(id)="'.$id.'"')->first();
      return ($ciudadano->sicargo != "1") ? $this->ciudadano($request,$id,$ciudadano) : $this->empleado($request,$id,$ciudadano);
  }
  public function ciudadano(Request $request,$id,$ciudadano)
  {
    $img = \Image::make('images/credencial.jpg');
    if(!file_exists(storage_path()."/perfil/".md5($ciudadano->id).".png"))
    {
      $img2 = \Image::make("images/logo_mini.jpg");
    } else {
      $img2 = \Image::make(storage_path()."/perfil/".md5($ciudadano->id).".png");
    }
    $img2->resize(265,377);
    $img->insert($img2, 'top-left', 0, 423);
    $codigo = $ciudadano->codigo;
    $img->text($codigo,326,980,function($font){
      $font->file('fonts/beba.ttf');
      $font->size(40);
      $font->align("center");
      $font->color("#36578C");
    });
    $img->text($ciudadano->nombre,460,520,function($font){
      $font->file('fonts/beba.ttf');
      $font->size(40);
      $font->align("center");
      $font->color("#36578C");
    });
    $img->text($ciudadano->apellidopat." ".$ciudadano->apellidomat,460,580,function($font){
      $font->file('fonts/beba.ttf');
      $font->size(40);
      $font->align("center");
      $font->color("#36578C");
    });
    $img->text($ciudadano->curp,460,655,function($font){
      $font->file('fonts/beba.ttf');
      $font->size(40);
      $font->align("center");
      $font->color("#333");
    });
    // back
    $img3 = \Image::make('images/back.jpg');
    $codigo = $ciudadano->codigo;
    $img3->insert(\QrCode::format("png")->size(300,300)->generate($ciudadano->codigo), 'top-left', 0, 640);
    return view('users.'.\Auth::user()->level->alias.".tarjeta",['data'=>[$img->encode('data-url'),$img3->encode('data-url')]]);

  }
  public function empleado(Request $request,$id,$ciudadano)
  {
    $img = \Image::make('images/empleado.jpg');
    if(!file_exists(storage_path()."/perfil/".md5($ciudadano->id).".png"))
    {
      $img2 = \Image::make("images/logo_mini.jpg");
    } else {
      $img2 = \Image::make(storage_path()."/perfil/".md5($ciudadano->id).".png");
    }
    $img2->resize(238,336);
    $img->insert($img2, 'top-left', 207, 260);
    $img->text($ciudadano->codigo,326,955,function($font){
      $font->file('fonts/spm.ttf');
      $font->size(30);
      $font->align("center");
      $font->color("#FFE817");
    });
    $img->text(strtoupper($ciudadano->nombre." ".$ciudadano->apellidopat." ".$ciudadano->apellidomat),326,720,function($font){
      $font->file('fonts/nsstdb.otf');
      $font->size(40);
      $font->align("center");
      $font->color("#FFF");
    });
    $img->text(strtoupper($ciudadano->cargo),326,755,function($font){
      $font->file('fonts/nspbi.ttf');
      $font->size(30);
      $font->align("center");
      $font->color("#FFF");
    });
    // back
    $img3 = \Image::make('images/empleado_back.jpg');
    $codigo = $ciudadano->codigo;
    $img3->insert(\QrCode::format("png")->size(180,180)->generate($ciudadano->codigo), 'bottom-right', 77, 75);
    return view('users.'.\Auth::user()->level->alias.".tarjeta",['data'=>[$img->encode('data-url'),$img3->encode('data-url')]]);
}
  public function modify(Request $r,$id)
  {
    return view('users.'.\Auth::user()->level->alias.".credencial.modify",['ciudadano'=>\App\ciudadano::whereRAW("md5(id)='$id'")->first()]);
  }
  public function refresh(Request $r)
  {
    $ciudadano = \App\ciudadano::find($r->id);
    $ciudadano->cargo = $r->cargo;
    $ciudadano->sicargo = ($r->empleado == "1") ? 1 : 0;
    $ciudadano->save();
    $img = $_POST['imagen'];
    $img = str_replace('data:image/png;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    $file = storage_path()."\\perfil\\".md5($r->id).".png";
    $success = file_put_contents($file, $data);
    return redirect('/tarjeta/modify/'.md5($r->id))->with('status','Fotografía actualizada');
  }
}
