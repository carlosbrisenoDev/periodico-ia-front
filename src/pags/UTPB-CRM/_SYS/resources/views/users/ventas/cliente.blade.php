@extends('users.'.Auth::user()->level->alias.'.home')
@section('styles')
<link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

<style media="screen">
  .form-switch .form-check-input:after {
    margin-top: 1px;
  }

  .ig {
    border-radius: 0.5rem 0px 0px 0.5rem !important;
  }

  hr {
    height: 10px;
    background-color: #f6f6f6;
    border: 0;
  }

  .line {
    height: 2px;
    background-color: #f6f6f6;
    border: 0;
    width: 30%;
    margin: 0;
    padding: 0;
  }

  .text-muted {
    color: #BD773E !important;
  }

  .chat {
    border: solid #f6f6f6 1px;
    border-radius: 10px;
  }

  .chat_header {
    height: 40px;
    line-height: 40px;
    padding-left: 20px;
    border-bottom: solid #f6f6f6 1px;
  }

  .chat_footer {
    height: 40px;
    border-top: solid #f6f6f6 1px;
  }

  .chat_body {
    height: 430px;
    overflow-y: auto;
    padding: 20px;
  }

  .chat_bod {
    height: 300px;
    overflow-y: auto;
    padding: 10px;
  }

  .chat .form-control {
    border: none;
    border-radius: 0;
  }

  .mensaje {
    padding: 0px 10px 1px 5px;
    margin-top: 5px;
    margin-bottom: 5px;
    display: inline-block;
    width: 80%;
    min-height: 50px;
  }

  .mini {
    display: block;
    font-size: 2mm;
  }

  .mleft {
    background-color: #4294FF;
    color: white;
    text-indent: 5px;
    border-radius: 5px 5px 5px 0px;
  }

  .mright {
    background-color: #B7BFCA;
    color: white;
    text-indent: 5px;
    border-radius: 5px 5px 0px 5px;
    margin-left: 20%;
  }

  .numero {
    cursor: pointer;
    text-decoration: underline;

  }

  .email {
    cursor: pointer;
    text-decoration: underline;
  }

  .form-check:not(.form-switch) .form-check-input[type="radio"]:after {
    margin-top: -1px;
    margin-left: -1px;
  }

  .select2-container--default .select2-selection--single {
    width: auto;
    height: 40px;
    padding: 5px;
  }
</style>


@endsection
@section('content')

@php
// dd($_REQUEST);
$c = \App\cliente::whereRAW("md5(id)='".$_REQUEST["cid"]."'")->first();
$consejosVenta = \App\sugerencias_venta::orderByRaw("RAND()")->limit(12)->get();
@endphp
<div class="row">
  <div class="col-md-12">
    <section class="pt-5 pb-5">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-6">
              <h5 class="mb-3">Consejos de Venta</h5>
            </div>
            <hr>
            <div class="col-6 text-right">
              {{-- <a class="btn btn-primary mb-3 mr-1" href="#carouselExampleIndicators2" role="button" data-slide="prev">
                          <i class="fa fa-arrow-left"></i>
                      </a>
                      <a class="btn btn-primary mb-3 " href="#carouselExampleIndicators2" role="button" data-slide="next">
                          <i class="fa fa-arrow-right"></i>
                      </a> --}}
            </div>
            <div class="col-12">
              <div id="carouselExampleIndicators2" class="carousel slide" data-ride="carousel">

                <div class="carousel-inner">
                  @foreach($consejosVenta as $index => $consejoVenta)
                  <div class="carousel-item @if($index==0) active @endif">
                    <div class="row">
                      <div class="col-md-12 mb-3">
                        <div class="card" style="background: #f5f5f5;">
                          <div class="card-body text-center">
                            <h6 class="card-title">{{$consejoVenta->titulo}}</h6>
                            <h4 class="card-title">{{$consejoVenta->descripcion}}</h4>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  @endforeach
                  {{-- <div class="carousel-item">
                                  <div class="row">
                                      @for($i=4;$i<8;$i++)    
                                        <div class="col-md-3 mb-3">
                                          <div class="card">
                                              <div class="card-body">
                                                  <h4 class="card-title">{{$consejosVenta[$i]->descripcion}}</h4>
                </div>
              </div>
            </div>
            @endfor
          </div>
        </div>
        <div class="carousel-item">
          <div class="row">
            @for($i=8;$i<12;$i++)
              <div class="col-md-3 mb-3">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">{{$consejosVenta[$i]->descripcion}}</h4>
                </div>
              </div>
          </div>
          @endfor
        </div>
      </div> --}}
  </div>
