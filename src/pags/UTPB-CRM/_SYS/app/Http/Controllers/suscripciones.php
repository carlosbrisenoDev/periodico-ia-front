<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class suscripciones extends Controller
{
    function create(Request $r){
        $data = [
            "titulo" => $r->titulo
        ];
        $sus = \App\suscripciones::create($data);
        return json_encode(["cid" => md5($sus->id)]);
    }

    public function removeiBrochure(Request $r){
        $si = \App\suscripciones_ibrochures::whereRAW("md5(id)='$r->ibrochure'")->first();
        if($si){
            $si->delete();
        }
        return json_encode(["ok"=>"ok"]);
    }

    function addiBrochure(Request $r){
        \App\suscripciones_ibrochures::create([
            "suscripcion_id" => $r->suscripcion_id,
            "launch_at" => $r->minutos,
            "ibrochure_id" => $r->ibrochure_id
        ]);

        return json_encode(["ok"=>"ok"]);
    }

    public function addCliente(Request $r){
        $cliente = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
        if($cliente && $r->v != 0){
            $cs = \App\clientes_suscripciones::create([
                "suscripcion_id" => $r->v,
                "cliente_id" => $cliente->id,
                "start_at" => time()
            ]);
            foreach($cs->suscripcion->ibrochures as $item){
                \App\cliente_suscripciones_ibrochures::create([
                    "ibrochure_id" => $item->ibrochure_id,
                    "cliente_suscripcion_id" => $cs->id,
                    "status" => 0,
                    "expire_at" => strtotime('+ '.$item->launch_at.' minutes', $cs->start_at)
                ]);
            }
        }
        return "Ok";
    }

    public function deleteCliente(Request $r){
        $cliente = \App\clientes_suscripciones::whereRAW("md5(id)='$r->csid'")->first();
        if($cliente){
            $cliente->cliente_suscripciones_ibrochures->each(function($item){
                $item->delete();
            });
            $cliente->delete();
        }
        return json_encode(["ok"=>"ok"]);
    }
}
