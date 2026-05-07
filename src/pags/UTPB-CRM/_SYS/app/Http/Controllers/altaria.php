<?php

namespace App\Http\Controllers;
require 'AltiriaSMS/httpPHPAltiria.php';

use Illuminate\Http\Request;
use \AltiriaSmsPhpClient\AltiriaClient;
use \AltiriaSmsPhpClient\AltiriaModelTextMessage;
use \AltiriaSmsPhpClient\Exception\GeneralAltiriaException;
use Altiria\AltiriaSMS; 
// use \App\Http\Controllers\altaria\httpPHPAltiria as Altas; 
// require (app_path() .'/Http/Controllers/altaria/httpPHPAltiria.php');

class altaria extends Controller
{
    public function index(){
        return view('altaria.file');
    }

    public function filepost(Request $request){
        // require app_path() .'/Http/Controllers/altaria/httpPHPAltiria.php';
        // dd(AltasAltiriaSMS());
        if($request->hasFile('file')){
            $array = array();
            $csv = $request->file('file');
            $csvo = fopen($csv->getRealPath(), "r");
            while (($data = fgetcsv($csvo)) !== false) {
                $array[] = $data[0];
            }
            $numers = implode("','",$array);
            
            try{
                $altiriaSMS = new AltiriaSMS();
                $altiriaSMS->setLogin('jesusdavidvaldivia@gmail.com');
                $altiriaSMS->setPassword('Yjy6924');
                $altiriaSMS->setDebug(false);
                $destinations = $numers;
                $response = $altiriaSMS->sendSms($destinations, $request->msgText);

            }catch (\Exception $exception) {
                // echo 'Mensaje no aceptado:'.$exception->getMessage();
                return redirect("/sms/file")->with('status','Algo fallo, revisa el formato de los numeros o intenta mas tarde.');
            }
            if(!$response){
                // dd('error');
                return redirect("/sms/file")->with('status','Algo fallo, revisa el formato de los numeros o intenta mas tarde.');
            }
            else{
                return redirect("/sms/file")->with('status','Mensajes enviados correctamente');
                // dd('funciono');
            }

            dd('a');
        }
        dd($request);
    }
    
}
