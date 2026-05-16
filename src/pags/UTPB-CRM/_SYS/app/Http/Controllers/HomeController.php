<?php

namespace App\Http\Controllers;
include_once 'newXMLAPI/newXMLAPI.php';

use newXMLAPI\newXMLAPI as api;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      try {
        return view('users.'.Auth::user()->level->alias.".default");
      } catch (\Exception $e) {
        return view('default');
      }

    }

    public function test(){
      $clients = \App\cliente::where('tag',3)->get();
      foreach($clients as $c){
        $c->update(['tag'=>1]);
      }
      dd($clients);
    }

    public function editmyinfo($id){
      $user = \App\User::where(DB::raw('md5(id)'),$id)->first();
      if(!$user){
        return redirect('/home');
      }
      if($user->id != auth()->user()->id){
        return redirect('/home');
      }
      return view('perfil.edit',compact('id','user'));
    }

    public function updateinfo(Request $request){
      $user = auth()->user();
      $ccusr = $request->ccuser ?? null;
      $ccpasswrd = $request->ccuser ?? null;
      $user->update([
        'name' => $request->name,
        // 'email' => $request->email,
        'telefono' => $request->phone,
        'cargo' => $request->cargo,
        'ccuser' => $ccusr,
        'ccpassword' => $ccpasswrd
      ]);
      return redirect()->back()->with("status","Respuesta Enviada");
    }
}
