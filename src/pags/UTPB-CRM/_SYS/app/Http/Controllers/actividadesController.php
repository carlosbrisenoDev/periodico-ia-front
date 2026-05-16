<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;


class actividadesController extends Controller
{
    //

    public function create(){
        return view('actividad.create');
    }

    public function list(){
        $acts = \App\actividades::where('usuario_id',auth()->user()->id)->get();
        return view('actividad.list',compact('acts'));
    }

    public function register(Request $request){
        
        $cat_act = \App\catalogo_actividades::where(DB::raw('md5(id)'),$request->actividad)->first();
        $client = \App\cliente::where(DB::raw('md5(id)'),$request->cliente)->first();
        $clientid = null;
        if($client){
            $clientid = $client->id;
        }
        $fechas = explode(" - ", $request->fechaRealizada);
        $dt = json_encode($request->weekday);
        $fi = date("Y-m-d", strtotime($fechas[0]));
        $ff = date("Y-m-d", strtotime($fechas[1]));

        $time = $request->time;
        $actividad = \App\actividades::create([
            'catalogo_actividad_id' => $cat_act->id,
            'usuario_id' => auth()->user()->id,
            'cliente_id' => $clientid,
            'tiempo_tomado' => $time ?? $cat_act->tiempo_promedio,
            'comentario' => $request->comment,
            'fecha_inicio' => $fi,
            'fecha_fin' => $ff,
            'dias_trabajados' => $dt,
            'via_comunicacion' => $request->via_comunicacion,
        ]);
        return redirect(url('/actividad/info/'.md5($actividad->id)));
    }

    public function view($id){
        if(auth()->user()->levels->alias=='administrador' || auth()->user()->id==932 || auth()->user()->id==428 || auth()->user()->id==631){
            $act = \App\actividades::where(DB::raw('md5(id)'),$id)->first();
        }
        else{
            $act = \App\actividades::where(DB::raw('md5(id)'),$id)->where('usuario_id',auth()->user()->id)->first();
        }
        
        if(!$act){
            return redirect('home');
        }
        return view('actividad.view',compact('act'));
    }

    public function createCat()
    {
        return view('actividad.createActividad');
    }
    public function registerCat(Request $request){
        $actividad = \App\catalogo_actividades::create([
            'titulo' => $request->titulo,
            'pasos' => $request->comment,
            'tiempo_promedio' => $request->tiempo,
        ]);
        return redirect(url('/actividadesCatalogo/list'));
    }
    public function listCat(){
        $acts = \App\catalogo_actividades::get();
        return view('actividad.listCatalogo',compact('acts'));
    }

    public function glist(){
        $acts = \App\actividades::get();
        $users = \App\User::whereIn('id',[933,960,891,932,934])->get();
        return view('actividad.generalList',compact('acts','users'));
    }

    public function getByDate(Request $request){    
        $act = collect();
        $fechas = explode(" - ", $request->rangeDate);
        $fi = date("Y-m-d", strtotime($fechas[0]));
        $ff = date("Y-m-d", strtotime($fechas[1]));
        $actq = \App\actividades::where('usuario_id',auth()->user()->id)->whereBetween('fecha_inicio',[$fi,$ff])->whereBetween('fecha_fin',[$fi,$ff])->get();
        if($actq){
            foreach($actq as $a){
                $client = '';
                $hour = $a->tiempo_tomado;
                
                if($a->tiempo_tomado<60){$hour = $a->tiempo_tomado.' minutos';}else{$hour = round($a->tiempo_tomado/60 ,2).' Horas';}    
                if($a->cliente){$client = $a->cliente->full_name();}else{$client = 'No data';}

                $act->push((object)[
                    'id' => md5($a->id),
                    'name' => $a->catalogo_actividades->titulo,
                    'client' => $client,
                    'coment' => $a->comentario ?? 'Sin Comentarios',
                    'time' =>  $hour,
                    'dateS' => \App\Helper\Helper::fechaEs($a->fecha_inicio),
                    'dateE' => \App\Helper\Helper::fechaEs($a->fecha_fin),
                ]);
            }
        }
        
        return $act;
    }
    
    public function getByDateGeneral(Request $request){
        $act = collect();
        $fechas = explode(" - ", $request->rangeDate);
        $fi = date("Y-m-d", strtotime($fechas[0]));
        $ff = date("Y-m-d", strtotime($fechas[1]));
        $min = auth()->user()->tiempoActividadesDate($fi,$ff);
        $hour = $min;
        if($hour<60){
            $hour = $min.' minutos';
        }
        else{
            $hour = round($min/60 ,2).' Horas';
        }  
        $act->push((object)[
            'actividades_realizadas' => auth()->user()->actividadesRealizadasDate($fi,$ff),
            'tiempo_usado' => $hour,
            'last_actividad' => auth()->user()->lastActivityDate($fi,$ff),
        ]);
        return $act;
    }

