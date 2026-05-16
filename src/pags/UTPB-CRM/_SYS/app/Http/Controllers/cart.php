<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use Srmklive\PayPal\Services\ExpressCheckout;
use Srmklive\PayPal\Services\AdaptivePayments;

class cart extends Controller
{
  public function add(Request $r){

    $cart = (Session::get("cart") !== null) ? Session::get("cart") : [];
    $cart[$r->cid] = ["cantidad"=>$r->cantidad];
    Session::put("cart",$cart);
    return redirect("/shirushi/menu");
  }
  public function addsaldo(Request $r){

    $usuario = \App\User::where("codigo",$r->codigo)->first();
    if($usuario != null && !empty($r->codigo)){
      $usuario->cash = $usuario->cash + $r->consumo * .05;
      $usuario->save();
      \App\orden::create(["status"=>3,"metodo"=>0,"sucursal_id"=>\Auth::user()->sucursal,"total"=>"$r->consumo","usuario_id"=>$usuario->id,"orden" => "","direccion_id"=>0]);

      \App\historial::create(["usuario_id"=>\Auth::user()->id,'accion'=>"Ha agreado saldo de una compra al cliente $r->codigo"]);
      return redirect("/sucursales/saldo")->with("status","Se agrego $".($r->consumo * .05)." MXN al monedero.");
    }
    return redirect("/sucursales/saldo")->with("status","No se encontro el codigo $r->codigo");
  }
  public function modify(Request $r){

    $cart = (Session::get("cart") !== null) ? Session::get("cart") : [];
    $cart[$r->cid] = ["cantidad"=>$r->cantidad];
    Session::put("cart",$cart);
    return redirect("/shirushi/cart");
  }
  public function del(Request $r){

    $cart = (Session::get("cart") !== null) ? Session::get("cart") : [];
    unset($cart[$r->cid]);
    Session::put("cart",$cart);
    return redirect("/shirushi/cart");
  }
  public function actualizarestado(Request $r){
    $cid = $r->cid;
    $pedido = \App\orden::whereRAW("md5(id)='$cid'")->first();
    $pedido->status = $r->status;
    switch($pedido->status){
      case 0:
        $pedido->historial = $pedido->historial.date("Y-m-d H:i:s")."|Tu pedido ha llegado a la sucursal, pronto será leído. <br>";
      break;
      case 1:
        $pedido->historial = $pedido->historial.date("Y-m-d H:i:s")."|Se esta preparando tu pedido, ¡Delicioso!. <br>";
      break;
      case 2:
        $pedido->historial = $pedido->historial.date("Y-m-d H:i:s")."|Tu pedido esta en camino a ti. <br>";
      break;
      case 3:
        $pedido->historial = $pedido->historial.date("Y-m-d H:i:s")."|Tu pedido ha sido entregado, gracias por tu preferencia. <br>";
        $pedido->usuario->cash = $pedido->usuario->cash + $pedido->total * .05;
        $pedido->usuario->save();
      break;
      case 4:
        $pedido->historial = $pedido->historial.date("Y-m-d H:i:s")."|Tu pedido ha sido cancelado, ponte en contacto con la sucursal para más información Tel:".$pedido->sucursal->telefono.". <br>";
      break;
    }
    $pedido->save();
    return redirect("/sucursales/leer?cid=$cid");
  }
  public function payment(Request $r){
    $data = [];
    $data['items'] = [];
    $t = 0.0;
    foreach (Session::get("cart") as $id => $cantidad) {
      $p = \App\platillo::find($id);
      $e = [
        'name' => $p->nombre,
        'price' => $p->precio,
        'desc'  => $p->descripcion,
        'qty' => $cantidad["cantidad"]
      ];
      array_push($data["items"],$e);
      $t += $cantidad["cantidad"] * $p->precio;
    }
    if(isset($monedero)){
      $e = [
        'name' => "Descuento",
        'price' => "-".\Auth::user()->cash,
        'desc'  => "Monedero",
        'qty' => 1
      ];
      array_push($data["items"],$e);
      $t = $t - \Auth::user()->cash;
    }

    $suc = \App\sucursal::find($r->sucid)->franquiciatarios[0];

    if(count($suc->usuario->paypals) > 0 && $r->formapago == "paypal"){
      $orden = \App\orden::create(["status"=>5,"metodo"=>"0","sucursal_id"=>$r->sucid,"total"=>"$t","usuario_id"=>Auth::user()->id,"orden" => json_encode(Session::get("cart")),"direccion_id"=>$r->direccion_id]);

      $data['invoice_id'] = "Shirushi_".$orden->id;
      $data['invoice_description'] = "Pedido #S{$orden->id}";
      $data['return_url'] = "https://".$_SERVER['HTTP_HOST']."/cart/success/".md5("shirushimx2019".$orden->id);
      $data['cancel_url'] = "https://".$_SERVER['HTTP_HOST']."/#pedidos";
      $data['total'] = $t;
      //dd($data);
      Session::put("data1",$data);

      $provider = new ExpressCheckout;

      if($suc->usuario->defecto == 0){
        $p = $suc->usuario->paypals[0];
      } else {
        $p = $suc->usuario->paypal;
      }

      $conf = \Config::get("paypal");

      $conf['live']['username'] = $p->username;
      $conf['live']['password'] = $p->password;
      $conf['live']['secret'] = $p->secret;




      // Through facade. No need to import namespaces
      $provider = \PayPal::setProvider('express_checkout');      // To use express checkout(used by default).
      $provider->setApiCredentials($conf);
      $o = [
          'BRANDNAME' => $suc->nombre,
          'LOGOIMG' => 'https://gruposhirushi.com/images/logo.png',
          'CHANNELTYPE' => 'Merchant'
      ];
      Session::put("o",$o);
      $response = $provider->addOptions($o)->setCurrency('MXN')->setExpressCheckout($data,true);
      //dd($response);
      // Use the following line when creating recurring payment profiles (subscriptions)
       // This will redirect user to PayPal

      //Session::put("cart",[]);

      return redirect($response['paypal_link']);
    } else {
      if($r->formapago == "efectivo"){
        $orden = \App\orden::create(["status"=>0,"metodo"=>1,"sucursal_id"=>$r->sucid,"total"=>"$t","usuario_id"=>Auth::user()->id,"orden" => json_encode(Session::get("cart")),"direccion_id"=>$r->direccion_id]);
        $orden->created_at = date("Y-m-d H:i:s");
        $orden->historial = $orden->historial.date("Y-m-d H:i:s")."|Tu pedido ha llegado a la sucursal, pronto será leído.<br>";
        $orden->metodo = 1;
        $orden->save();
        Session::put("cart",[]);
        if(isset($r->monedero)){
          \Auth::user()->cash = 0;
          \Auth::user()->save();
        }
        return redirect("/shirushi/tracker?cid=".md5($orden->id))->with("success","Orden recibida, Monitorea tu orden desde Shirushi Tracker.");
      }
      return redirect("/shirushi/cart")->with("error","Esta sucursal no tiene pago en linea");
    }

  }

