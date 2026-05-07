<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use \App\Mail\ReciboPago;
use \App\Mail\ReciboAtrasado;

class pagos extends Controller
{
  public function recibo(Request $r){
    $pago = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
    $c = $pago->tabla->cliente;
    $pago->recibo = 1;
    $pago->save();
    \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,"Creditos"=>Auth::user()->email])->send(new ReciboPago($pago));
    return redirect()->back()->with('status','Recibo de pago enviado');
  }
  public function notify(Request $r){
    $pago = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
    $c = $pago->tabla->cliente;
    $pago->notify = 1;
    $pago->save();
    \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,"Creditos"=>Auth::user()->email])->send(new ReciboAtrasado($pago));
    return redirect("/creditos/notify")->with('status','Notificación enviada');
  }
  public function notifysms(Request $r){
    $pago = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
    $c = $pago->tabla->cliente;

    require ('altaria/httpPHPAltiria.php');

    $altiriaSMS = new \AltiriaSMS();

    $altiriaSMS->setLogin('jesusdavidvaldivia@gmail.com');
    $altiriaSMS->setPassword('nb3tv5sf');

    $altiriaSMS->setDebug(false);
    $sDestination = '52'.$c->telefono;

    $mensaje = "Universidad Santander; recuerda estar al corriente con tus pagos para que la plataforma te permita continuar sin problemas con tus clases.";
    $response = $altiriaSMS->sendSMS($sDestination, $mensaje);

    $pago->notifysms = 1;
    $pago->save();

    return redirect("/creditos/notify")->with('status','Notificación SMS Enviada');
  }
  public function split(Request $r){
    $pago = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
    $pago->split = \Carbon\Carbon::now();
    $pago->save();
    $and = $r->and != NULL ? "&and=$andmonth" : "";
    return redirect("/creditos/pagos?mes=".$r->mes."&anio=".$r->anio.$and)->with('status','Split removido');
  }
  public function unsplit(Request $r){
    $pago = \App\pagos::whereRAW("md5(id)='".$r->cid."'")->first();
    $pago->split = NULL;
    $pago->save();
    $and = $r->and != NULL ? "&and=$andmonth" : "";
    return redirect("/creditos/pagos?mes=".$r->mes."&anio=".$r->anio.$and)->with('status','Split removido');
  }
  public function notifyssms(Request $r){
    $pagos = \App\pagos::where("pagado",NULL)->orderBy("created_at","desc")->get();
    $i = 0;
    require ('altaria/httpPHPAltiria.php');
    $altiriaSMS = new \AltiriaSMS();

    foreach ($pagos as $pago) {
      $i++;
      if ((\Carbon\carbon::parse($pago->anio."-".$pago->mes_en."-1")->subDays(2)->isPast() && $pago->tabla->cliente->baja == NULL) && $pago->status != 9){
        $c = $pago->tabla->cliente;

        $altiriaSMS->setLogin('jesusdavidvaldivia@gmail.com');
        $altiriaSMS->setPassword('nb3tv5sf');

        $altiriaSMS->setDebug(false);
        if(!empty($c->telefono))
        {
          $sDestination = '52'.$c->telefono;

          $mensaje = "Universidad Santander; recuerda estar al corriente con tus pagos para que la plataforma te permita continuar sin problemas con tus clases.";
          $response = $altiriaSMS->sendSMS($sDestination, $mensaje);

          $pago->notifysms = 1;
          $pago->save();
        }
      }
    }
    return redirect("/creditos/notify")->with('status','Notificación SMS enviada '.$i);
  }
  public function notifys(Request $r){
    $pagos = \App\pagos::where("pagado",NULL)->where("status","<>",9)->orderBy("created_at","desc")->get();
    foreach ($pagos as $pago) {
      if (\Carbon\carbon::parse($pago->anio."-".$pago->mes_en."-1")->subDays(2)->isPast() && $pago->notify==0 && $pago->tabla->cliente->baja == NULL){
        $c = $pago->tabla->cliente;
        \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,"Creditos"=>Auth::user()->email])->send(new ReciboAtrasado($pago));
        sleep(100);
        $pago->notify = 1;
        $pago->save();
      }
    }
    return redirect("/creditos/notify")->with('status','Notificación enviada');
  }
  public function renotifys(Request $r){
    $pagos = \App\pagos::where("pagado",NULL)->orderBy("created_at","desc")->get();
    foreach ($pagos as $pago) {
      if (\Carbon\carbon::parse($pago->anio."-".$pago->mes_en."-1")->isPast() && $pago->pagado == 0){
        $pago->notify = 0;
        $pago->save();
      }
    }
    return redirect("/creditos/notify")->with('status',"Notificaciones reiniciadas");
  }
  public function renotifyssms(Request $r){
    $pagos = \App\pagos::where("pagado",NULL)->orderBy("created_at","desc")->get();
    foreach ($pagos as $pago) {
      if (\Carbon\carbon::parse($pago->anio."-".$pago->mes_en."-1")->isPast() && $pago->pagado == 0){
        $pago->notifysms = 0;
        $pago->save();
      }
    }
    return redirect("/creditos/notify")->with('status',"Notificaciones reiniciadas");
  }
  public function recibos(Request $r){
    $mes = $r->mes;
    $anio = $r->anio;
    $pagos = \App\pagos::whereHas("documento",function($q) use($mes){
      $q->whereRAW("MONTH(created_at) = '$mes'");
    })->where("anio",$anio)->where("pagado","<>",NULL);


    foreach ($pagos->get() as $pago) {
      if ($pago->recibo == NULL) {
        $c = $pago->tabla->cliente;
        \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,"Creditos"=>Auth::user()->email])->send(new ReciboPago($pago));
        $pago->recibo = 1;
        $pago->save();
      }
    }
    $and = $r->and != NULL ? "&and=$andmonth" : "";
    return redirect("/creditos/pagos?mes=".$mes."&anio=".$anio.$and)->with('status','Recibos enviados');
  }
}
