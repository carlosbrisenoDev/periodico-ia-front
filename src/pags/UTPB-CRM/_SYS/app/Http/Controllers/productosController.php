<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class productosController extends Controller
{
    public function productosGetAll(Request $request){
        $query = \App\empresas::where('slug',$request->slug)->join('empresas_productos','empresas_productos.id_empresa','empresas.id')->join('productos','productos.id','empresas_productos.id_producto')->select('productos.*')->get();
        return $query;
    }

    public function index(){
        return view('productos.list');
    }

    public function create(){
        return view('productos.create');
    }

    public function make(Request $request){
        $product = \App\productos::create([
            'nombre' => $request->name,
            'tipo' => $request->tipo,
            'precio' => $request->precio,
            'costo' => $request->costo,
            'descuento_max' => $request->desc_max,
            'tipo_descuento' => $request->tipoDescuento,
            'comision' => $request->comision,
            'precio_mensualidad' => $request->precioMat,
            'costo_mensualidad' => $request->costoMat,
            'mensualidades' => $request->durationMat,
            'descuento_max_mensualidad' => $request->descMaxMat,
            'tipo_descuento_mensualidad' => $request->tipoDescuentoMat,
            'precio_materia' => $request->precioMat,
            'costo_materia' => $request->costoMat,
            'descuento_max_materia' => $request->descMaxMat,
            'tipo_descuento_materia' => $request->tipoDescuentoMat,
            'comision_mensualidad' => $request->comisionMat,
            'comision_materia' => $request->comisionMat,
        ]);
        return view('productos.create')->with("status","Producto Creado");
    }

    public function edit($id){
        $producto = \App\productos::where('id',$id)->first();
        return view('productos.edit',compact('producto'));
    }

    public function update(Request $request){
        $product = \App\productos::where('id',$request->prod_id)->first()->update([
            'nombre' => $request->name,
            'tipo' => $request->tipo,
            'precio' => $request->precio,
            'costo' => $request->costo,
            'descuento_max' => $request->desc_max,
            'tipo_descuento' => $request->tipoDescuento,
            'comision' => $request->comision,
            'precio_mensualidad' => $request->precioMat,
            'costo_mensualidad' => $request->costoMat,
            'mensualidades' => $request->durationMat,
            'descuento_max_mensualidad' => $request->descMaxMat,
            'tipo_descuento_mensualidad' => $request->tipoDescuentoMat,
            'precio_materia' => $request->precioMat,
            'costo_materia' => $request->costoMat,
            'descuento_max_materia' => $request->descMaxMat,
            'tipo_descuento_materia' => $request->tipoDescuentoMat,
            'comision_mensualidad' => $request->comisionMat,
            'comision_materia' => $request->comisionMat,
        ]);
        return redirect(url('/productos/edit/'.$request->prod_id))->with("status","Producto Actualizado");
    }

    public function destroy(Request $request){
        $product = \App\productos::where('id',$request->prod_id)->first()->delete();
        return redirect(url('/productos/list/'))->with("status","Producto Borrado");
    }
}
