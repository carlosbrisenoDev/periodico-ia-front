<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use \App\Mail\Empleado;
use \App\Mail\AutorizadoEmpleado;
use \App\Mail\Archivado;
use \App\Mail\Rechazado;
use \App\Mail\Contratado;
class empleados extends Controller
{
    public function  registro(Request $r){
      $empleado = \App\empleado::create($r->all());
      \Mail::to([$empleado->nombre=>$empleado->correo])->send(new Empleado($empleado));
      return redirect("/shirushi/enviado#content")->with('texto','Revisa y confirma tu correo electrónico para continuar');
    }
    public function  archivar(Request $r,$cid){
      $empleado = \App\empleado::whereRAW("md5(id)='".$cid."'")->first();
      $empleado->status = 4; // archivado
      $empleado->save();
      \Mail::to([$empleado->nombre=>$empleado->correo])->send(new Archivado($empleado));
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Archivo al solicitante de empleo ".$empleado->nombre]);

      return redirect("/empleados/empleado/".md5($empleado->id))->with('status','La solicitud ha sido archivada');
    }
    public function  despedir(Request $r, $id){
      $n = "/empleados/empleado/$id";
      $y = "/empleados/trash";
      $w = " y despedir al empleado";

      return view('users.'.Auth::user()->level->alias.".secure_ask",["id"=>$id,"what"=>$w,"noroot"=>$n,"yesroot"=>$y]);
    }
    public function trash(Request $r){
      $empleado = \App\empleado::whereRAW("md5(id)='".$r->id."'")->first();
      $empleado->status = 6; // archivado
      $empleado->save();
      $empleado->usuario->delete();
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Despidio del sistema al empleado ".$empleado->nombre]);
      return redirect("/empleados/empleado/".md5($empleado->id))->with('status','El empleado ha sido despedido (archivo)');
    }

    public function  contratar(Request $r,$cid){
      $empleado = \App\empleado::whereRAW("md5(id)='".$cid."'")->first();
      $empleado->status = 5; // archivado
      $empleado->save();
      \Mail::to([$empleado->nombre=>$empleado->correo])->send(new Contratado($empleado));
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Contrato al empleado ".$empleado->nombre]);

      return redirect("/empleados/empleado/".md5($empleado->id))->with('status','El empleado ha cambiado a Contratado');
    }
    public function  rechazar(Request $r,$cid){
      return view("users.rh.razon",["id"=>$cid]);
    }
    public function  rechazado(Request $r){
      $empleado = \App\empleado::whereRAW("md5(id)='".$r->cid."'")->first();
      \Mail::to([$empleado->nombre=>$empleado->correo])->send(new Rechazado($empleado,$r->razon));
      $usuario = $empleado->usuario;
      if($usuario != null){
        $usuario->delete();
      }
      $empleado->delete();
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Rechazo la solicitud de empleado de  ".$empleado->nombre]);
      return redirect("/rh/solicitudes")->with('status','La solicitud ha sido rechazada');

    }
    public function  desarchivar(Request $r,$cid){
      $empleado = \App\empleado::whereRAW("md5(id)='".$cid."'")->first();
      $empleado->status = 3; // archivado
      $empleado->save();
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Desarchivo del sistema al empleado ".$empleado->nombre]);
      return redirect("/empleados/empleado/".md5($empleado->id))->with('status','La solicitud ha sido desarchivada');
    }
    public function empleado(Request $r,$cid){
      $empleado = \App\empleado::whereRAW("md5(id)='".$cid."'")->first();
      return view("users.".Auth::user()->level->alias.".empleado",["empleado"=>$empleado]);
    }
    public function solicitar(Request $r,$cid){
      $empleado = \App\empleado::whereRAW("md5(id)='".$cid."'")->first();
      if($empleado->status == 1){
        return view("website.solicitarempleado",["empleado"=>$empleado]);
      } else {
        return redirect("/shirushi/enlaceinvalido#content");
      }
    }
    public function rh(Request $r){
      $empleado = \Auth::user()->empleado;
      $empleado->status = 3; // En revisión
      $empleado->save();
      return redirect("/empleados/informacion")->with('status','Su estado ha sido cambiado a En revisión');

    }
    public function anexarsolicitud(Request $r){
      $empleado = \App\empleado::whereRAW("md5(id)='".$r->cid."'")->first();

        $data = [
            'name' => $empleado->nombre,
            'email' => $empleado->correo,
            'level_id' => \App\level::where("name",'=','Empleado')->first()->id,
            'password' => bcrypt("ShirushiMX"),
        ];

        $validator = \Validator::make($data, [
            'email' => 'required|unique:users',
        ]);

        if ($validator->fails()) {
            return redirect('/shirushi/enviado#content')
                        ->with("texto","Ya existe un Usuario registrado con el correo ".$data["email"]);
        }

        $u = \App\User::create($data);
        $all = $r->all();
        unset($all["cid"]);
        $empleado->usuario_id = $u->id;
        $empleado->fill($all);
        $empleado->status = 2; // Significa acceso a subir su información
        $empleado->save();

        $u->codigo = "S".\Carbon\Carbon::parse($u->created_at)->year.$u->id;
        $u->save();


        \App\historial::create(["usuario_id"=>0,'accion'=>"Ha creado al usuario Solicitante de empleo ".$u->name]);

        \Mail::to([$empleado->nombre=>$empleado->correo])->send(new AutorizadoEmpleado($empleado,$u));


        return redirect("/shirushi/enviado#content")->with("texto","Se ha creado tu cuenta de Empleado, revisa tu correo electrónico para obtener tu cuenta de acceso");
    }
}
