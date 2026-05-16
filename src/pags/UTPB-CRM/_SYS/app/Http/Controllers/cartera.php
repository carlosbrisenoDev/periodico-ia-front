<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\documento;
use App\historial;
use \App\Mail\TablaPagos;
use \App\Mail\VideoFirma;

class cartera extends Controller
{
    protected $meses_ingles = array("January","February","March","April","May","June","July","August","September","October","November","December");
    protected $meses_espanol = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre',);
    protected $dim2Mes = ["Jan" => "Enero","Feb" => "Febrero","Mar" => "Marzo","Apr" => "Abril","May" => "Mayo","Jun" => "Junio","Jul" => "Julio","Aug" => "Agosto","Sep" => "Septiembre","Nov" => "Noviembre","Oct" => "Octubre","Dec" => "Diciembre",];
    protected $dim2Month = [];

    public function __construct(){
      $this->dim2Month = array_flip($this->dim2Mes);
    }

    public function nuevo(Request $r){
      $c = \App\cliente::whereRAW("md5(id)='".$r->cid."'")->first();
      if(count($c->carteras) > 0){
        \App\cartera::create(["cliente_id"=>$c->id,"concepto"=>"Sin definir"]);
      } else {
        $fech = $c->isinscripcion->periodo;
        for ($i=0; $i < count($this->meses_ingles); $i++) {
          $fech = str_replace($this->meses_espanol[$i],$this->meses_ingles[$i],$fech);
        }
        $anio = \Carbon\carbon::parse($c->isinscripcion->created_at)->format("Y");
        $mes_pago = \Carbon\carbon::parse($fech." ".$anio);
        $mes = \Carbon\carbon::parse($fech." ".$anio)->addMonth(1);
        \App\cartera::create(["cliente_id"=>$c->id,
                              "concepto"=>"Crédito de estudios",
                              "fecha_estudio"=>$mes_pago,
                              "fecha_inicio"=>$mes,
                              "interes"=>$c->credito,
                              "plazo"=>$c->plazo
                            ]);
      }
      return redirect("/creditos/creditos?cid=".$r->cid)->with('status','Se ha creado un crédito nuevo');
    }
    public function restructurar(Request $r){
      $tabla = \App\tablapagos::whereRAW("md5(id)='".$r->cid."'")->first();
      $c = $tabla->cliente;
      $cartera = $tabla->cartera;
      $totalpagos = 0.0;
      foreach ($tabla->pagos as $pago) {
        $totalpagos += str_replace(",","",str_replace("$","",$pago->pago))*1;
      }
      \App\cartera::create(["cliente_id"=>$c->id,
                            "concepto"=>"Restructuración crédito de estudios",
                            "fecha_estudio"=>$cartera->fecha_estudio,
                            "fecha_inicio"=>$cartera->fecha_inicio,
                            "interes"=>$cartera->interes,
                            "plazo"=>$cartera->plazo,
                            "valor_materia"=>$cartera->valor_materia-($totalpagos/40),
                            "valor_titulo"=>$cartera->valor_titulo
                          ]);
      return redirect("/creditos/creditos?cid=".md5($c->id))->with('status','Restructuración de crédito con éxito');
    }
    public function eliminartabla(Request $r){
      $tabla = \App\tablapagos::whereRAW("md5(id)='".$r->cid."'")->first();
      \App\pagos::where("tabla_id",$tabla->id)->delete();
      $tabla->delete();
      return redirect("/creditos/cartera?cid=".md5($tabla->cartera->id))->with('status','Tabla de pagos eliminada');
    }
    public function pausar(Request $r){
      $tabla = \App\tablapagos::whereRAW("md5(id)='".$r->cid."'")->first();
      $tabla->status = 1;
      $tabla->save();
      return redirect("/creditos/cartera?cid=".md5($tabla->cartera->id))->with('status','Tabla pausada');
    }
    public function derivar(Request $r){
      $tabla = \App\tablapagos::whereRAW("md5(id)='".$r->cid."'")->first();
      \App\pagos::where("tabla_id",$tabla->id)->where("pagado",NULL)->delete();
      $tabla->status = 1;
      $tabla->derivada = 1;
      $tabla->save();
      return redirect("/creditos/cartera?cid=".md5($tabla->cartera->id))->with('status','Tabla en proceso de derivación');
    }
    public function play(Request $r){
      $tabla = \App\tablapagos::whereRAW("md5(id)='".$r->cid."'")->first();
      $tabla->status = NULL;
      $tabla->save();
      return redirect("/creditos/cartera?cid=".md5($tabla->cartera->id))->with('status','Tabla pausada');
    }
    public function eliminarcartera(Request $r){
      $cartera = \App\cartera::whereRAW("md5(id)='".$r->cid."'")->first();
      \App\firma::where("cartera_id",$cartera->id)->delete();
      $c = $cartera->cliente;
      $cartera->delete();
      return redirect("/creditos/creditos?cid=".md5($c->id))->with('status','Cartera eliminada');
    }
    public function eliminar(Request $r){
      $pago = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
      \Storage::delete(storage_path('/'.md5($pago->pagado).'.file'));
      $d = documento::find($pago->pagado);
      historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha eliminado el archivo ".$d->title]);
      $d->delete();
      $pago->pagado = null;
      $pago->save();
      return redirect("/creditos/cartera?cid=".md5($pago->tabla->cartera->id))->with('status','Comprobante eliminado');
    }
    public function extrapay(Request $r){
      $pago = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();

      \App\pagos::where("tabla_id",$pago->tabla_id)->where("pagado",NULL)->where("extra",NULL)->delete();

      $last_p = \App\pagos::where("tabla_id",$pago->tabla_id)->where("pagado","<>",NULL)->orderBy("updated_at","DESC")->first();
      $pagados = \App\pagos::where("tabla_id",$pago->tabla_id)->where("pagado","<>",NULL)->where("extra",NULL)->get();

      $_data = $pago->toArray();
      unset($_data["id"]);
      $_data["numero"] = "E-".$_data["numero"];
      $_data["pago"] = '$'.number_format($r->cargo,2,".",",");
      $_data["extra"] = $pago->id;
      $ac = str_replace("$","",str_replace(",","",$last_p->acumulado)) + $r->cargo;
      $_data["acumulado"] = '$'.number_format($ac,2,".",",");
      $_data["pagado"] = NULL;
      $_data["recibo"] = NULL;
      $_data["capital"] = '$'.number_format($r->cargo,2,".",",");
      //dd($last_p);
      $extra = \App\pagos::create($_data);

      $c = $extra->tabla->cartera;
      $acumulado = $ac;

      $mes_pago = \Carbon\carbon::parse($last_p->anio."-".$last_p->mes_en."-01")->addMonth(1);
      $fecha = $mes_pago;

      $capital = $c->total - $ac;
      $total = $capital;
      $capital = $capital/($c->plazo-count($pagados));
      $pago_mes = (($total * $c->interes / 100) + $total)/($c->plazo-count($pagados));
      $interes_mes = $pago_mes-$capital;
      for ($i=count($pagados)+1; $i <= $c->plazo; $i++){
        $acumulado += $pago_mes;

        $dat["numero"] = $i;
        $dat["anio"] = $fecha->format("Y");
        $dat["mes"] = $this->dim2Mes[$fecha->format("M")];
        $dat["mes_en"] = $fecha->format("M");
        $dat["acumulado"] = '$'.number_format($acumulado,2,".",",");
        $dat["pago"] = '$'.number_format($pago_mes,2,".",",");
        $dat["capital"] = '$'.number_format($capital,2,".",",");
        $dat["interes"] = '$'.number_format($interes_mes,2,".",",");
        $dat["tabla_id"] = $c->tablapagos->id;

        \App\pagos::create($dat);
        $fecha->addMonth(1);

      }

      return redirect("/creditos/cartera?cid=".md5($pago->tabla->cartera->id))->with('status','Pago agregado');
    }
    public function reformar(Request $r){
      $c = \App\cartera::whereRAW("md5(id)='".$r->cid."'")->first();
      \App\pagos::where("tabla_id",$c->tablapagos->id)->where("pagado",NULL)->delete();
      $last_p = \App\pagos::where("tabla_id",$c->tablapagos->id)->where("pagado","<>",NULL)->orderBy("id","DESC")->first();
      $pagados = \App\pagos::where("tabla_id",$c->tablapagos->id)->where("pagado","<>",NULL)->where("extra",NULL)->get();
      $ac = str_replace("$","",str_replace(",","",$last_p->acumulado));
      $year = $last_p->anio;
      $acumulado = $ac;
      $mes_pago = \Carbon\carbon::parse($year."-".$last_p->mes_en."-01")->addMonth(1);
      $fecha = $mes_pago;

      $capital = $c->total - $ac;
      $total = $capital;
      $capital = $capital/($c->plazo-count($pagados));
      $pago_mes = (($total * $c->interes / 100) + $total)/($c->plazo-count($pagados));
      $interes_mes = $pago_mes-$capital;
      for ($i=count($pagados)+1; $i <= $c->plazo; $i++){
        $acumulado += $pago_mes;

        $dat["numero"] = $i;
        $dat["anio"] = $fecha->format("Y");
        $dat["mes"] = $this->dim2Mes[$fecha->format("M")];
        $dat["mes_en"] = $fecha->format("M");
        $dat["acumulado"] = '$'.number_format($acumulado,2,".",",");
        $dat["pago"] = '$'.number_format($pago_mes,2,".",",");
        $dat["capital"] = '$'.number_format($capital,2,".",",");
        $dat["interes"] = '$'.number_format($interes_mes,2,".",",");
        $dat["tabla_id"] = $c->tablapagos->id;
        \App\pagos::create($dat);
        $fecha->addMonth(1);
      }
      return redirect("/creditos/cartera?cid=".md5($c->id))->with('status','Pago agregado');

    }
    public function pagar(Request $r){
      $pago = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
      $pago->status = 1;
      $pago->save();
      return redirect("/creditos/cartera?cid=".md5($pago->tabla->cartera->id))->with('status','Pagado');
    }
    public function beca(Request $r){
      $pago = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
      $pago->status = "9";
      $pago->save();
      return redirect("/creditos/cartera?cid=".md5($pago->tabla->cartera->id))->with('status','Becado');
    }
    public function adelantar(Request $r){
      $pago = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
      $pago->pagado = 1;
      $pago->save();
      return redirect("/creditos/cartera?cid=".md5($pago->tabla->cartera->id))->with('status','Activado adelanto');
    }
    public function unpagar(Request $r){
      $pago = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
      $pago->status = 0;
      $pago->save();
      return redirect("/creditos/cartera?cid=".md5($pago->tabla->cartera->id))->with('status','Pagado');
    }
    public function enviartabla(Request $r){
      $tabla = \App\tablapagos::whereRAW("md5(id)='".$r->cid."'")->first();
      $c = $tabla->cliente;
      \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,"Creditos"=>Auth::user()->email])->send(new TablaPagos($tabla));
      return redirect("/creditos/cartera?cid=".md5($tabla->cartera->id))->with('status','Tabla de pagos enviada');
    }
    public function csv(Request $r){
      $delimiter = ",";
      $tabla = \App\tablapagos::whereRAW("md5(id)='".$r->cid."'")->first();
      $filename = $tabla->cliente->usuario->codigo."-".$tabla->cliente->isinscripcion->nombre_completo."_" . date('Y-m-d') . ".csv";

      $f = fopen('php://memory', 'w');

      $fields = array('No. Pago','Año','Mes','Acumulado','Pago','Capital','Interes','Pago');
      fputcsv($f, $fields, $delimiter);
      foreach($tabla->pagos as $p){
          $pa = $p->pagado != NULL ? "Pagado" : "Pendiente de pago";
          fputcsv($f, [$p->numero,$p->anio,$p->mes,$p->acumulado,$p->pago,$p->capital,$p->interes,$pa], $delimiter);
      }
      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');
      fpassthru($f);
    }

    public function api_pagos(Request $r){
      $delimiter = ",";
      $f[0] = \Carbon\carbon::parse("01-".date("M")."-2021")->format("m");
      $f[1] = date("Y");
      $total = 0;
      $a = \Carbon\carbon::parse("01-".date("M")."-2021")->subMonths(1)->format("m");
      $pagos = \App\pagos::whereHas("documento",function($q) use($f,$a){
        $q->whereRAW("MONTH(created_at) = '".$f[0]."' or MONTH(created_at) = '$a'");
      })->where("anio",$f[1])->where("pagado","<>",NULL);
      $i = 0;
      $sort = $pagos->get()->sortBy('documento.created_at',SORT_REGULAR,false)->reverse();
      foreach($sort as $p){
          if($p->split != NULL)
            break;
          if(!strstr($p->numero,"E-"))
            if($p->numero < 20)
              $i++;
          $ac = str_replace("$","",str_replace(",","",$p->pago));
          $total += $ac;
      }
      $aprox = ($total-($i*700))*.45;
      header("Content-type: json/application");
      echo '{"name": "UniSant","data": [{"value": "'.$aprox.'","value_classification": "Créditos","timestamp": "1632960000","time_until_update": "74159"}],"metadata": {"error": null}}';
    }

    public function pagoscsv(Request $r){
      $delimiter = ",";
      $f[0] = \Carbon\carbon::parse("01-".$r->mes."-2021")->format("m");
      $f[1] = $r->anio;
      $cobrar = isset($r->cobrar) ? "YES" : NULL;
      if($r->and != NULL){
        $a = $r->and;
        $pagos = \App\pagos::whereHas("documento",function($q) use($f,$a){
          $q->whereRAW("MONTH(created_at) = '".$f[0]."' or MONTH(created_at) = '$a'");
        })->where("anio",$f[1])->where("pagado","<>",NULL);
      } else {
        $pagos = \App\pagos::whereHas("documento",function($q) use($f){
          $q->whereRAW("MONTH(created_at) = '".$f[0]."'");
        })->where("anio",$f[1])->where("pagado","<>",NULL);
      }

      $filename = "PAGOSRECIBIDOS_" . date('Y-m-d') . ".csv";

      $f = fopen('php://memory', 'w');

      $fields = array('#','No. Pago','Concepto','Nombre','Pago recibido','Comprobante','Fecha registro de pago','Mes de pago');
      fputcsv($f, $fields, $delimiter);
      $i = 1;
      $sort = $pagos->get()->sortBy('documento.created_at',SORT_REGULAR,false);
      if($cobrar != NULL){
        $sort = $pagos->get()->sortBy('documento.created_at',SORT_REGULAR,false)->reverse();
      }
      foreach($sort as $p){
          if($cobrar != NULL && $p->split != NULL){
            break;
          }
          $link = "https://sii.unisantorizaba.com/documentos/watchar/".md5($p->pagado);
          $mespago = $p->mes."/".$p->anio;
          $concepto = $p->tabla->cartera->concepto;
          if(strstr($p->numero,"E-"))
            $concepto = "Pago extra";
          fputcsv($f, [$i++,$p->numero,$concepto,$p->tabla->cliente->isinscripcion->nombre_completo,$p->pago,$link,$p->updated_at,$mespago], $delimiter);
      }
      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');
      fpassthru($f);
    }
    public function comprobante(Request $r){
        ini_set('upload_max_filesize', '2G');
        ini_set('post_max_size', '4G');
        ini_set('max_execution_time', '5000000');
        ini_set('max_input_time', '5000000');
        ini_set('memory_limit', '200M');
        $pago = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
        if($r->hasFile('file')){
          $file = $r->file('file');
          $data = explode('.',$file->getClientOriginalName());
          $name = "";
          for($k = 0; $k < count($data)-1;$k++)
          {
           $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
          }
          $ext = $data[count($data)-1];
          $document = documento::create(['size'=>$file->getSize(),'ext'=>$ext,'titulo'=>$name,'empleado_id'=>$r->id]);
          $file->move(storage_path(),md5($document->id).'.file');
          historial::create(["usuario_id"=>Auth::user()->id,'accion'=>"Ha subido el archivo $name de multiples"]);
          $pago->pagado = $document->id;
          $pago->save();
        }
        return redirect("/creditos/cartera?cid=".md5($pago->tabla->cartera_id))->with('status','Comprobante subido');
    }
    public function actualizar(Request $r){
      $c = \App\cartera::whereRAW("md5(id)='".$r->cid."'")->first();
      $plazo = $r->plazo;
      $interes = $r->interes;
      $valor_materia = $r->valor_materia;
      $valor_titulo = $r->valor_titulo;
      $concepto = isset($r->concepto) ? $r->concepto : $c->concepto;
      $anio = \Carbon\carbon::parse($c->created_at)->format("Y");
      $fecha_estudio = \Carbon\carbon::parse($this->dim2Month[$r->fecha_estudio]." ".$anio);
      $fecha_inicio = \Carbon\carbon::parse($this->dim2Month[$r->fecha_inicio]." ".$anio);

      $val2 = ($valor_titulo == null) ? 18800 : $valor_titulo;
      $val = ($valor_materia == null) ? 1520 : $valor_materia;
      $total = ($val * 40) + $val2;

      $datos = ["concepto"=>$concepto,"fecha_estudio"=>$fecha_estudio,"fecha_inicio"=>$fecha_inicio,"total"=>$total,"plazo"=>$plazo,"valor_materia"=>$valor_materia,"valor_titulo"=>$valor_titulo,"interes"=>$interes];
      $c->fill($datos)->save();

      $tabla = \App\tablapagos::create(["cliente_id"=>$c->cliente->id,"cartera_id"=>$c->id]);

      $interes2 = ($total * $interes/100 * $plazo/12);
      $interes_mes = $interes2/$plazo;
      $capital = ($total)/$plazo;
      $pago_mes = $capital+$interes_mes;
      $pago_mes = round($pago_mes,2);

      $mes_pago = \Carbon\carbon::parse($c->fecha_inicio)->addMonth(0);

      $fecha = $mes_pago;
      $acumulado = 0;

      for ($i=1; $i <= $plazo; $i++){
        $acumulado += $pago_mes;

        $dat["numero"] = $i;
        $dat["anio"] = $fecha->format("Y");
        $dat["mes"] = $this->dim2Mes[$fecha->format("M")];
        $dat["mes_en"] = $fecha->format("M");
        $dat["acumulado"] = '$'.number_format($acumulado,2,".",",");
        $dat["pago"] = '$'.number_format($pago_mes,2,".",",");
        $dat["capital"] = '$'.number_format($capital,2,".",",");
        $dat["interes"] = '$'.number_format($interes_mes,2,".",",");
        $dat["tabla_id"] = $tabla->id;

        \App\pagos::create($dat);
        $fecha->addMonth(1);

      }
      return redirect("/creditos/cartera?cid=".$r->cid)->with('status','Se ha generado la tabla de pagos');

    }
    public function like(Request $r){
        $n = \App\notas_cliente::whereRAW("md5(id)='".$r->cid."'")->first();
        $c = \App\notas_likes::create(["usuario_id"=>Auth::user()->id,"nota_id"=>$n->id]);
        return redirect('/creditos/cartera?cid='.md5($r->carteraid))->with("status","Has reaccionado");
    }
    public function unlike(Request $r){
        $n = \App\notas_cliente::whereRAW("md5(id)='".$r->cid."'")->first();
        $c = \App\notas_likes::where("nota_id",$n->id)->where("usuario_id",Auth::user()->id)->delete();
        return redirect('/creditos/cartera?cid='.md5($r->carteraid))->with("status","Ya no estas reaccionando");
    }
    public function nota(Request $f){
      $n = \App\notas_cliente::create(
        [
          "usuario_id" => Auth::user()->id,
          "cliente_id" => $f->cliente_id,
          "nota" => $f->comentario
        ]
      );
      return redirect('/creditos/cartera?cid='.md5($f->cartera_id))->with("status","Nota");
    }
    public function aceptarfirma(Request $r){
      $c = \App\cartera::whereRAW("md5(id)='".$r->cid."'")->first();
      $c->hasFirma->status = 1;
      $c->hasFirma->save();
      return redirect("/")->with("status","Video firma aceptada");
    }
    public function rechazarfirma(Request $r){
      $c = \App\cartera::whereRAW("md5(id)='".$r->cid."'")->first();
      \Mail::to([$c->cliente->isinscripcion->nombre_completo=>$c->cliente->correo,"Creditos"=>Auth::user()->email])->send(new VideoFirma($c));
      unlink(storage_path()."/signs/".$c->hasFirma->video_id.".file");
      $c->hasFirma->delete();
      return redirect("/")->with("status","Video firma rechazada");
    }
}
