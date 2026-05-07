<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class calculadoraController extends Controller
{
    //

    public function index(){
        return view('calculadora.index');
    }

    public function empresasGetAll(Request $request){
        $query = \App\empresas::where('deleted_at',null)->get();
        return $query;
    }

    public function pasarelasGetAll(Request $request){
        $query = \App\pasarelas::where('deleted_at',null)->get();
        return $query;
    }

    public function productosGetAll(Request $request){
        $query = \App\empresas::where('slug',$request->slug)->join('empresas_productos','empresas_productos.id_empresa','empresas.id')->join('productos','productos.id','empresas_productos.id_producto')->select('productos.*')->get();
        return $query;
    }

    public function saveData(Request $request){
        $data_json = array(
            'curso' => $request->curso ?? null,
            'descuento' => $request->desc ?? null,
            'descuento_adicional' => $request->desc_ad ?? null,
            'descuento_adicional_materia' => $request->desc_ad_materia ?? null,
            'descuento_materia' => $request->desc_materia ?? null,
            'empresa' => $request->empresaData ?? null,
            'materia' => $request->matr ?? null,
            'pasarela' => $request->pas ?? null,
            'pasarela_materia' => $request->pas_materia ?? null,
            'producto' => $request->prod ?? null,
        );
        $calculadora_data = \App\registros_calculadora::create([
            'usuario_id' => auth()->user()->id,
            'data_calc_json' => json_encode($data_json)
        ]);
        return json_encode($calculadora_data);
        //a
        
    }

    public function misDatos(){
        return view('calculadora.misDatos');
    }

    public function datosGenerales(){
        return view('calculadora.datosGenerales');

    }
    public function registro($id){
        $registro_data = \App\registros_calculadora::where(DB::raw('md5(id)'),$id)->first();
        return view('calculadora.registro',compact('registro_data'));

    }
}
