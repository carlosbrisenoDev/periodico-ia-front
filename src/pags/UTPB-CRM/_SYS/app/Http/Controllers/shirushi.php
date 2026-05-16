<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class shirushi extends Controller
{
    public function sucursal(Request $r,$cid){
      return view('shirushi.sucursal',["suc"=>\App\sucursal::where('nombre',$cid)->first()]);
    }
}