</div>
</div>
</div>
</div>
</div>
</section>
</div>
<div class="col-md-6 col-sm-12">
  <div class="card">
    <div class="card-body">
      <div class="col">
        <div class="float-end">
        </div>
      </div>
      <h5 class="card-title">Registro</h5>
      <h6 class="card-subtitle mb-2 text-muted">Cliente (UOV-{{date("Y")}}-{{$c->id}})</h6>
      <hr>
      <form class="" action="/clientes/nuevo" method="post">
        <div class="row">
          <div class="col-12 col-md-6 col-lg-4 col-xl-4">
            <label for="formGroupExampleInput" class="form-label">Nombre(s)</label>
            <input type="text" w="/clientes/seto" class="as form-control" name="nombre" placeholder="Nombre" value="{{$c->nombre}}">
          </div>
          <div class="col-12 col-md-6 col-lg-4 col-xl-4">
            <label for="formGroupExampleInput2" class="form-label">Apellido Paterno</label>
            <input type="text" w="/clientes/seto" class="as form-control" name="apat" placeholder="Apellido" value="{{$c->apat}}">
          </div>
          <div class="col-12 col-md-6 col-lg-4 col-xl-4">
            <label for="formGroupExampleInput2" class="form-label">Apellido Materno</label>
            <input type="text" w="/clientes/seto" class="as form-control" name="amat" placeholder="Apellido" value="{{$c->amat}}">
          </div>
          <div class="col-12 col-md-12 col-lg-6">
            <label for="formGroupExampleInput" class="form-label">Correo electr&oacute;nico</label>
            <div class="input-group mb-3">
              <input type="text" w="/clientes/seto" class="ig nn as form-control" name="correo" placeholder="alguien@gmail.com" value="{{$c->correo}}">
              @if($gaceta = \App\gaceta::first())
              <a data-bs-toggle="tooltip" data-bs-placement="top" title="Enviar información" href="#" class="btn btn-outline-primary mb-0 iBrochure" type="button" id="button-addon2">
                <i class="fa-solid fa-file-invoice"></i>
              </a>
              @endif
            </div>
          </div>
          <div class="col-12 col-md-12 col-lg-6">
            <label for="formGroupExampleInput2" class="form-label">N&uacute;mero de contacto</label>
            <div class="input-group mb-3">
              <input type="text" w="/clientes/seto" class="ig cc as form-control" name="telefono" placeholder="(555) 555 22 22" value="{{$c->telefono}}">
              @if(auth()->user()->ccuser && auth()->user()->ccpassword)
              <button class="btn btn-outline-primary mb-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Llamar por CallCenter" onclick="window.top.makeCall(document.querySelector('[name=telefono]').value)" type="button" id="button-addon2">
                <i class="fa-solid fa-phone"></i>
              </button>
              @endif

            </div>
          </div>
          <div class="col-12">
            <label for="formGroupExampleInput2" class="form-label">Antecedentes:</label>
            <textarea class="form-control as" w="/clientes/seto" required name="antecedente" style="width:100%;height:200px;" placeholder="¿De donde se obtuvo el lead?, problemas, puntos de cuidado.">{{$c->antecedente}}</textarea>
          </div>
          <input type="hidden" class="cid" value="{{$c->cid()}}">
        </div>
        <div class="col-12">
          <label for="formGroupExampleInput2" class="form-label">Escuela:</label>
          <select name="tag" id="" required class="form-control ig cc as" w="/clientes/seto">
            @foreach(\App\tag::get() as $tag)
            <option value="{{$tag->id}}" {{$c->tagsel($tag->id)}}>{{ $tag->tag }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12">
          <label for="formGroupExampleInput2" class="form-label">Oferta Educativa:</label>
          <select name="ofer" id="ofer" required class="form-control ig cc as" w="/clientes/seto">
            <option value="0">Ninguna</option>
            @foreach(\App\productos::get() as $p)
            <option value="{{$p->nombre}}" {{$c->psel($p->id)}}>{{ $p->nombre }}</option>
            @endforeach
          </select>
        </div>
        @if($c->agente != NULL)
        <div class="col-12">
          <label for="formGroupExampleInput2" class="form-label">Suscripción:</label>
          @php
          $cliente_suscripciones = \App\clientes_suscripciones::where("cliente_id",$c->id)->get();
          $cs_id = $cliente_suscripciones->pluck("suscripcion_id")->toArray();
          @endphp
          <select name="suscripcion_id" id="suscripcion_id" required class="form-control ig cc asreload" w="/suscripciones/addCliente">
            <option value="0">Ninguna</option>
            @foreach(\App\suscripciones::whereNotIn("id",$cs_id)->get() as $p)
            <option value="{{$p->id}}">{{$p->titulo}}</option>
            @endforeach
          </select>
          @if(count($cs_id) > 0)
          <br>
          <ul>
            <b>Suscripciones</b>
            @foreach ($cliente_suscripciones as $cs)
            <li>
              <a href="/ventas/clientesuscripcion?cid={{md5($cs->id)}}">{{$cs->suscripcion->titulo}}: Inició el {{\Carbon\Carbon::parse($cs->start_at)->format("Y-m-d H:i")}}</a>
            </li>
            @endforeach
          </ul>
          @endif
        </div>
        @else
        Debes de tener un agente asignado para suscribir a un cliente.
        @endif
        <br>

      </form>

      @include('componentes.notas_rapidas')
      <br>
      <div class="row">
        <hr>
        <div class="col">
          <hr>
          <a href="/clientes/inscribir/n?cid={{md5($c->id)}}" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Crear cuenta de Inscripci&oacute;n">
            <i class="fas fa-user-graduate"></i>
          </a>
          <button type="button" class="fecha btn btn-danger btn-agend" data-bs-toggle="tooltip" data-bs-placement="top" title="Agendar">
            <i class="fas fa-calendar-check"></i>
          </button>
          <i class="fas fa-grip-lines-vertical"></i>
          {{-- <a class="btn btn-info" target="_blank" href="/bandeja/nuevo/enviar?a={{$c->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Enviar un correo electr&oacute;nico"> --}}
          <a class="btn btn-info" target="_blank" href="mailto:{{$c->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Enviar un correo electr&oacute;nico">

            <i class="far fa-envelope"></i>
          </a>
          <a class="btn btn-success" target="_blank" href="https://api.whatsapp.com/send?phone=+521{{$c->telefono}}&text=Hola" data-bs-toggle="tooltip" data-bs-placement="top" title="Enviar WhatsApp">
            <i class="fab fa-whatsapp"></i>
          </a>
          <a target="_blank" class="btn btn-primary" href="https://unisantorizaba.com/mailman/subscribe/controlescolar_unisantorizaba.com/?email={{$c->correo}}">
            SUSCRIBIR
          </a>

          <a class="btn btn-danger btn-rmv-client" data-c="{{md5($c->id)}}">
            Eliminar
          </a>
          @if ($c->inscripcion != null)
          @php
          $sms = 1;
          //$sms = explode("credit(0):",shell_exec("curl -X POST -v -i 'http://www.altiria.net/api/http?cmd=getcredit&login=jesusdavidvaldivia%40gmail.com&passwd=nb3tv5sf'"))[1];
          @endphp
          @if ($sms > 1)
          <a class="btn btn-sea text-light" href="/clientes/sms/n?cid={{md5($c->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Enviar SMS con su acceso a plataforma">
            <i class="fas fa-sms"></i> {{$sms}}
          </a>
          @endif
          <i class="fas fa-grip-lines-vertical"></i>
          @if ($c->xmaterias == NULL)
          <div class="btn btn-warning text-light" data-bs-toggle="modal" data-bs-target="#creditoModal" data-bs-toggle="tooltip" data-bs-placement="top" title="Activar formulario de crédito">
            <i class="fas fa-dollar-sign"></i>
          </div>
          @endif
          @if ($c->xmaterias == NULL)
          @if ($c->credito == NULL)
          <a href="/clientes/cash/do?cid={{md5($c->id)}}" class="btn btn-unisant" data-bs-toggle="tooltip" data-bs-placement="top" title="Marcar alumno como pago por materias">
            <i class="fas fa-book text-light"></i>
          </a>
          @endif
          @else
          <a href="/clientes/uncash/do?cid={{md5($c->id)}}" class="btn btn-unisant" data-bs-toggle="tooltip" data-bs-placement="top" title="Marcar alumno como pago por crédito">
            <i class="far fa-money-bill-alt text-light"></i>
          </a>
          @endif
          <i class="fas fa-grip-lines-vertical"></i>
          <div class="btn btn-info" data-bs-toggle="modal" data-bs-target="#accesoModal" data-bs-toggle="tooltip" data-bs-placement="top" title="Datos de acceso">
            <i class="fas fa-universal-access"></i>
          </div>
          @if ($c->status == 2)
          <div class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#documentosModal" data-bs-toggle="tooltip" data-bs-placement="top" title="Forzar cerrado de documentos">
            <i class="fas fa-fist-raised"></i>
          </div>
          @endif
          @if ($c->status == 3)
          <div class="btn btn-danger alumno" data-bs-toggle="modal" data-bs-target="#alumnoModal" data-bs-toggle="tooltip" data-bs-placement="top" title="Convertir en Alumno">
            <i class="fas fa-graduation-cap"></i>
          </div>
          @endif

          @endif
          @if ($c->status == 4)
          <div class="btn btn-light disable" data-bs-toggle="tooltip" data-bs-placement="top" title="Este cliente ahora es un Alumno">
            <i class="fas fa-graduation-cap"></i>
          </div>
          @if ($c->baja == NULL)
          <a href="/clientes/baja/do?cid={{md5($c->id)}}" class="btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Alumno regular, presiona para dar de baja.">
            <i class="fas fa-toggle-on fa-2x text-success"></i>
          </a>
          @else
          <a href="/clientes/alta/do?cid={{md5($c->id)}}" class="btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Alumno con baja, presiona para dar de alta.">
            <i class="fas fa-toggle-on fa-2x text-warning"></i>
          </a>
          @endif
          @if ($c->comprobante == NULL)
          <div class="btn btn-leon text-light comprobante" data-bs-toggle="modal" data-bs-target="#comprobanteModal" data-bs-toggle="tooltip" data-bs-placement="top" title="Subir comprobante de pago">
            <i class="fas fa-upload"></i>
          </div>
          @else
          <a target="_blank" href="/documentos/watchar/{{md5($c->comprobante)}}" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver comprobante">
            <i class="fas fa-receipt"></i>
          </a>
          @endif
          @endif
          @if ($c->status == 4)
          {{-- <a href="/clientes/down/do?cid={{md5($c->id)}}" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Regresar a estado basal">
          <i class="fas fa-trash"></i>
          </a> --}}
          <button class="btn btn-danger btn-basal" data-bs-toggle="tooltip" data-bs-placement="top" title="Regresar a estado basal">
            <i class="fas fa-trash"></i>
          </button>
          @endif
        </div>
        @if ($c->status == 4)
        <div class="col-12">
          <hr>
          <div class="row">
            <div class="col-12 col-md-12 col-lg-6">
              <label for="matricula">Matricula:</label>
              <input type="text" placeholder="Matricula" class="form-control as" w="/clientes/seto" name="matricula" value="{{$c->matricula}}">
            </div>
            <div class="col-12 col-md-12 col-lg-6">
              <label for="matricula">Precio por materia:</label>
              <input type="text" placeholder="$1520" class="form-control as" w="/clientes/seto" name="pago_materia" value="{{$c->pago_materia}}">
            </div>
          </div>
        </div>
        @endif
        <div class="col-12">
          <hr>
          @php
          $lista = \App\level::select("id")->where('name','Ventas')->orWhere("name","Control escolar");
          @endphp
          <label for="" class="form-label">Asignado a:</label>
          <select class="form-control as" name="agente_id" w="/clientes/seto">
            <option>Seleccionar</option>

            @foreach (\App\User::whereIn('level_id',$lista->get()->toArray())->get() as $key => $value)
            <option {{($c->agente_id == $value->id) ? "selected" : ""}} value="{{$value->id}}">{{$value->name}}</option>
            @endforeach
          </select>
          <br>
        </div>
        <div class="col-12 table-responsive">
          <h5 class="card-title">Llamadas:</h5>
          @php
          $num = substr($c->telefono,strlen($c->telefono)-10,10);
          echo $num;
          $num = utf8_encode($num);
          $calls = \App\contact_id::where("phone","like","+52%$num%")->orWhere("phone","like","%$num%")->get()->sortByDesc("created_at");
          @endphp
          <hr>
          <table class="table table-striped">
            @foreach ($calls as $item)
            <tr>
              <td colspan="3" class="text-center">
                {{$item->queue}}
              </td>
            </tr>
            <tr>
              <td>{{$item->created_at}}</td>
              <td>{{$item->agent}}</td>
              <td class="text-center">
                <audio class="call-{{$item->id}}" src="#" controls>
                </audio>
              </td>
            </tr>
            @endforeach
          </table>
        </div>
        <div class="col-12 table-responsive">
          <hr>
          <table class="table table-striped">
            <tr>
              <td>Fecha de alta:</td>
              <td>{{\Carbon\carbon::parse($c->created_at)}}</td>
            </tr>
            <tr>
              <td>Última modificación</td>
              <td>{{\Carbon\carbon::parse($c->updated_at)}}</td>
            </tr>
          </table>
        </div>
        @if ($c->credito != null)
        <div class="col-12">
          <hr>
          <h5 class="card-title">Crédito</h5>
          <h6 class="card-subtitle mb-2 text-muted">(CUOV-{{date("Y")}}-{{$c->id}})</h6>
          <table class="table table-striped">
            <tr>
              <td>Estado:</td>
              <td>{{$c->cinfo()->status}}</td>
            </tr>
          </table>
        </div>
        @endif
      </div>
    </div>
  </div>
  @if ($c->status >= 4)
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Materias</h5>
      @include('componentes.materias-control')
      </table>
    </div>
  </div>
  @endif
  @if (count($c->agenda) > 0)
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Fechas</h5>
      <h6 class="card-subtitle mb-2 text-muted">Recordatorios con el cliente: </h6>
      <hr>
      <div class="row">
        @foreach ($c->agenda as $f)
        <div class="col-12">
          <label for="">{{$f->evento}}</label>
          <small>
            {{\carbon\carbon::parse($f->year."/".$f->mes."/".$f->dia." ".$f->hora.":".$f->minuto)->diffForHumans()}}
          </small>
          <p class="form-control" style="height:auto;">
            @if (empty($f->nota))
            Sin notas adicionales
            @else
            "{{$f->nota}}"
            @endif
          </p>
          <hr>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif
  @if (count($c->agenda) > 0)
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Recibidos</h5>
      <h6 class="card-subtitle mb-2 text-muted">Conversaci&oacute;n por correo electr&oacute;nico </h6>
      <hr>
      {{-- <div class="row">
                <ul class="col">
                  @php
                    $con = \App\Http\Controllers\bandeja::getCon();
                    $names = ["Notes"=>"Notas","Archive"=>"Archivo","spam"=>"No deseados","Sent"=>"Enviados","INBOX"=>"Recibidos","Drafts"=>"Borrador","Trash"=>"Eliminados","Junk"=>"Basura"];
                    $box1 =  "INBOX";
                    $rebox = (!strstr($box1,"INBOX")) ? "INBOX.$box1" : $box1;
                    $mailbox = $con->getMailbox($rebox);
                    $selection = $mailbox->getMails();
                  @endphp
                  @if (count($selection->fetchAll()) > 0)

                    <table class="table">
                      @foreach ($selection->fetchAll() as $index => $mail)
                        @php
                          $flags = $mail->getFlags();
                        @endphp
                        @if ($mail->getHeader("to") == "<".$c->correo.">" || strstr($mail->getHeader("from"),"noreply"))
                          <tr>
                            <td>
                              <a class="btn btn-primary" href="/bandeja/correo/listar?mail={{$index}}&box=INBOX&from=" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver mensaje">
      <i class="far fa-envelope-open"></i>
      </a>
      </td>
      <td>
        <label>
          <b>{{substr($mail->getHeader("Subject"),0,55)}}</b>
        </label>
      </td>
      <td>
        {{substr($mail->getTextBody(),0,50)}}
      </td>
      <td>
        {{\Carbon\Carbon::parse($mail->getHeader("Date"))->diffForHumans()}}
      </td>
      </tr>
      @endif
      @endforeach
      </table>
      @else
      No hay mensajes contigo
      @endif
      </ul>
    </div> --}}
  </div>
</div>

@endif
</div>
<div class="col-md-6 col-sm-12">
  @if ($c->credito_info != NULL && $c->credito_info->status == "cartera")
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Pagos:</h5>
      <h6 class="card-subtitle mb-2 text-muted">Pagos de crédito</h6>
      <hr>
      @php
      $meses_ingles = array( "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" );
      $meses_espanol = array( 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre', );
      $dim2Mes = [ "Jan" => "Enero", "Feb" => "Febrero", "Mar" => "Marzo", "Apr" => "Abril", "May" => "Mayo", "Jun" => "Junio", "Jul" => "Julio", "Aug" => "Agosto", "Sep" => "Septiembre", "Nov" => "Noviembre", "Oct" => "Octubre", "Dec" => "Diciembre", ];
      $dim2Month = array_flip($dim2Mes);
      @endphp
      @foreach ($c->carteras as $cartera)
      @if ($cartera->tablapagos != null)
      <div class="table-responsive" style="background-color:#f6f6f6;margin-bottom:20px;padding:15px;border:solid #ccc 2px;">
        <label for="">{{$cartera->concepto}}</label><br>
        <hr>
        <table class="table">
          @foreach ($cartera->tablapagos->pagos as $pago)
          @if (\Carbon\carbon::parse($dim2Month[$pago->mes]." ".$pago->anio)->isPast(\Carbon\carbon::now()) || ($pago->pagado != null))
          @php
          $status = $pago->status == 1 ? "bg-success" : ($pago->pagado != null ? "bg-warning" : "bg-danger");
          @endphp
          <tr class='{{$status}}'>
            <td>{{$pago->numero}}</td>
            <td>{{$pago->anio}}</td>
            <td>
              <a target="_blank" href="/documentos/watchar/{{md5($pago->pagado)}}">
                {{$pago->mes}}
              </a>
            </td>
            <td>{{$pago->acumulado}}</td>
            <td>{{$pago->pago}}</td>
            <td>{{$pago->capital}}</td>
            <td>{{$pago->interes}}</td>
            <td></td>
          </tr>
          @if ($status != "bg-success")
          <tr>
            <td colspan="7">
              <form class="" action="/clientes/agendarpago" method="post">
                <input type="hidden" name="cid" value="{{md5($c->id)}}">
                <input type="hidden" name="pid" value="{{md5($pago->id)}}">
                <div class="input-group mb-3">
                  <input type="date" value="{{$pago->agenda}}" name="agenda" class="form-control" aria-label="Fecha agendada de pago" aria-describedby="basic-addon2">
                  <div class="input-group-append">
                    <button type="submit" class="input-group-text btn btn-success" id="basic-addon2">Agendar</button>
                  </div>
                </div>
              </form>
            </td>
          </tr>
          @endif
          @endif
          @endforeach
        </table>
        <hr>
        <form class="" action="/clientes/nuevo" method="post">
          <div class="row">
            <div class="col-12">
              <label for="formGroupExampleInput" class="form-label">Nota de crédito:</label>
              <input type="text" w="/clientes/seto" class="as form-control" name="nota_credito" placeholder="Nota de crédito" value="{{$c->nota_credito}}">
            </div>
          </div>
        </form>
      </div>

      @else
      <p class="text-center">
        <i class="fas fa-exclamation-circle text-warning fa-3x"></i>
      </p>
      <h5 class="text-center">Este alumno aún no tiene su tabla de pagos generada</h5>
      @endif
      @endforeach
    </div>
  </div>
  @endif
  @if ($c->inscripcion != null)
  <div class="card">
    <div class="card-body table-responsive">
      <h5 class="card-title">Cuenta de inscripción:</h5>
      <h6 class="card-subtitle mb-2 text-muted">Información de estado</h6>
      <hr>
      <table class="table">
        <tr>
          <td>Estado de la cuenta:</td>
          <td>
            <small>
              {{$c->inscripcion->estado()}}
            </small>
          </td>
        </tr>
        <tr>
          <td>Inscripci&oacute;n</td>
          <td>
            @if ($c->isinscripcion != null)
            <a class="btn btn-link formulario" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver formulario">
              <small>
                Ver inscripci&oacute;n
              </small>
            </a>
            @else
            No enviado
            @endif

          </td>
        </tr>
      </table>
      @if ($c->isinscripcion != null)
      <div class="collapse" id="collapseExample">
        <div class="card card-body">
          @include('componentes.datosalumno')
        </div>
      </div>
      @endif
    </div>
  </div>
  @endif
  @include('componentes.documentosview')
  @include('componentes.documentosup')
  @if ($c->status >= 4)
  @include('componentes.comprobante')
  @endif

  @include('componentes.notas')
  @include('componentes.graph_materias')
</div>
</div>
@include('componentes.credito')
@include('componentes.alumno')
@if ($c->usuario != NULL)
@include('componentes.acceso')
@endif
@include('componentes.documentos')

@endsection
@section('scripts')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script>
  $("#ofer").select2({
    tags: true,
    createTag: function(params) {
      return {
        id: params.term,
        text: params.term,
        newOption: true
      }
    },
    templateResult: function(data) {
      var $result = $("<span></span>");

      $result.text(data.text);

      if (data.newOption) {
        $result.append(" <em>(nuevo)</em>");
      }

      return $result;
    }
  });
</script>
<script src="{{ asset('js/dropzone.js') }}"></script>
<script>
  @if($gaceta = \App\ gaceta::first())
  $(".iBrochure").bind("click", () => {
    console.log('a')
    let me = event.target;
    Swal.fire({
      "title": "¿Que iBrochure desea enviar?",
      "icon": "question",
      "html": '<label>Asunto</label>' +

        '<input type="text" class="form-control" name="asunto" id="asuntoform" value="{{$gaceta->asunto }}">' +

        '<label>¿Elige el brochure?</label>' +
        '<select class="form-control ibrochureform" name="ibrochure" id="ibrochureform">' +
        @foreach(\App\ gaceta::all() as $item)
      '<option value="{{md5($item->id)}}" data-asunto="{{$item->asunto}}">{{$item->titulo}}</option>' +
      @endforeach '</select>',
      // "input" : "select",
      // "inputAttributes" : {
      //   "requires" : true,
      //   "class" : "form-control"
      // },
      // "inputOptions" : {
      //   @foreach(\App\gaceta::all() as $item)
      //      "{{md5($item->id)}}" : "{{$item->titulo}} ({{$item->asunto}})",
      //   @endforeach
      // },
      "showConfirButton": true,
      "showCancelButton": true,
      "confirmButtonText": "Enviar",
      "cancelButtonText": "Cancelar",
      showLoaderOnConfirm: true,
      preConfirm: (input) => {
        return fetch(`/gaceta/sendcliente`, {
            method: "POST",
            body: JSON.stringify({
              "cliente_id": "{{$c->id}}",
              "ibrochure": $('#ibrochureform').val(),
              "asunto": $('#asuntoform').val(),
              "_token": $("meta[name=csrf-token]").attr("content")
            }),
            headers: {
              "Content-type": "application/json; charset=UTF-8"
            }
          })
          .then(response => {
            if (!response.ok) {
              throw new Error(response.statusText)
            }
            return response.json()
          })
          .catch(error => {
            Swal.showValidationMessage(
              `Request failed: ${error}`
            )
          })
      },
      allowOutsideClick: () => !Swal.isLoading()
    }).then(result => {
      console.log(result);
      if (result.isConfirmed) {
        Swal.fire(
          '¡Excelente!',
          'El correo ha sido enviado',
          'success'
        )
      }
    });
  });
  @endif
  $(document).on('change', '#ibrochureform', function(e) {
    $('#asuntoform').val($(this).find('option:selected').attr('data-asunto'));
    // console.log($(this));
  });

  $(".btn-rmv-client").on("click", function(e) {
    var cl = $(this).attr('data-c');
    Swal.fire({
      icon: 'error',
      title: '¿Seguro que quieres eliminar a este cliente?',
      showCancelButton: true,
      confirmButtonText: 'Si',
      cancelButtonText: `Cancelar`,
      showLoaderOnConfirm: true,
      preConfirm: (login) => {},
    }).then((result) => {
      if (result.isConfirmed) {
        console.log($('#deletec'))
        $(location).attr('href', `{{url('ventas/cliente/removeClient/${cl}')}}`);
        // $('#deletec'+cl).click();
      }
    })
  });
</script>
<script type="text/javascript">
  $(".as").bind("change", function() {
    $(this.parentNode).find("label").append("<i class='fas fa-cog fa-spin'></i>");
    $.post($(this).attr("w") + "?seto=" + $(this).prop("name") + "&cid=" + $(".cid").val() + "&v=" + $(this).val(), function(data) {
      $("label").find("i").remove()
    });
  });

  $(".asreload").bind("change", function() {
    $(this.parentNode).find("label").append("<i class='fas fa-cog fa-spin'></i>");
    $.post($(this).attr("w") + "?seto=" + $(this).prop("name") + "&cid=" + $(".cid").val() + "&v=" + $(this).val(), function(data) {
      $("label").find("i").remove()
      location.reload();
    });
  });

  $(function() {
    $(".formulario").bind("click", function() {
      $(".collapse").toggle();
    })
    $(".dz-message").html('<i class="fas fa-file-upload fa-3x"></i>' + "</br>Arrastra y suelta aquí archivos para adjuntarlos");
  })

  $(document).ready(function() {
    var myDropzone = new Dropzone("#dropzone");
    myDropzone.on("addedfile", function(file) {
      $(".enviar").addClass("disabled");
    });
    myDropzone.on("complete", function(file) {
      $(".enviar").removeClass("disabled");
    });
    myDropzone.on("success", function(file, data) {
      $(".archivos").val($(".archivos").val() + data + ",")
    });
  });


  $(document).on('click', '.btn-agend', async function(event) {
    Swal.fire({
      "title": "¿En que fecha y hora deseas agendar?",
      "icon": "question",
      "html": '<input type="datetime-local" class="form-control" id="agendtime" autofocus required><br><input type="text" class="form-control" placeholder="Nota" id="noteAgend">',
      // "input" : "time",
      // "inputAttributes" : {
      //   "requires" : true,
      //   "class" : "form-control"
      // },
      "showConfirmButton": true,
      "showCancelButton": true,
      "confirmButtonText": "Guardar",
      "cancelButtonText": "Cancelar",
      showLoaderOnConfirm: true,
      preConfirm: (input) => {
        if ($('#agendtime').val().length <= 0) {
          Swal.showValidationMessage(
            `No dejes campos vacios.`
          )
          return false;
          event.preventDefault();
        }
        if ($('#noteAgend').val().length <= 0) {
          Swal.showValidationMessage(
            `No dejes campos vacios.`
          )
          return false;
          event.preventDefault();
        }
        return fetch(`/ventas/agend`, {
            method: "POST",
            body: JSON.stringify({
              "cliente_id": "{{$c->id}}",
              "nota": $('#noteAgend').val(),
              "fecha": $('#agendtime').val(),
              "_token": '{{csrf_token()}}'
            }),
            headers: {
              "Content-type": "application/json; charset=UTF-8"
            }
          })
          .then(response => {
            if (!response.ok) {
              throw new Error(response.statusText)
            }
            return response.json()
          })
          .catch(error => {
            Swal.showValidationMessage(
              `Request failed: ${error}`
            )
          })
      },
      allowOutsideClick: () => !Swal.isLoading()
    }).then(result => {
      console.log(result);
      if (result.isConfirmed) {
        Swal.fire(
          '¡Excelente!',
          '¡Se guardo la agenda con exito!',
          'success'
        )
      }
    });
  });

  $(document).on('click', '.btn-basal', async function(event) {
    var deleteF = 0;
    if ($('#fileDeleted').is(':checked')) {
      deleteF = 1;
    }
    Swal.fire({
      "title": "¿Seguro que quieres hacerlo?",
      "icon": "question",
      "html": `<div class="form-check form-switch mb-4 d-flex align-items-center" style="justify-content: center;">
          <input class="form-check-input" type="checkbox" id="fileDeleted">
          
          <label class="form-check-label ms-3 mb-0" for="fileDeleted">¿Quieres borrar los comprobantes?</label>
        </div>`,
      "showConfirmButton": true,
      "showCancelButton": true,
      "confirmButtonText": "Aceptar",
      "cancelButtonText": "Cancelar",
      showLoaderOnConfirm: true,
      preConfirm: (input) => {
        return fetch(`/clientes/deletebasal`, {
            method: "POST",
            body: JSON.stringify({
              "cliente_id": "{{$c->id}}",
              "filedelete": deleteF,
              "_token": '{{csrf_token()}}'
            }),
            headers: {
              "Content-type": "application/json; charset=UTF-8"
            }
          })
          .then(response => {
            if (!response.ok) {
              throw new Error(response.statusText)
            }
            return response.json()
          })
          .catch(error => {
            Swal.showValidationMessage(
              `Request failed: ${error}`
            )
          })
      },
      allowOutsideClick: () => !Swal.isLoading()
    }).then(result => {
      console.log(result);
      if (result.isConfirmed) {
        Swal.fire(
          '¡Excelente!',
          '¡Se guardo la agenda con exito!',
          'success'
        )
        location.reload();
      }
    });
  });


  $(document).on('click', '.quicknote', async function(event) {
    $('#notarapidatextarea').text('');
    $('#notarapidatextarea').text($(this).attr('data-note'));
  });
</script>
@if ($calls->count() > 0)
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.826.0.min.js"></script>
<script>
  // $(".iBrochure").bind("click",() => {
  //   console.log('a')
  //   let me = event.target;
  //   Swal.fire({
  //     "title" : "¿Que iBrochure desea enviar?",
  //     "icon" : "question",
  //     "input" : "select",
  //     "inputAttributes" : {
  //       "requires" : true,
  //       "class" : "form-control"
  //     },
  //     "inputOptions" : {
  //       @foreach(\App\gaceta::all() as $item)
  //          "{{md5($item->id)}}" : "{{$item->titulo}} ({{$item->asunto}})",
  //       @endforeach
  //     },
  //     "showConfirButton" : true,
  //     "showCancelButton" : true,
  //     "confirmButtonText" : "Enviar",
  //     "cancelButtonText" : "Cancelar",
  //     showLoaderOnConfirm: true,
  //     preConfirm: (input) => {
  //       return fetch(`/gaceta/sendcliente`,{
  //         method: "POST",
  //         body: JSON.stringify({
  //           "cliente_id" : "{{$c->id}}",
  //           "ibrochure" : input,
  //           "_token" : $("meta[name=csrf-token]").attr("content")
  //         }),
  //         headers: {"Content-type": "application/json; charset=UTF-8"}
  //       })
  //         .then(response => {
  //           if (!response.ok) {
  //             throw new Error(response.statusText)
  //           }
  //           return response.json()
  //         })
  //         .catch(error => {
  //           Swal.showValidationMessage(
  //             `Request failed: ${error}`
  //           )
  //         })
  //     },
  //     allowOutsideClick: () => !Swal.isLoading()
  //   }).then(result => {
  //     console.log(result);
  //     if(result.isConfirmed){
  //       Swal.fire(
  //           '¡Excelente!',
  //           'El correo ha sido enviado',
  //           'success'
  //         )
  //     }
  //   });
  // });

  AWS.config.region = 'us-east-2'; // Región
  AWS.config.credentials = new AWS.CognitoIdentityCredentials({
    IdentityPoolId: 'us-east-2:7f08076b-c8ce-4828-8449-c2c0c7a179ca',
  });

  var ultimos = function() {
    var s3 = new AWS.S3({
      apiVersion: '2006-03-01',
      params: {
        Bucket: "registrodellamadasedav"
      }
    });

    let files = [
      @foreach($calls as $call) {
        "md5": "call-{{$call->id}}",
        "id": "{{$call->contact_id}}",
        "year": "{{\Carbon\Carbon::parse($call->created_at)->format("
        Y ")}}",
        "month": "{{\Carbon\Carbon::parse($call->created_at)->format("
        m ")}}",
        "day": "{{\Carbon\Carbon::parse($call->created_at)->format("
        d ")}}"
      },
      @endforeach
    ];

    files.forEach((e) => {
      let {
        md5,
        id,
        year,
        month,
        day
      } = e;

      let params = {
        Prefix: `connect/edav/CallRecordings/${year}/${month}/${day}/${id}`
      }

      s3.listObjects(params, function(err, data) {
        if (err) {
          console.log(data);
          document.querySelector(`.${md5}`).parentNode.text("Audio no disponible aún.")
        }
        try {
          let key = data["Contents"][0]["Key"];
          const url = s3.getSignedUrl('getObject', {
            Bucket: "registrodellamadasedav",
            Key: key,
            Expires: 60 * 5
          });
          console.log(url);

          document.querySelector(`.${md5}`).src = url;
        } catch (e) {
          document.querySelector(`.${md5}`).parentNode.innerText = ("Audio no disponible aún.")
        }
      });
    });

  }

  $(function() {
    ultimos();
  });
</script>
@endif
@endsection
@section('styles')
<link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
@endsection