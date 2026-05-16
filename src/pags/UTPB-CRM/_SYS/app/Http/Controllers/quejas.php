<?php

namespace App\Http\Controllers;

include_once 'newXMLAPI/newXMLAPI.php';
include 'MailLibrary/loader.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use greeny\MailLibrary\Drivers\ImapDriver;
use greeny\MailLibrary\Connection;

use Auth;
use Storage;
use Response;
class quejas extends Controller
{

  public function aplicar(Request $r)
  {
    $mail = new PHPMailer(true);


        $mail->isSMTP();
        $mail->Host       = 'mail.gruposhirushi.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = "quejasysugerencias@gruposhirushi.com";
        $mail->Password   = "4YRX9rRC";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 25;
        $mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true)
        );

        $mail->setFrom($r->correo, htmlentities($r->nombre));
        $to = \App\level::where('name','Marketing')->first()->usuarios;

        foreach ($to as $value) {
          $mail->addAddress($value->email);
        }



        $c = explode(",",$r->archivos);
        if (count($c) > 0)  {
          foreach ($c as $key => $value) {
            if($value != "")
              $mail->addAttachment(storage_path()."/quejas/".$value);
          }
        }
        $severidad = [0=>"Leve",1=>"Media",2=>"Alta"];
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = "Quejas y sugerencias [Severidad: ".$severidad[$r->severidad]."]";
        $mail->Body= $r->descripcion;

        if($mail->send()){
            $driver = new ImapDriver($mail->Username,$mail->Password, 'mail.gruposhirushi.com', 143, FALSE);
            $connection = new Connection($driver);

            $connection->appendMail($mail->getString(),"INBOX.Sent");
        }
        return redirect('/')->with('status','Queja/Sugerencia enviada. Gracias por su ayuda, pronto nos pondremos en contacto.');

  }
  public function actualizar(Request $r)
  {
    \App\gaceta::find($r->id)->fill($r->all())->save();
    return redirect('/gaceta/publicaciones/lista')->with('status','Articulo actualizado');
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
        $file->move(storage_path()."/quejas/",md5($name).'.'.$data[1]);
        echo md5($name).'.'.$data[1];
     }

   }
   public function watchar(Request $r,$cid)
   {
     return Response::file(storage_path()."/quejas/".$cid);
   }
}
