<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class materiasController extends Controller
{
    public function materiasGetAll(Request $request){
        $query = \App\materias_productos::where('producto_id',$request->id)->join('materias','materias.id','materias_productos.materia_id')->select('materias.*','materias_productos.tipo_comision','materias_productos.costo','materias_productos.precio','materias_productos.descuento_max','materias_productos.tipo_descuento','materias_productos.comision')->get();
        return $query;
    }
}
