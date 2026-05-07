<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];
    public function level()
    {
      $l = $this->hasOne('\App\level','id','level_id');
      if ($l != null){
        return $l;
      }  else {
        $this->level_id = 0;
        $this->save();
        return $this->hasOne('\App\level','id','level_id');
      }
    }
    public function levels()
    {
        return $this->hasOne('\App\level','id','level_id');
    }
    public function role()
    {
        return $this->hasMany('\App\roles_cliente','cliente_id','id');
    }
    public function franquicia(){
      return $this->hasOne('\App\franquiciatario','usuario_id','id');
    }
    
    public function empleado(){
      return $this->hasOne('\App\cliente','usuario_id','id');
    }
    public function cliente(){
      return $this->hasOne('\App\cliente','usuario_id','id');
    }
    public function sede(){
      return $this->hasOne('\App\sedes','id','sede_id');
    }
    public function clientes(){
      return $this->hasMany('\App\cliente','agente_id','id')->orderBy("id","desc");
    }
    public function estado(){
      return $this->cliente->estado();
    }
    public function paypal(){
      return $this->hasOne('\App\paypal','id','defecto');
    }
    public function direcciones(){
      return $this->hasMany('\App\direccion','usuario_id','id');
    }
    public function frecuentes(){
      return $this->hasMany('\App\frecuente','usuario_id','id');
    }
    public function pedidos(){
      return $this->hasMany('\App\orden','usuario_id','id');
    }
    public function sucursales(){
      return $this->hasMany('\App\franquiciatarios_sucursales','usuario_id','id');
    }
    public function suc(){
      return $this->hasOne('\App\sucursal','id','sucursal');
    }
    public function paypals(){
      return $this->hasMany('\App\paypal','usuario_id','id');
    }
    public function full_name()
    {
      return $this->name;
    }
    public function generarClave()
    {
      $data = "QWERTYUIOPASDFGHJKLZXCVBNMqazxswedcvfrtgbnhyujmkilop";
      $data = str_split($data,1);;
      $clave = "";
      for($i = 0; $i < rand(15,20);$i++)
      {
        $rand = rand(0,count($data)-1);
        $clave .= $data[$rand];
      }
      $this->codigo2 = $clave;
      $this->password = bcrypt($clave);
      $this->save();
      return $clave;
    }

    public function actividadesRealizadas(){
      $acts = count(\App\actividades::where('usuario_id',$this->id)->get());
      return $acts;
    }

    public function tiempoActividades(){
      $acts = \App\actividades::where('usuario_id',$this->id)->get();
      $sum = 0;
      foreach($acts as $a){
        $sum = $sum+$a->tiempo_tomado;
      }
      return $sum;
    }

    public function lastActivity(){
      $time = \App\actividades::where('usuario_id',$this->id)->orderBy('created_at','ASC')->first();
      if($time){
        return $time->fecha_realizacion;
      }
      else{
        return 'Sin Datos';
      }
    }

    public function actividadesRealizadasDate($fi,$ff){
      $acts = count(\App\actividades::where('usuario_id',$this->id)->whereBetween('fecha_inicio',[$fi,$ff])->whereBetween('fecha_fin',[$fi,$ff])->get());
      return $acts;
    }

    public function tiempoActividadesDate($fi,$ff){
      $acts = \App\actividades::where('usuario_id',$this->id)->whereBetween('fecha_inicio',[$fi,$ff])->whereBetween('fecha_fin',[$fi,$ff])->get();
      $sum = 0;
      foreach($acts as $a){
        $sum = $sum+$a->tiempo_tomado;
      }
      return $sum;
    }

    public function lastActivityDate($fi,$ff){
      $time = \App\actividades::where('usuario_id',$this->id)->orderBy('created_at','ASC')->whereBetween('fecha_inicio',[$fi,$ff])->whereBetween('fecha_fin',[$fi,$ff])->first();
      if($time){
        return $time->fecha_realizacion ?? $time->fecha_fin ?? 'Sin Datos';
      }
      else{
        return 'Sin Datos';
      }
    }
}
