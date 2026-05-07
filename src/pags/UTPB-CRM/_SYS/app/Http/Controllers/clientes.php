<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use \App\Mail\AutorizadoCliente;
use \App\documento;
use \App\Mail\NuevoCliente;
use \App\Mail\RemoveCliente;
use \App\historial;

class clientes extends Controller
{
  public function materiasalta(Request $r){
    $gestor = file_get_contents(storage_path()."/materias.csv");
    $arr = explode("\n", $gestor);
    $i = 0;
    foreach ($arr as &$line) {
      $line = str_getcsv($line);
      if ($i++ != 0) {
          \App\materias::create([
            "matricula"=>$line[1],
            "asignatura"=>$line[2],
            "clave"=>$line[3],
            "calificacion"=>$line[4],
            "situacion"=>$line[5],
            "grupo"=>$line[7]
          ]);
      }
    }
    unlink(storage_path()."/materias.csv");
    return redirect("/controlescolar/upload");
  }
  public function materias(Request $r){
    ini_set('upload_max_filesize', '2G');
    ini_set('post_max_size', '4G');
    ini_set('max_execution_time', '5000000');
    ini_set('max_input_time', '5000000');
    ini_set('memory_limit', '200M');
    if($r->hasFile('file')){
        $file = $r->file('file');
        $a = file_get_contents($file->getPathName());
        file_put_contents(storage_path()."/materias.csv",$a);
      }
      return redirect("/controlescolar/upload");
  }
  public function inscribir(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='".$r->cid."'")->first();

      $data = [
          'name' => $c->nombre,
          'email' => $c->correo,
          'level_id' => \App\level::where("name",'=','Clientes')->first()->id,
          'password' => bcrypt("UVO-".date("Y")."-".$c->id),
      ];

      $validator = \Validator::make($data, [
          'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:users',
      ]);

      if ($validator->fails()) {
          return redirect('/ventas/cliente?cid='.$r->cid)
                      ->with("status","Ya existe una cuenta de inscripción registrada con el correo ".$data["email"]);
      }

      $u = \App\User::create($data);
      $all = $r->all();
      unset($all["cid"]);
      $c->usuario_id = $u->id;
      $c->fill($all);
      $c->status = 2; // Significa acceso a subir su información
      $c->save();
      $u->codigo = "CUOV-".\Carbon\Carbon::parse($u->created_at)->year."-".$u->id;
      $u->save();

      // $u = (object) array(
      //   "generarClave"=>"Hola",
      //   "name"=>"Daiv",
      //   "nombre"=>"Jesus David",
      //   "email"=>"jesusdavidvaldivia@gmail.com"
      // );


      \App\historial::create(["usuario_id"=>0,'accion'=>"Ha creado una cuenta de Inscripción ".$u->name]);

      \Mail::to([$c->nombre=>$c->correo,\Auth::user()->email])->send(new AutorizadoCliente($c,$u));

      return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Cuenta de inscripci&oacute;n creada");

  }
  public function comprobante(Request $r){
      ini_set('upload_max_filesize', '2G');
      ini_set('post_max_size', '4G');
      ini_set('max_execution_time', '5000000');
      ini_set('max_input_time', '5000000');
      ini_set('memory_limit', '200M');
      $cliente = \App\cliente::whereRAW("md5(id)='".$r->cid."'")->first();
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
        $cliente->comprobante = $document->id;
        $cliente->save();
      }
      return redirect("/ventas/cliente?cid=".md5($cliente->id))->with('status','Comprobante subido');
  }
  public function sms(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='".$r->cid."'")->first();
    require ('altaria/httpPHPAltiria.php');
    //$lastone = \App\outbox::first();
    $altiriaSMS = new \AltiriaSMS();

    $altiriaSMS->setLogin('jesusdavidvaldivia@gmail.com');
    $altiriaSMS->setPassword('nb3tv5sf');

    $altiriaSMS->setDebug(false);
    $sDestination = '52'.$c->telefono;

    $mensaje = "URL:https://sii.unisantorizaba.com\nUsuario: ".$c->usuario->email."\nClave: ".$c->usuario->codigo2;
    $response = $altiriaSMS->sendSMS($sDestination, $mensaje);
    return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Acceso a la cuenta, enviado.");

  }
  public function facturas(Request $r){
      $delimiter = ",";
      $tabla = \App\inscripciones::all();
      $filename = "Facturas_" . date('Y-m-d') . ".csv";

      $f = fopen('php://memory', 'w');

      $fields = array('No.','Nombre','Télefono','Correo','Factura','Razón social','RFC','Correo Fiscal');
      fputcsv($f, $fields, $delimiter);
      $i = 1;
      foreach($tabla as $p){
        if (isset($p->cliente->nombre) && !strstr($p->cliente->nombre,"PRUEBA") && $p->cliente->status == 4 && $p->factura == "Si") {
          fputcsv($f, [$i++,$p->nombre_completo,$p->tel,$p->correo,$p->factura,$p->razon_social,$p->rfc,$p->correo_fiscal]);
        }
      }
      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');
      fpassthru($f);
  }
  public function set(Request $r){
    $empleado = \Auth::user()->empleado;
    $empleado->status = 3; // En revisión
    $empleado->save();
    return redirect("/alumnos/informacion")->with('status','Su estado ha sido cambiado a En revisión');
  }
  public function seto(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    if($c->agente_id==null){
      try{
        $uas = \App\user::find($r->v);
        if($uas->email){
            \Mail::to([$uas->email])->send(new NuevoCliente('Hey, Tienes un nuevo cliente asignado'.$c->nombre,$c,$uas));
        }   
      }
      catch(\Exception $e){
          // \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,$c->agente->name=>$c->agente->email])->send(new FirmarCliente($c,$r->razon));
      }
    }
    elseif($c->agente_id!=null){
      try{
        $uas = \App\user::find($r->v);
        if($uas->email){
            \Mail::to([$uas->email])->send(new NuevoCliente('Hey, Tienes un nuevo cliente asignado'.$c->nombre,$c,$uas));
        }
        $uasdos = \App\user::find($c->agente_id);
        if($uasdos->email){
          \Mail::to([$uasdos->email])->send(new RemoveCliente('Han reasignado al cliente '.$c->nombre.' a otro vendedor.',$c,$uasdos));
      }
      }
      catch(\Exception $e){
          // \Mail::to([$c->isinscripcion->nombre_completo=>$c->correo,$c->agente->name=>$c->agente->email])->send(new FirmarCliente($c,$r->razon));
      }
    }
    if($r->seto=='ofer'){
      if($r->v==0){

      }
      else{
        $oferta = \App\productos::where('nombre','like','%'.$r->v.'%')->first();
        if($oferta){
          $c->update(['producto_id'=>null]);
        }
        else{
          $oferta = \App\productos::create([
            'nombre' => $r->v,
            'tipo' => 'LICENCIATURA',
            'precio' => 0,
            'costo' => 0,
            'descuento_max' => 0,
            'tipo_descuento' => '$',
          ]);
          $c->update(['producto_id'=>$oferta->id]);
        }
      }
      
    }
    else{
      eval("\$c->".$r->seto."='".$r->v."';");
    }
    $c->save();
    return "Ok";
  }
  public function llenar(Request $r){
    $a = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    if($a->matricula != ""){
      $token = "4ba07dd78a8a6bc15844adebebffc342";
      $matricula = $a->matricula;
      $url = "http://plataformaunisant.mx/unisant/apiEstudy/externos/alumno/detalleAlumno.php?token=$token&matricula=$matricula";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
      $result = curl_exec($ch);
      curl_close($ch);

      $d = json_decode($result)->alumno;

      $a->isinscripcion->nombre_completo = $d->nombre." ".$d->primer_apellido." ".$d->segundo_apellido;
      $a->isinscripcion->fecha_nacimiento = $d->fecha_nacimiento;
      $a->isinscripcion->curp = $d->curp;
      $a->isinscripcion->no_domicilio = $d->calle." ".$d->cp;
      $a->isinscripcion->no_interior = $d->num_int;
      $a->isinscripcion->no_exterior = $d->num_ext;
      $a->isinscripcion->colonia = $d->colonia;
      $a->isinscripcion->estado = $d->estado;
      $a->isinscripcion->factura = $d->requiere_factura;
      $a->isinscripcion->razon_social = $d->razon_social;
      $a->isinscripcion->rfc = $d->rfc;
      $a->isinscripcion->correo_fiscal = $d->email;
      $a->isinscripcion->ultimo = $d->nivel_estudios;
      $a->isinscripcion->institucion = $d->nombre_institucion;

      $a->isinscripcion->save();

      return redirect()->back()->with("status","Datos sincronizados con plataforma");
    } else {
      return redirect()->back()->with("status","No hay matricula registrada, registra una primero");
    }
  }
  public function preciomateria(Request $r){
    $c = \App\cliente::whereRAW("matricula='$r->m'")->first();
    return isset($c) ? $c->pago_materia : "";
  }
  public function bymat(Request $r){
    $c = \App\cliente::whereRAW("matricula='$r->m'")->first();
    return redirect("/ventas/cliente?cid=".md5($c->id));
  }
  public function setoi(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first()->isinscripcion;
    eval("\$c->".$r->seto."='".$r->v."';");
    $c->save();
    return "Ok";
  }
  public function alumno(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    $c->status = 4;
    $c->save();
    return redirect('/ventas/cliente?cid='.$r->cid)->with("status","El cliente ahora es Alumno");
  }
  public function baja(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    $c->baja = 1;
    $c->save();
    return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Alumno dado de baja");
  }
  public function cash(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    $c->xmaterias = 1;
    $c->save();
    return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Alumno por materias");
  }
  public function uncash(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    $c->xmaterias = NULL;
    $c->save();
    return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Alumno por crédito");
  }
  public function agendarpago(Request $r){
    $pago = \App\pagos::whereRAW("md5(id)='".$r->pid."'")->first();
    $pago->agenda = $r->agenda;
    $pago->save();
    return redirect("/ventas/cliente?cid=".$r->cid)->with('status','Agenda de pago guardada');
  }
  public function alta(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    $c->baja = NULL;
    $c->save();
    return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Alumno dado de alta");
  }
  public function down(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    $c->status = 3;
    $c->save();
    return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Alumno suspendido");
  }
  public function forzar(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    $c->status = 3;
    $c->save();
    return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Envio de documentos cerrado");
  }
  public function credito(Request $r){
    $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
    if($r->credito == "null"){
      $c->credito = null;
      $c->save();
      return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Acceso a crédito desactivado");
    } else {
      $c->credito = $r->credito;
      $c->plazo = $r->plazo;
      $c->save();
      return redirect('/ventas/cliente?cid='.$r->cid)->with("status","Acceso a crédito activado");
    }
  }
  public function acredito(Request $r){
      $c = \App\cliente::whereRAW("md5(id)='$r->cid'")->first();
      $c->credito = $r->credito;
      $c->plazo = $r->plazo;
      $c->save();
      return redirect('/creditos/solicitud?cid='.md5($c->credito_info->id))->with("status","Acceso a crédito activado");
  }
  public function buscar(Request $r){
    $busqueda = \App\cliente::selectRAW("*, md5(id) as cid")
    ->where('nombre','like','%'.$r->t.'%')
    ->orWhere('correo','like','%'.$r->t.'%')
    ->orWhere('apat','like','%'.$r->t.'%')
    ->orWhere('amat','like','%'.$r->t.'%')
    ->orWhere('telefono','like','%'.$r->t.'%')
    ->orWhere('id','like','%'.$r->t.'%')
    ->get();
    return json_encode($busqueda);
  }

  public function deletebasal(Request $request){
    $c = \App\cliente::where('id',$request->cliente_id)->first();
    $c->status = 3;
    $c->comprobante = null;
    $c->save();
    return json_encode(["ok" => "ok","cid" => md5($c->id)]);
  }

  public function getAllDataInFile($token){
    if($token=='3d9d79e13fb200c8aed5be35bcccc265'){
      $clientes = \App\cliente::where('status',4)->get();
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
      foreach($clientes as $c){
        $oferta = null;
        $status = null;
        if($c->oferta){
          $oferta = $c->oferta->nombre;
        }
        $list->push((array)[
          "id" => $c->id,
          "nombre" => $c->nombre.' '.$c->apat.' '.$c->amat,
          "fecha" => $c->created_at, //$a->usuario->name,
          // "estado" => $c->estado,
          "programa" => $oferta ?? 'Sin datos',
          "status" => 'Inscrito',
          // "ciudad" => $co,
          // "edad" => $a->fecha_inicio,
        ]); 
        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject);
              
        $csv->insertOne(array_keys($list[0]));
        foreach ($list as $l) {
            $csv->insertOne($l);
        }
      }
      
      $csv->output('Registro de Clientes Inscritos - hasta el dia '.\App\Helper\Helper::fechaEswoutC(date("Y-m-d H:i:s")).'.csv');
      
      // return 1;
      return 1;
    }
    return 0;
  }

  public function crmClient(Request $request,$token){
    if($token=='57a0caf58c7cbda315f5def24f899d12'){
      if($request->has('email') && $request->has('name') && $request->has('phone') && $request->has('course')){
        $crm = \App\crm::create([
          'nombre' => $request->name,
          'apellidos' => $request->lastname,
          'telefono' => $request->phone,
          'correo' => $request->email,
          'curso' => $request->course,
          'nota' => $request->note,
        ]);
        $c = \App\cliente::create([
          'nombre' => $request->name,
          'apat' => $request->lastname,
          'telefono' => $request->phone,
          'correo' => $request->email,
          'tag' => 1,
          'antecedente' => 'educaedu',
        ]);
        $n = \App\notas_cliente::create(
          [
            "usuario_id" => 20,
            "cliente_id" => $c->id,
            "nota" => $request->note,
          ]
        );
        return 1;
      }
      return view('crm');
    }
    return 0;
  }

  public function crmSinapisssteClient(Request $request,$token){
    if($token=='57a0caf58c7cbda315f5def24f899d12'){
      if($request->has('email') && $request->has('name') && $request->has('phone') && $request->has('course')){
        $tag = 1;
        if($t_q = \App\tag::where('tag',$request->tag)->first()){
          $tag = $t_q->id;
        }
        $crm = \App\crm::create([
          'nombre' => $request->name,
          'apellidos' => $request->apat.' '.$request->amat,
          'telefono' => $request->phone,
          'correo' => $request->email,
          'curso' => $request->course,
          // 'nota' => $request->note,
        ]);
        $c = \App\cliente::create([
          'nombre' => $request->name,
          'apat' => $request->apat,
          'amat' => $request->amat,
          'telefono' => $request->phone,
          'correo' => $request->email,
          'tag' => $tag,
          'antecedente' => 'sinapissste',
        ]);
        // $n = \App\notas_cliente::create(
        //   [
        //     "usuario_id" => 20,
        //     "cliente_id" => $c->id,
        //     "nota" => $request->note,
        //   ]
        // );
        return redirect()->back();
        return 1;
      }
      return view('crm');
    }
    return 0;
  }

  public function crmClientEmg(Request $request,$token){
    if($token=='57a0caf58c7cbda315f5def24f899d12'){
      if($request->has('email') && $request->has('name') && $request->has('phone') && $request->has('course')){
        try{
          $crm = \App\crm::create([
            'nombre' => $request->name,
            'apellidos' => $request->lastName,
            'telefono' => $request->phone,
            'correo' => $request->email,
            'curso' => $request->idCourse,
            // 'curso' => $request->country,
            // 'nota' => $request->state,
          ]);
          $c = \App\cliente::create([
            'nombre' => $request->name,
            'apat' => $request->lastName,
            'telefono' => $request->phone,
            'correo' => $request->email,
            'tag' => 4,
            'antecedente' => $request->provider
          ]);
          $n = \App\notas_cliente::create(
            [
              "usuario_id" => 20,
              "cliente_id" => $c->id,
              "nota" => $request->provider,
            ]
          );
          return 1;
        }
        catch(\Exception $e){
          abort(503,'Algo fallo');
        }
      }
      else{
        return view('crmEmg');
      }
      
    }
    return response()->json([
      'status' => 505,
      'message' => 'KO'
    ], Response::HTTP_INTERNAL_SERVER_ERROR);
  }
    // return 0;

  public function registerClient(Request $request){
    // apat
    // amat
    // celular
    // email
    // nombre
    // brochure_id
    // antecedentes
    // user_id
    // asunto
    if($request->user_id == NULL){
      return 0;
    }
    try{
      $c = \App\cliente::create([
        'nombre' => $request->nombre,
        'apat' => $request->apat,
        'amat' => $request->amat,
        'telefono' => $request->celular,
        'correo' => $request->email,
        'tag' => 1,
        'antecedente' => $request->antecedentes,
        'agente_id' => 0,  
      ]);
      $p = \App\gaceta::where('id',$request->brochure_id)->first();
      $u = \App\User::where('id',$request->user_id)->first();
      try{
        // $asunto = $request->asunto;
        $newdesc = str_replace("%NOMBRE%",$c->name,$p->contenido);
        $newdesc = str_replace("%APAT%",$c->apat,$newdesc);
        $newdesc = str_replace("%AMAT%",$c->amat,$newdesc);
        $newdesc = str_replace("%NOMBREASESOR%",$u->name,$newdesc);
        $newdesc = str_replace("%CARGOASESOR%",$u->cargo ?? "Sin cargo",$newdesc);
        $newdesc = str_replace("%TELEFONOASESOR%",$u->telefono ?? "Sin teléfono",$newdesc);
        $newdesc = str_replace("%CORREOASESOR%",$u->email ?? "Sin correo",$newdesc);
    
        $email = $request->email;
        $subject = $p->titulo;
        $filespath = \App\filesfrompath::where("pathname","crearnuevagaceta?cid=".md5($p->id))->get();
        $asunto = $p->asunto;
        $files = [];
        $names = [];
        foreach($filespath as $fp){
          array_push($names,$fp->filename.".".$fp->documento->ext);
          array_push($files,\Cloud::url($fp->document_id));
        }
        // return $files;
        \Mail::to($email)->send(new \App\Mail\informacionEmail($asunto,$newdesc,$files,$names,$subject,$u));
      }
      catch(\Exception $e){
        return $e;
      }
      try{
        if($u->email){
            \Mail::to([$u->email])->send(new NuevoCliente('Hey, Tienes un nuevo cliente asignado'.$c->nombre,$c,$u));
        } 
      }
      catch(\Exception $e){
        return $e;
      }
      return 1;
    }
    catch(\Exception $e){
      return $e;
    }
  }
}
