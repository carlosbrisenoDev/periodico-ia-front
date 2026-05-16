<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cliente extends Model
{
  protected $table = "clientes";
  protected $guarded = [];

  public function usuario(){
    return $this->hasOne('\App\User','id','usuario_id');
  }
  public function agente(){
    return $this->hasOne('\App\User','id','agente_id');
  }
  public function firma(){
    return $this->hasOne('\App\firma','cliente_id',"id");
  }
  public function comprobante_pago(){
    return $this->hasOne('\App\documento','id','comprobante');
  }
  public function isinscripcion(){
    return $this->hasOne('\App\inscripciones','cliente_id','id');
  }
  public function get_comprobante(){
    return $this->hasOne('\App\documento','id','comprobante');
  }
  public function calls(){
    return $this->hasMany('\App\call','cliente_id','id');
  }
  public function encuestas(){
    return $this->hasMany('\App\cliente_encuesta','cliente_id','id');
  }
  public function materias(){
    return $this->hasMany('\App\materias','matricula','matricula');
  }
  public function leads(){
    return $this->hasMany('\App\leads','recipient','recipient')->orderBy("id","asc");
  }
  public function cinfo(){
    if($this->credito_info != null){
      return $this->credito_info;
    } else {
      $c = \App\credito_info::create(["cliente_id"=>$this->id]);
      return $c;
    }
  }
  public function carteras(){
    return $this->hasMany('\App\cartera','cliente_id','id');
  }
  public function ccredito(){
    if($this->cocredito != null){
      return $this->cocredito;
    } else {
      $c = \App\credito::create(["cliente_id"=>$this->id]);
      return $c;
    }
  }
  public function credito_info(){
    return $this->hasOne('\App\credito_info','cliente_id','id');
  }
  public function cocredito(){
    return $this->hasOne('\App\credito','cliente_id','id');
  }
  public function cid(){
    return md5($this->id);
  }
  public function agenda(){
    return $this->hasMany('\App\evento','cliente_id','id');
  }
  public function inscripcion(){
    return $this->hasOne('\App\User','id','usuario_id');
  }
  public function full_name(){
    return mb_strtoupper($this->nombre." ".$this->apat." ".$this->amat);
  }
  public function suspendido(){
    return ($this->status == 4);
  }
  public function documentos()
  {
    return $this->hasMany('\App\documento','empleado_id','id');

  }
  public function documentosc()
  {
    return $this->hasMany('\App\documento','empleado_id','id')->where("type","credit");

  }
  public function estado(){
    switch($this->status){
      case 4:
        return "Alumno";
        break;
      case 3:
        return "Documentos enviados para revisión";
        break;
      case 2:
        return "Subiendo documentos";
        break;
      default:
        return "Desconocido";
        break;
    }
  }

  public function tagsel($tagid){
    if($tagid == $this->tag){
      return 'selected';
    }
    else{
      return '';
    }
  }

  public function psel($pid){
    if($this->oferta && ($pid == $this->oferta->id)){
      return 'selected';
    }
    else{
      return '';
    }
  }

  public function oferta(){
    return $this->hasOne('\App\productos','id','producto_id');
  }

  public function tags(){
    return $this->hasOne('\App\tag','id','tag');
  }
}
