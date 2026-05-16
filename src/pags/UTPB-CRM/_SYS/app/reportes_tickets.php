<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class reportes_tickets extends Model
{
    protected $guarded = [];
    protected $table = "reportes_tickets";

    public function respuestas(){
        return $this->hasMany('\App\respuestas_reportes','id_reporte_ticket','id')->orderBy('created_at','DESC');
    }

    public function ultimasRespuestas(){
        return $this->hasMany('\App\respuestas_reportes','id_reporte_ticket','id')->orderBy('created_at','DESC')->limit(3);
    }

    public function user(){
        return $this->hasOne('\App\User','id','user_id');
    }

    public function prioridades()
    {
        return $this->hasOne('\App\prioridad','id','prioridad');
    }

    public function ciudadano()
    {
      return $this->hasOne('\App\ciudadano','id','ciudadano_id');
    }

    public function documentos()
    {
      return $this->hasMany('\App\documento','reporte_id','id');
    }

    public function getUsuariosName()
    {
        if($this->usuarios && $this->usuarios!='null'){
            $usuarios = \App\User::whereIn('id',json_decode($this->usuarios))
            ->orderBy('created_at','DESC')
            ->select('name')
            ->get();
            return $usuarios;
    
        }
        return false;
    }
}
