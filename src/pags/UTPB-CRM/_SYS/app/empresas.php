<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class empresas extends Model
{
    protected $guarded = [];
    protected $table = "empresas";

    public function productos(){
        $productos = \App\productos::where('productos.deleted_at',null)
        ->join('empresas_productos','empresas_productos.id_producto','productos.id')
        ->join('empresas','empresas.id','empresas_productos.id_empresa')
        ->where('empresas.id',$this->id)
        ->select('productos.*')
        ->get();
        return $productos;
    }

    public function nonProductos(){
        $productos_obtenidos = $this->productos();
        $empresa = $this;
        $productos = \App\productos::whereNotIn('id', function($query) use ($empresa) {
            $query->select('id_producto')
                  ->from('empresas_productos')
                  ->where('id_empresa', $empresa->id);
        })->get();
        return $productos;
    }
}
