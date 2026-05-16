<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ClienteMail;
use App\Mail\anexarSolicitud;
use App\Mail\Autorizado;
use App\Mail\eliminarfranquicia;
use App\Mail\cambiarclave;
use Auth;
use App\User;
class usuarios extends Controller
{
    public function registro(Request $r){
      $data = [
          'email' => $r->correo
      ];

      $validator = \Validator::make($data, [
          'email' => 'required|unique:users',
      ]);

      if ($validator->fails()) {
          return redirect('/shirushi/pideadomicilio')
                      ->with("error","Ya existe un Usuario con el correo ".$data["email"]);
      }

      $cliente = \App\cliente::create($r->all());
      \Mail::to([$cliente->nombre=>$cliente->correo])->send(new ClienteMail($cliente));
      return redirect("/shirushi/enviado#content")->with("texto","Confirma tu dirección de correo electrónico para continuar con tu registro");
    }
    public function confirmar(Request $r,$cid){
        return view("shirushi.cliente_clave",["cid"=>$cid]);
    }
    public function setsign(Request $r){
      \Auth::user()->sign = $r->sign;
      \Auth::user()->save();
      return redirect("/bandeja/nuevo/correo")->with("status","Firma actualizada");
    }
    public function concluir(Request $r){
      $cliente = \App\cliente::whereRAW("md5(id)='".$r->cid."'")->first();

      $data = $r->all();
      unset($data["cid"]);

      $u = \App\User::create($data);
      $u->password = bcrypt($u->password);
      $u->email = $cliente->correo;
      $u->name = $cliente->nombre;

      $cliente->status = 3;
      $cliente->usuario_id = $u->id;

      $u->codigo = "UOV".\Carbon\Carbon::parse($u->created_at)->year.$u->id;
      $u->save();


      \App\historial::create(["usuario_id"=>0,'accion'=>"Nuevo usuario registrado como cliente ".$u->name]);

      Auth::login($u);

      return redirect("/")->with("status","Bienvenido a tu cuenta de Shirushi");
    }

    public function close(Request $r){
      \Auth::user()->hide = (\Auth::user()->hide == 0) ? 1 : 0;
      \Auth::user()->save();
      return redirect()->back()->with("status","Panel lateral modificado");
    }
    public function cambiarclave(Request $r, $id){
      $franquiciatario = \App\User::whereRAW("md5(id)='".$id."'")->first();
      $nC = $franquiciatario->generarClave();
      \Mail::to([$franquiciatario->name=>$franquiciatario->email])->send(new cambiarclave($franquiciatario,$nC));
      return redirect("/franquiciatarios/lista/franquicias")->with('status','Se ha enviado una nueva clave al correo del Usuario');
    }

}
