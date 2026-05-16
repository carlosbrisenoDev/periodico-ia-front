<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\FranquiciatarioMail;
use App\Mail\anexarSolicitud;
use App\Mail\Autorizado;
use App\Mail\eliminarfranquicia;
use App\Mail\cambiarclave;
use Auth;
use App\User;
class franquiciatarios extends Controller
{
    public function registro(Request $r){
      $franquiciatario = \App\franquiciatario::create($r->all());
      \Mail::to([$franquiciatario->nombre=>$franquiciatario->correo])->send(new FranquiciatarioMail($franquiciatario));
      return redirect("/shirushi/enviado#content")->with("texto","Se ha enviado más información acerca de las Franquicias Shirushi a su correo electrónico");
    }
    public function solicitar(Request $r,$cid){
      $franquiciatario = \App\franquiciatario::whereRAW("md5(id)='".$cid."'")->first();
      if($franquiciatario->status == 1){
        return view("website.solicitar",["franq"=>$franquiciatario]);
      } else {
        return redirect("/shirushi/enlaceinvalido#content");
      }
    }
    public function autorizar(Request $r,$cid){
      $franquiciatario = \App\franquiciatario::whereRAW("md5(id)='".$cid."'")->first();
      $data = [
          'name' => $franquiciatario->nombre,
          'email' => $franquiciatario->correo,
          'level_id' => \App\level::where("name",'=','Franquiciatario')->first()->id,
          'password' => bcrypt("ShirushiMX"),
      ];

      $validator = \Validator::make($data, [
          'email' => 'required|unique:users',
      ]);

      if ($validator->fails()) {
          return redirect('/franquiciatarios/solicitantes/lista')
                      ->with(["error"=>"Ya existe un Franquiciatario con el correo ".$data["email"]]);
      }

      $u = \App\User::create($data);

      $franquiciatario->status = 3;
      $franquiciatario->usuario_id = $u->id;
      $franquiciatario->save();

      $u->codigo = "S".\Carbon\Carbon::parse($u->created_at)->year.$u->id;
      $u->save();
      $sucursal = \App\sucursal::create();

      $sucursal->franquiciatario_id = $u->franquicia->id;
      $sucursal->save();

      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha creado al usuario Franquiciatario ".$u->name]);

      \Mail::to([$franquiciatario->nombre=>$franquiciatario->correo])->send(new Autorizado($franquiciatario,$u));

      return redirect("/franquiciatarios/solicitantes/lista")->with('status','Usuario franquiciatario creado');;
    }
    public function anexarsolicitud(Request $r){
      $franquiciatario = \App\franquiciatario::whereRAW("md5(id)='".$r->cid."'")->first();
      $franquiciatario->status = 2;
      $franquiciatario->save();
      \Mail::to([$franquiciatario->nombre=>$franquiciatario->correo])->send(new anexarSolicitud($franquiciatario));
      return redirect("/shirushi/enviado#content")->with("texto","Su solicitud para formar parte de Shirushi ha sido recibida, será informado vía correo electrónico cuando haya una respuesta por parte de la empresa");
    }
    public function solicitantes(Request $r,$cid){
      $franquiciatarios = \App\franquiciatario::where("status","2")->get();
      return view('users.'.\Auth::user()->level->alias.".franquiciatarios.solicitantes",["franqs"=>$franquiciatarios]);
    }
    public function lista(Request $r,$cid){
      $franquiciatarios = \App\User::whereHas("level",function($q){
        return $q->where("name","Franquiciatario");
      })->get();

      return view('users.'.\Auth::user()->level->alias.".franquiciatarios.lista",["franqs"=>$franquiciatarios]);
    }
    public function baja(Request $r,$cid){
      $franquiciatario = \App\User::whereRAW("md5(id)='".$cid."'")->first();
      $franquiciatario->franquicia->status = 4;
      $franquiciatario->franquicia->save();
      return redirect("/franquiciatarios/lista/franquicias")->with('status','Se ha suspendido el Franquiciatario');
    }
    public function reanudar(Request $r,$cid){
      $franquiciatario = \App\User::whereRAW("md5(id)='".$cid."'")->first();
      $franquiciatario->franquicia->status = 3;
      $franquiciatario->franquicia->save();
      return redirect("/franquiciatarios/lista/franquicias")->with('status','Se ha reanudado el Franquiciatario');
    }
    public function cambiarclave(Request $r, $id){
      $franquiciatario = \App\User::whereRAW("md5(id)='".$id."'")->first();
      $nC = $franquiciatario->generarClave();
      \Mail::to([$franquiciatario->name=>$franquiciatario->email])->send(new cambiarclave($franquiciatario,$nC));
      return redirect("/franquiciatarios/lista/franquicias")->with('status','Se ha enviado una nueva clave al correo del Usuario');
    }
    public function delete(Request $r, $id){
      $n = "/franquiciatarios/solicitantes/lista";
      $y = "/franquiciatarios/trash";
      $w = "la solicitud de franquicia seleccionada";
      return view('users.'.Auth::user()->level->alias.".secure_ask",["id"=>$id,"what"=>$w,"noroot"=>$n,"yesroot"=>$y]);
    }
    public function trash(Request $r){
      $franquiciatario = \App\franquiciatario::whereRAW("md5(id)='".$r->id."'")->first();
      \Mail::to([$franquiciatario->nombre=>$franquiciatario->correo])->send(new eliminarfranquicia($franquiciatario));
      $franquiciatario->delete();
      return redirect("/franquiciatarios/lista/franquicias")->with('status','Solicitud de franquicia eliminada');
    }
}
