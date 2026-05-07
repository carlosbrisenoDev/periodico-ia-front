<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class empresasController extends Controller
{
    public function index(){
        return view('empresas.list');
    }
    public function create(){
        return view('empresas.create');
    }

    public function make(Request $request){
        $empresa = \App\empresas::create([
            'nombre' => $request->name,
            'slug' => $request->slug,
        ]);
        return redirect(url('/empresas/edit/'.$empresa->id))->with("status","Empresa Creada");
        // return view('empresas.create')->with("status","Empresa Creada");
    }

    public function edit($id){
        $empresa = \App\empresas::where('id',$id)->first();
        return view('empresas.edit',compact('empresa'));
    }

    public function update(Request $request){
        $empresa = \App\empresas::where('id',$request->empr_id)->first()->update([
            'nombre' => $request->name,
            'slug' => $request->slug,
        ]);
        return redirect(url('/empresas/edit/'.$request->empr_id))->with("status","Empresa Actualizada");
    }

    public function destroy(Request $request){
        $empresa = \App\empresas::where('id',$request->empr_id)->first()->delete();
        return redirect(url('/empresas/list/'))->with("status","Empresa Borrada");
    }
    
    public function empresas_productos(Request $request){
        $empresa = \App\empresas::where('id',$request->empr_id)->first();
        foreach($empresa->productos() as $productos_empresa){
            if(!in_array($productos_empresa->id,$request->in)){
                \App\empresas_productos::where('id_empresa',$empresa->id)->where('id_producto',$productos_empresa->id)->first()->delete();
            }
        }

        foreach($request->in as $productos_empresa_new){
            if(!in_array($productos_empresa_new,$empresa->productos()->pluck('id')->toArray())){
                \App\empresas_productos::create([
                    'id_empresa' => $empresa->id,
                    'id_producto' => $productos_empresa_new,
                ]);
            }
        }

        return redirect(url('/empresas/edit/'.$empresa->id))->with("status","Cambios Guardados");
    }
}