    public function viewperuser($id){
        $acts = \App\actividades::where(DB::raw('md5(usuario_id)'),$id)->get();
        $user = \App\User::where(DB::raw('md5(id)'),$id)->first();
        return view('actividad.viewuser',compact('acts','user'));
    }

    public function getByDatebyuser(Request $request){
        $act = collect();
        $user = \App\User::where(DB::raw('md5(id)'),$request->user)->first();
        $fechas = explode(" - ", $request->rangeDate);
        $fi = date("Y-m-d", strtotime($fechas[0]));
        $ff = date("Y-m-d", strtotime($fechas[1]));
        $actq = \App\actividades::where('usuario_id',$user->id)->whereBetween('fecha_inicio',[$fi,$ff])->whereBetween('fecha_fin',[$fi,$ff])->get();
        if($actq){
            foreach($actq as $a){
                $client = '';
                $hour = $a->tiempo_tomado;
                
                if($a->tiempo_tomado<60){$hour = $a->tiempo_tomado.' minutos';}else{$hour = round($a->tiempo_tomado/60 ,2).' Horas';}    
                if($a->cliente){$client = $a->cliente->full_name();}else{$client = 'No data';}

                $act->push((object)[
                    'id' => md5($a->id),
                    'name' => $a->catalogo_actividades->titulo,
                    'client' => $client,
                    'coment' => $a->comentario ?? 'Sin Comentarios',
                    'time' =>  $hour,
                    'dateS' => \App\Helper\Helper::fechaEs($a->fecha_inicio),
                    'dateE' => \App\Helper\Helper::fechaEs($a->fecha_fin),
                ]);
            }
        }
        
        return $act;
    }

    public function getByDateGeneralbyuser(Request $request){
        $act = collect();
        $user = \App\User::where(DB::raw('md5(id)'),$request->user)->first();
        $fechas = explode(" - ", $request->rangeDate);
        $fi = date("Y-m-d", strtotime($fechas[0]));
        $ff = date("Y-m-d", strtotime($fechas[1]));
        $min = $user->tiempoActividadesDate($fi,$ff);
        $hour = $min;
        if($hour<60){
            $hour = $min.' minutos';
        }
        else{
            $hour = round($min/60 ,2).' Horas';
        }  
        $act->push((object)[
            'actividades_realizadas' => $user->actividadesRealizadasDate($fi,$ff),
            'tiempo_usado' => $hour,
            'last_actividad' => $user->lastActivityDate($fi,$ff),
        ]);
        return $act;
    }

    public function getAllDataInFile($token){
        if($token=='3d9d79e13fb200c8aed5be35bcccc265'){
            $acts = \App\actividades::get();
            $semana = array(
                "Lunes",
                "Martes",
                "Miercoles",
                "Jueves",
                "Viernes",
                "Sabado",
                "Domingo"
            );
            $list = collect();
            foreach($acts as $index => $a){
                $user = 'Sin datos';
                $via_com = 'Desconocida';
                $time = $a->tiempo_tomado;
                $client = 'Sin datos';
                $days = '';
                $actividad = 'Sin datos';
                if($a->tiempo_tomado<60){
                    $time = $a->tiempo_tomado.' minutos';
                }
                else{
                    $time = round($a->tiempo_tomado/60 ,2).' Horas';
                }   
                if($a->usuario){
                    $user = $a->usuario->name ?? 'Sin datos';
                }
                if($a->dias_trabajados){
                    if(json_decode($a->dias_trabajados)){
                        foreach(json_decode($a->dias_trabajados) as $key => $day) {
                            $days .= $semana[$day];
                            if(count(json_decode($a->dias_trabajados))>0 && $key<(count(json_decode($a->dias_trabajados))-1)){
                                $days .= ', ';
                            }
                        }                    
                    }
                    else{
                        $days = 'Sin datos';
                    }
                }
                else{
                    $days = 'Sin datos';
                }
                if($a->client){
                    $client = $a->client->full_name();
                }
                if($a->catalogo_actividades){
                    $actividad = $a->catalogo_actividades->titulo;
                }
                $comment = $a->comentario;
                $list->push((array)[
                    "id" => $a->id,
                    "actividad" => $actividad,
                    "reponsable" => $user, //$a->usuario->name,
                    "cliente" => $client,
                    "fecha" => $a->fecha_realizacion ?? 'Sin datos',
                    "tiempo_usado" => $time,
                    "comentario" => $comment,
                    "fecha_inicio" => $a->fecha_inicio,
                    "fecha_fin" => $a->fecha_fin,
                    "dias_trabajados" => $days,
                    "via_comunicacion" => $via_com,
                ]); 
            }
            
            $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject);
            
            $csv->insertOne(array_keys($list[0]));
            foreach ($list as $l) {
                $csv->insertOne($l);
            }
            $csv->output('Registro de Actividades - hasta el dia '.\App\Helper\Helper::fechaEswoutC(date("Y-m-d H:i:s")).'.csv');
            
            // return 1;
            return 1;
        }
           return 0;
        
    }
}