  public function success(Request $request,$cid)
  {
      $provider = new ExpressCheckout;      // To use express checkout.
      // Through facade. No need to import namespaces
      $provider = \PayPal::setProvider('express_checkout');
      $o = Session::get("o");
      $provider->addOptions($o)->setCurrency('MXN');
      $response = $provider->getExpressCheckoutDetails($request->token);
      //dd($response);
      $orden = \App\orden::whereRAW("md5(concat('shirushimx2019',id))='".$cid."'")->first();
      $data = Session::get("data1");
      $payment_status = $provider->doExpressCheckoutPayment($data, $request->token, $request->PayerID);
      //dd($payment_status);
      $status = $payment_status['PAYMENTINFO_0_PAYMENTSTATUS'];
      $response = $provider->getTransactionDetails($request->token);
      //dd($response);
      if (!strcasecmp($status, 'Completed') || !strcasecmp($status, 'Processed') || !strcasecmp($status, 'Pending')) {
        $orden->status = 0;
        $orden->save();
        Session::put("cart",[]);
        if(isset($r->monedero)){
          \Auth::user()->cash = 0;
          \Auth::user()->save(["cash"=>0]);
        }
        return redirect("/#ordenes")->with("error","Pago $orden->id concretado con exito,");
      } else {
        return redirect("/#ordenes")->with("error","No se ha podido realizar el pago");
      }


  }

}
