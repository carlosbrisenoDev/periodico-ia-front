<?php

namespace App\Http\Controllers;

include_once 'newXMLAPI/newXMLAPI.php';
include 'MailLibrary/loader.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use greeny\MailLibrary\Drivers\ImapDriver;
use greeny\MailLibrary\Connection;
use Illuminate\Http\Request;
use Auth;
use newXMLAPI\newXMLAPI as api;
use greeny\MailLibrary\Mail;

class bandeja extends Controller
{
  public static function getCon(){
      $api = new api();
      $username = explode("@",Auth::user()->email);
      $api->setEmailAccount("unisantorizaba.com",$username[0],Auth::user()->codigo);
      $driver = new ImapDriver(Auth::user()->email,Auth::user()->codigo2, 'mail.unisantorizaba.com', 993, TRUE);
      $connection = new Connection($driver);
      return $connection;
  }
  public static function getMailCount(){
    $api = new api();
    $username = explode("@",Auth::user()->email);
    $api->setEmailAccount("unisantorizaba.com",$username[0],Auth::user()->codigo);
    $driver = new ImapDriver(Auth::user()->email,Auth::user()->codigo2, 'mail.unisantorizaba.com', 993, TRUE);
    $connection = new Connection($driver);

    echo $connection->getMailBox("INBOX")->getMails()->where("SEEN",FALSE)->countMails();
  }
  public function correo(Request $r){
    $api = new api();
    $username = explode("@",Auth::user()->email);

    $api->setEmailAccount("unisantorizaba.com",$username[0],Auth::user()->codigo);
    //$api->changePassword(Auth::user()->codigo2);

    $driver = new ImapDriver(Auth::user()->email,Auth::user()->codigo2, 'mail.unisantorizaba.com', 993, TRUE);
    $connection = new Connection($driver);

    //dd($connection);


    return view('users.'.Auth::user()->level->alias.".bandeja",["con"=>$connection,"api"=>$api,"seen"=>Mail::SEEN]);
  }
  public function nuevo(Request $r,$correo){
    $driver = new ImapDriver(Auth::user()->email,Auth::user()->codigo2, 'mail.unisantorizaba.com', 993, TRUE);
    $connection = new Connection($driver);

    return view('users.'.Auth::user()->level->alias.".bandeja.nuevo",["con"=>$connection]);
  }
  public function iosandroid(Request $r,$correo){
    return view('users.'.Auth::user()->level->alias.".bandeja.iosandroid");
  }
  public function enviar(Request $r){

    $mail = new PHPMailer(true);

    try {
      //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'mail.unisantorizaba.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'unisanto';                     // SMTP username
        $mail->Password   = '@Sve14111elw#3210';                                // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port       = 25;
        $mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);                                    // TCP port to connect to

        //Recipients
        $mail->setFrom(Auth::user()->email, htmlentities(Auth::user()->name));
        foreach (explode(",",$r->to) as $key => $value) {
          $mail->addAddress($value);
        }

        $c = explode(",",$r->cc);
        if (count($c) > 0)  {
          foreach ($c as $key => $value) {
            if(!empty($value)){
              $mail->addCC($value);
            }
          }
        }

        $c = explode(",",$r->cco);
        if (count($c) > 0)  {
          foreach ($c as $key => $value) {
            if(!empty($value)){
              $mail->addBCC($value);
            }
          }
        }

        $c = explode(",",$r->archivos);
        if (count($c) > 0)  {
          foreach ($c as $key => $value) {
            if($value != "")
              $mail->addAttachment(storage_path()."/perfiles/".Auth::user()->id."/".$value);
          }
        }

        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $r->asunto;
        $mail->Body    = $r->body.Auth::user()->sign;

        if($mail->send()){
            $driver = new ImapDriver(Auth::user()->email,Auth::user()->codigo2, 'mail.unisantorizaba.com', 993, TRUE);
            $connection = new Connection($driver);

            $connection->appendMail($mail->getString(),"INBOX.Sent");
        }
        echo 'Mensaje enviado';
        return redirect("bandeja/correo/listar")->with(["status"=>"Correo enviado"]);
      } catch (Exception $e) {
        return redirect("bandeja/correo/listar")->with(["status"=>"{$mail->ErrorInfo}"]);
      }
  }
  public function enviarnotify(Request $r){

    $mail = new PHPMailer(true);

    try {
      //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'mail.unisantorizaba.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'unisanto';                     // SMTP username
        $mail->Password   = '@Sve14111elw#3210';                               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port       = 25;
        $mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);                                    // TCP port to connect to

        //Recipients
        $mail->AddReplyTo(Auth::user()->email, htmlentities(Auth::user()->name));
        $mail->setFrom(Auth::user()->email, htmlentities(Auth::user()->name));
        foreach (explode(",",$r->to) as $key => $value) {
          $mail->addAddress($value);
        }

        $c = explode(",",$r->cc);
        if (count($c) > 0)  {
          foreach ($c as $key => $value) {
            if(!empty($value)){
              $mail->addCC($value);
            }
          }
        }

        $c = explode(",",$r->cco);
        if (count($c) > 0)  {
          foreach ($c as $key => $value) {
            if(!empty($value)){
              $mail->addBCC($value);
            }
          }
        }

        $c = explode(",",$r->archivos);
        if (count($c) > 0)  {
          foreach ($c as $key => $value) {
            if($value != "")
              $mail->addAttachment(storage_path()."/perfiles/".Auth::user()->id."/".$value);
          }
        }

        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $r->asunto;
        $mail->Body    = $r->body.Auth::user()->sign;

        if($mail->send()){
            $driver = new ImapDriver(Auth::user()->email,Auth::user()->codigo2, 'mail.unisantorizaba.com', 993, TRUE);
            $connection = new Connection($driver);

            $connection->appendMail($mail->getString(),"INBOX.Sent");
        }
        return 1;
      } catch (Exception $e) {
        return $mail->ErrorInfo;
      }
  }
  public function upload(Request $request){
    ini_set('upload_max_filesize', '2G');
    ini_set('post_max_size', '4G');
    ini_set('max_execution_time', '5000000');
    ini_set('max_input_time', '5000000');
    ini_set('memory_limit', '200M');
    if($request->hasFile('file')){
        $file = $request->file('file');
        $data = explode('.',$file->getClientOriginalName());
        $name = "";
        for($k = 0; $k < count($data)-1;$k++)
        {
         $name .= $data[$k].(($k==(count($data)-2)) ? "" : ".");
        }
        $ext = $data[count($data)-1];
        $file->move(storage_path()."/perfiles/".Auth::user()->id."/",$name.'.'.$ext);
        echo $name.'.'.$ext;
     }

}
  public function attach(Request $r, $cid){
    $driver = new ImapDriver(Auth::user()->email,Auth::user()->codigo2, 'mail.unisantorizaba.com', 993, TRUE);
    $con= new Connection($driver);

    $box1 = (isset($_REQUEST["box"])) ? $_REQUEST["box"] : "INBOX";
    $box = (!strstr("INBOX",$box1)) ? "INBOX.$box1" : $box1;
    $mailbox = $con->getMailbox($box);
    $selection = $mailbox->getMails();
    $mail = $selection[$_REQUEST["mail"]];

    foreach ($mail->getAttachments() as $attach) {
      if($attach->getName() == urldecode($r->get("attach"))){
        header("Content-disposition: attachment; filename=".urldecode($attach->getName()));
        header("Content-type: application/octet-stream");
        echo $attach->getContent();
      }
    }
  }

  public function accion(Request $r){
    if($r->has("mail"))
    {
      $driver = new ImapDriver(Auth::user()->email,Auth::user()->codigo2, 'mail.unisantorizaba.com', 993, TRUE);
      $con= new Connection($driver);

      $box1 = (isset($_REQUEST["box"])) ? $_REQUEST["box"] : "INBOX";
      $box = (!strstr($box1,"INBOX")) ? "INBOX.$box1" : $box1;

      $mailbox = $con->getMailbox($box);
      $selection = $mailbox->getMails();

      foreach ($r->mail as $key => $val) {
        $mail = $selection[$val];
        if($r->accion == 1){
          $mail->setFlags(array(Mail::FLAG_SEEN => TRUE, Mail::FLAG_DRAFT => FALSE));
        } else if($r->accion == 2){
          $mail->setFlags(array(Mail::FLAG_SEEN => FALSE, Mail::FLAG_DRAFT => FALSE));
        } else if($r->accion == 3){
          $mail->delete();
        }
        $con->flush();
      }
      return redirect("bandeja/correo/listar?box=$box")->with(["status"=>"Acción realizada"]);
    } else {
      return redirect("bandeja/correo/listar?box=".$r->box)->with(["status"=>"No hay nada seleccionado"]);
    }
  }

  public function mover(Request $r){
    if($r->has("mail")){
      $driver = new ImapDriver(Auth::user()->email,Auth::user()->codigo2, 'mail.unisantorizaba.com', 993, TRUE);
      $con= new Connection($driver);

      $box1 = (isset($_REQUEST["box"])) ? $_REQUEST["box"] : "INBOX";
      $box = (strstr($box1,"INBOX") == false) ? "INBOX.$box1" : $box1;

      $mover = (isset($_REQUEST["mover"])) ? $_REQUEST["mover"] : "INBOX";
      $mover = (strstr($mover,"INBOX") == false) ? "INBOX.$mover" : $mover;

      $mailbox = $con->getMailbox($box);
      $selection = $mailbox->getMails();

      foreach ($r->mail as $key => $val) {
        $mail = $selection[$val];
        $mail->move($mover);
        $con->flush();
      }

      return redirect("bandeja/correo/listar?box=$mover")->with(["status"=>"Acción realizada"]);
    } else {
      return redirect("bandeja/correo/listar?box=".$r->mover)->with(["status"=>"No hay nada seleccionado"]);
    }
  }

}
