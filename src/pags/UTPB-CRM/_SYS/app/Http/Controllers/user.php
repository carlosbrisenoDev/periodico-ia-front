<?php

namespace App\Http\Controllers;
include_once 'newXMLAPI/newXMLAPI.php';

use newXMLAPI\newXMLAPI as api;
use Illuminate\Http\Request;
class user extends Controller
{
    public function create(Request $r)
    {
      $r->validate([
          'name' => 'required|string|max:255',
          'email' => 'string|email|max:255',
          'password' => 'required|string|min:6',
      ]);

      $data = $r->all();
      $u = \App\User::create([
          'name' => $data['name'],
          'email' => $data['email'],
          'level_id' => $data['level_id'],
          'password' => bcrypt($data['password']),
          "codigo2" => $data['password']
      ]);
      if($r->sucursal != "0"){
        $u->sucursal = $r->sucursal;
        $u->save();
      }

      // $api = new api();
      // $username = explode("@",$u->email);

      // $api->createEmailAccount("unisantorizaba.com",$username[0],$data['password']);

      $u->codigo = "UOV".\Carbon\Carbon::parse($u->created_at)->year.$u->id;
      $u->save();
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha creado al usuario ".$u->name]);
      return redirect('/user/modify/'.md5($u->id))->with('status','Usuario creado');
    }
    public function refresh(Request $r)
    {
      $r->validate([
          'name' => 'required|string|max:255',
          'password' => 'required|string|min:6',
      ]);

      $u = \App\User::find($r->get('id'));
      $all = $r->all();$all['password']=bcrypt($all['password']);$u->fill($all);
      $u->save();
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha  modificado ".$u->name]);
      return redirect('/user/modify/'.md5($r->id))->with('status','Usuario modificado con exito');
    }
    public function roles(Request $r)
    {
      $u = \App\User::whereRAW("md5(id) = '".$r->cid."'")->first();
      \App\roles_cliente::where("cliente_id",$u->id)->delete();
      foreach($r->roles as $key=>$rol){
        \App\roles_cliente::create(["cliente_id"=>$u->id,"role_id"=>$rol]);
      }
      return redirect('/user/modify/'.$r->cid)->with('status','Usuario modificado con exito');
    }
    public function seto(Request $r)
    {
      \Auth::user()->codigo2 = $r->codigo;
      \Auth::user()->password = bcrypt($r->codigo);
      \Auth::user()->save();
      return redirect('/bandeja/correo/listar')->with('status','Usuario modificado con exito');
    }
    public function trash(Request $r)
    {
      return view('users.'.\Auth::user()->level->alias.".secure",['id'=>$r->id]);
    }
    public function secure(Request $r)
    {
      $u=\App\User::find($r->get('id'));
      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha eliminado al usuario ".$u->name]);
      $api = new api();
      $username = explode("@",$u->email);

      $api->delAccount();
      $u->delete();
      return redirect('/'.\Auth::user()->level->alias.'/buscar/')->with('status','Usuario eliminado con exito');
    }
    public function modify(Request $r,$id)
    {
      return view('users.'.\Auth::user()->level->alias.".modify",['user'=>\App\User::whereRAW("md5(id)='$id'")->first()]);
    }
    public function search(Request $r)
    {
      return view('users.'.\Auth::user()->level->alias.".list",['users'=>\App\User::whereRAW("email like '%".$r->get('search')."%'")->get()]);
    }
}
