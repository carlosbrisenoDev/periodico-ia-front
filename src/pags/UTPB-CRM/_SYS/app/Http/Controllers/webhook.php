<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class webhook extends Controller
{
  public function test(){
    dd(\App\datos::find(1)->valor);
  }
  public function facebook(Request $r){
    if (isset($_GET['hub_verify_token'])) {
        if ($_GET['hub_verify_token'] == 'sg3210la') {
            echo $_GET['hub_challenge'];
            return;
        } else {
            echo 'Invalid Verify Token';
            return;
        }
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['entry'][0]['messaging'][0]['sender']['id'])) {
    $sender = $input['entry'][0]['messaging'][0]['sender']['id'];
    $message = $input['entry'][0]['messaging'][0]['message']['text'];
    \App\leads::create(["recipient"=>$sender,"mensaje"=>$message]);
    }
  }

  public function sender(Request $r){
    $token = \App\datos::find(1)->valor;
    $url = 'https://graph.facebook.com/v2.6/me/messages?access_token='.$token;
    $ch = curl_init($url);
    $jsonData = '{
    "recipient":{
        "id":"' . $r->sender . '"
        },
        "message":{
            "text":"' . $r->message . '"
        }
    }';
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    if (!empty($r->message)) {
        curl_exec($ch);
        \App\leads::create(["recipient"=>$r->sender,"mensaje"=>$r->message,"usuario_id"=>\Auth::user()->id]);

    }
    return json_encode(["recipient_id"=>$r->sender,"date"=>\carbon\carbon::now()->diffForHumans()]);
  }
}
