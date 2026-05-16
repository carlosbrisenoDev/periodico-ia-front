<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use Srmklive\PayPal\Services\ExpressCheckout;
use Srmklive\PayPal\Services\AdaptivePayments;

class dir extends Controller
{
  public function add(Request $r){
    $all = $r->all();
    $all["usuario_id"] = Auth::user()->id;
    \App\direccion::create($all);
    return redirect("/");
  }
  public function modify(Request $r){

    $cart = (Session::get("cart") !== null) ? Session::get("cart") : [];
    $cart[$r->cid] = ["cantidad"=>$r->cantidad];
    Session::put("cart",$cart);
    return redirect("/w/cart");
  }
  public function del(Request $r){
    \App\direccion::find($r->cid)->delete();
    return redirect("/");
  }
}
