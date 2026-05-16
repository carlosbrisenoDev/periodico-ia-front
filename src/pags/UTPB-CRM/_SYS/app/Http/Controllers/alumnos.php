<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
class alumnos extends Controller
{
  public function inscribirse(Request $r){
    $all = $r->all();
    $c = \App\cliente::where("usuario_id",Auth::user()->id)->first();
    $all["cliente_id"] = $c->id;
    $i = \App\inscripciones::create($all);

    return redirect("/alumnos/formulario")->with('status','La solicitud ha sido enviada');
  }
  public function listacorreos(Request $r){
    $alumnos = \App\cliente::where("status",">",3)->has('isinscripcion')->get();
    $correos = "";
    $delimiter = ",";
    $filename = "contactos_" . date('Y-m-d') . ".csv";

    $f = fopen('php://memory', 'w');
    $fields = array('Name', 'Email');
    fputcsv($f, $fields, $delimiter);

    foreach ($alumnos as $a) {
      $lineData = array($a->isinscripcion->nombre_completo,$a->isinscripcion->correo);
      fputcsv($f, $lineData, $delimiter);
    }

    fseek($f, 0);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    fpassthru($f);
  }
  public function borrarasginatura(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='".$r->cid."'")->first();
    \App\materias::whereRAW("md5(id)='".$r->mid."'")->first()->delete();
    return redirect("/ventas/cliente?cid=".$r->cid)->with('status','Materia eliminada');
  }
  public function inscritoscsv(Request $r){
      $delimiter = ",";
      $tabla = \App\cliente::where("status",4)->get();
      $filename = "inscritos_" . date('Y-m-d') . ".csv";

      $f = fopen('php://memory', 'w');

      $fields = array('No.','Formulario de inscripción','Matricula','Nombre','Télefono','Baja','Correo','Crédito','Materias cursadas','Aprobadas','Reprobadas');
      fputcsv($f, $fields, $delimiter);
      $i = 1;
      foreach($tabla as $cr){
        $p = $cr->isinscripcion;
        $nombre = $p == NULL ? $cr->nombre : $p->nombre_completo;
        $correo = $p == NULL ? $cr->correo : $p->correo;
        $tel = $p == NULL ? $cr->tel : $p->tel;

        if (!strstr($cr->nombre,"PRUEBA")) {
          $baja = $cr->baja != NULL ? "Si" : "No";
          $credito = $cr->credito_info != null ? (empty($cr->credito_info->status) ? "Sin enviar" : $cr->credito_info->status) : "Sin crédito";
          $totalm = count($cr->materias);
          $totala = 0;
          $totalr = 0;
          $form = $p==NULL ? "No" : "Si";
          foreach ($cr->materias as $m) {
            if($m->situacion != "Aprobado"){
              $totalr++;
            } else {
              $totala++;
            }
          }
          fputcsv($f, [$i++,$form,$cr->matricula,$nombre,$tel,$baja,$correo,$credito,$totalm,$totala,$totalr]);
        }
      }
      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');
      fpassthru($f);
  }
  public function signvideo(Request $r){
    $cartera = \App\cartera::whereRAW("md5(id)='".$r->cid."'")->first();
    $cliente = $cartera->cliente;
    $data = substr($_POST['data'], strpos($_POST['data'], ",") + 1);
    $decodedData = base64_decode($data);
    $filename = $_POST['fname'].".file";
    $fp = fopen(storage_path()."/signs/".$filename, 'wb');
    fwrite($fp, $decodedData);
    fclose($fp);
    \App\firma::create(["video_id"=>$_POST['fname'] = $_POST['fname'],"cliente_id"=>$cliente->id,"cartera_id"=>$cartera->id]);
  }
  public function video(Request $r,$cid)
  {
    try {
      return \Response::file(storage_path()."/signs/".$cid.'.file');
    } catch (\Exception $e) {
      return view("errors.404");
    }

  }
}
