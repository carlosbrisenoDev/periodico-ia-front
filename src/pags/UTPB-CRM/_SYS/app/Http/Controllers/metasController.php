<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class metasController extends Controller
{
    public function metas(){
        return view('metas.metas');
    }

    public function setmeta(Request $request){
        if($request->metaid){
            $meta = \App\metas::where(DB::raw('md5(id)'),$request->metaid)->first();
            $meta->update([
                'meta_mensual' => $request->metam ?? 50,
                'meta_total' => $request->metat ?? 500,
                'equilibrio' => $request->equilibrio ?? 250,
            ]);
            return redirect(url('/metas'))->with("status","Meta Cambiada");
        }
        \App\metas::create([
            'meta_mensual' => $request->metam ?? 50,
            'meta_total' => $request->metat ?? 500,
            'equilibrio' => $request->equilibrio ?? 250,
        ]);
        return redirect(url('/metas'))->with("status","Meta Creada");
    }
}
