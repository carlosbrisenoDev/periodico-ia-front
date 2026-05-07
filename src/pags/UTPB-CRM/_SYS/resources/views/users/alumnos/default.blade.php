@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
    @if(Auth::user()->cliente)
      @if (Auth::user()->cliente->baja == NULL)
        @if (Auth::user()->cliente->status >= 4  && Auth::user()->cliente->credito != null && Auth::user()->cliente->credito_info->status=="cartera")
          <div class="row">
            <div class="col-3">
              <div class="card card1">
                <div class="card-body">
                  <h5 class="card-title">Crédito</h5>
                  <h6 class="card-subtitle mb-2 text-muted">Métodos de pago</h6>
                  <label for="">Titular:</label>
                  <div class="form-control">
                    CORPORATIVO UNISANT SC
                  </div>
                  <label for="">Banco:</label>
                  <div class="form-control">
                    SCOTIABANK
                  </div>
                  <label>No. Cuenta</label>
                  <div class="form-control">
                    00106626893
                  </div>
                  <label for="">CLABE</label>
                  <div class="form-control">
                    044180001066268936
                  </div>
                </div>
              </div>
            </div>
            <div class="col-9">
              @if (count(Auth::user()->cliente->carteras) > 0 && Auth::user()->cliente->name == "PRUEBA")
                @foreach (Auth::user()->cliente->carteras as $_cartera)
                  @if ($_cartera->hasFirma == NULL)
                    <div class="card card1">
                      <div class="card-body">
                        <div class="alert alert-warning">
                          <div class="clearfix">
                            <div class="float-left">
                              <i class="fa fa-info"></i>
                              <b>No has firmado tu crédito {{$_cartera->concepto}}.</b> Firma tu crédito para contiuar con este beneficio de estudiante.
                            </div>
                            <div class="float-right">
                              <a href="/alumnos/firmar?cid={{md5($_cartera->id)}}" class="btn btn-primary">
                                <i class="fas fa-signature"></i> Ir a firmar
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endif
                @endforeach
              @endif
              <div class="card card1">
                <div class="card-body">
                  <h5 class="card-title">Crédito</h5>
                  <h6 class="card-subtitle mb-2 text-muted">Información de pago</h6>
                  <p class="card-text" style="min-height:60px;">
                    Próximo pago de credito
                  </p>
                  @if (count(Auth::user()->cliente->carteras) > 0)
                    @foreach (Auth::user()->cliente->carteras as $ct)
                      @if ($ct->tablapagos != null)
                        <a class="btn btn-light" href="/alumnos/cartera?cid={{md5($ct->tablapagos->cartera->id)}}">
                          Ver tabla de pagos de {{$ct->concepto}}
                        </a>
                      @endif
                    @endforeach
                  @endif
                  <hr>
                      @php
                        $dim2Mes = [
                            "Jan" => "Enero",
                            "Feb" => "Febrero",
                            "Mar" => "Marzo",
                            "Apr" => "Abril",
                            "May" => "Mayo",
                            "Jun" => "Junio",
                            "Jul" => "Julio",
                            "Aug" => "Agosto",
                            "Sep" => "Septiembre",
                            "Nov" => "Noviembre",
                            "Oct" => "Octubre",
                            "Dec" => "Diciembre",
                        ];
                        $dim2Month = array_flip($dim2Mes);
                      @endphp
                      @foreach (Auth::user()->cliente->carteras as $cartera)
                        @if ($cartera->tablapagos != null)
                          @foreach ($cartera->tablapagos->pagos as $pago)
                            @if (\Carbon\carbon::parse($dim2Month[$pago->mes]." ".$pago->anio)->isPast(\Carbon\carbon::now()) || $pago->pagado != null)
                              @if ($pago->pagado == null)
                                <div class="alert alert-warning">
                                  <div class="clearfix">
                                    <div class="float-left">
                                      <span style="color:black;">{{$pago->tabla->cliente->isinscripcion->nombre_completo}}</span>
                                      <p>
                                        <i>{{$pago->mes}} {{$pago->anio}}</i> - <span style="color:black;">{{$pago->pago}}</span>
                                      </p>
                                    </div>
                                    <div class="float-right">
                                      <i class="fas fa-money-check-alt"></i> Saldo pendiente:</br>
                                      <a class="btn btn-link" href="/alumnos/cartera?cid={{md5($pago->tabla->cartera->id)}}">
                                        Ver tabla de pagos
                                      </a>
                                    </div>
                                  </div>
                                </div>
                                @else
                                  <div class="alert alert-success">
                                    <div class="clearfix">
                                      <div class="float-left">
                                        <span style="color:black;">{{$pago->tabla->cliente->isinscripcion->nombre_completo}}</span>
                                        <p>
                                          <i>{{$pago->mes}} {{$pago->anio}}</i> - <span style="color:black;">{{$pago->pago}}</span>
                                        </p>
                                      </div>
                                      <div class="float-right">
                                        <i class="fas fa-money-check-alt"></i> Pagado:</br>
                                        <a class="btn btn-link" href="/alumnos/cartera?cid={{md5($pago->tabla->cartera->id)}}">
                                          Ver tabla de pagos
                                        </a>
                                      </div>
                                    </div>
                                  </div>
                              @endif
                            @endif
                          @endforeach
                          @else
                            <div class="alert alert-info">
                              <i class="fas fa-info-circle"></i>
                              El departamento de crédito esta generando tu tabla de pagos.
                            </div>
                        @endif
                      @endforeach
                </div>
              </div>
            </div>
          </div>
          <hr>
        @endif
        <div class="row">
          @if (Auth::user()->cliente->status <= 5)
            <div class="col-md-4 col-sm-6">
              <div class="card card1">
                <img src="{{asset('images/banner.jpeg')}}?a=1" class="img-fluid">
                <div class="card-body">
                  <h5 class="card-title">Inscripción</h5>
                  <h6 class="card-subtitle mb-2 text-muted">Formulario de información</h6>
                  <p class="card-text" style="min-height:60px;">
                    Formato de inscripción Universidad Santander Orizaba
                  </p>
                  @if (Auth::user()->cliente->isinscripcion == null)
                    <a href="/alumnos/formulario" class="card-link">Llenar</a>
                    @else
                      <div class="card-link btn btn-default">Llenado y enviado</div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4  col-sm-6">
              <div class="card">
                <img src="{{asset('images/banner3.jpeg')}}?a=1" class="img-fluid">
                <div class="card-body">
                  <h5 class="card-title">Inscripción</h5>
                  <h6 class="card-subtitle mb-2 text-muted">Carga de documentos</h6>
                  <p class="card-text" style="min-height:60px;">
                    Documentos requisito para la inscripción en Licenciatura, Maestría y Doctorado.
                  </p>
                  @if (Auth::user()->cliente->status == 2)
                    <a href="/alumnos/informacion" class="card-link">Llenar</a>
                    @else
                      <div class="card-link btn btn-default">Llenado y enviado</div>
                  @endif
                </div>
              </div>
            </div>
          @endif
            @if (Auth::user()->cliente->credito != null && (Auth::user()->cliente->cinfo()->status == ""))
            <div class="col-md-4 col-sm-6">
              <div class="card">
                <img src="{{asset('images/banner2.jpeg')}}?a=1" class="img-fluid">
                <div class="card-body">
                  <h5 class="card-title">Crédito</h5>
                  <h6 class="card-subtitle mb-2 text-muted">Solicitud de crédito</h6>
                  <p class="card-text" style="min-height:60px;">
                    Requisitos para solicitar un crédito para estudiar.
                  </p>
                  @if (Auth::user()->cliente->cinfo()->status == null)
                    <a href="/alumnos/credito" class="card-link">Llenar</a>
                    @else
                      <div class="card-link btn btn-default">Llenado y enviado</div>
                  @endif
                </div>
              </div>
            </div>
            @endif
            @if (Auth::user()->cliente->cinfo()->status == "preaprobado")
            <div class="col-md-4 col-sm-6">
              <div class="card">
                <img src="{{asset('images/banner2.jpeg')}}?a=1" class="img-fluid">
                <div class="card-body">
                  <h5 class="card-title">Mi crédito</h5>
                  <h6 class="card-subtitle mb-2 text-muted">¡Tu crédito ha sido pre aprobado!</h6>
                  <p class="card-text" style="min-height:60px;">
                    Completa tu solicitud de crédito, sube los documentos y completa la información anexa.
                  </p>
                  @if (Auth::user()->cliente->ccredito()->status == 0)
                      <a href="/alumnos/cocredito" class="card-link">Completar mi solicitud</a>
                    @else
                      <div class="card-link btn btn-default">Completada y enviada</div>
                  @endif
                </div>
              </div>
            </div>
            @endif
            @if (Auth::user()->cliente->cinfo()->status == "firmando")
            <div class="col-md-4 col-sm-6">
              <div class="card">
                <img src="{{asset('images/banner3.jpeg')}}?a=1" class="img-fluid">
                <div class="card-body">
                  <h5 class="card-title">Firma tu crédito</h5>
                  <h6 class="card-subtitle mb-2 text-muted">¡Tu crédito ha sido aprobado!</h6>
                  <p class="card-text" style="min-height:60px;">
                    Este es el último paso para obtener tu crédito para estudiar.
                  </p>
                  @if (Auth::user()->cliente->ccredito()->status == 2)
                      <a href="/alumnos/fcredito" class="card-link">Ir a firmar mi crédito</a>
                    @else
                      <div class="card-link btn btn-default">Completada y enviada</div>
                  @endif
                </div>
              </div>
            </div>
            @endif
            @if (Auth::user()->cliente->cinfo()->status == "cartera")
            <div class="col-md-4 col-sm-6">
              <div class="card">
                <img src="{{asset('images/banner2.jpeg')}}?a=1" class="img-fluid">
                <div class="card-body">
                  <h5 class="card-title">Tu crédito ha sido aprobado</h5>
                  <h6 class="card-subtitle mb-2 text-muted">¡Tu crédito ha sido aprobado!</h6>
                  <p class="card-text" style="min-height:60px;">
                    Si tienes algunda duda contacta al dpto de cr&eacute;dito <a href="mailto:credito@unisantorizaba.com">credito@unisantorizaba.com</a>
                  </p>
                  <div class="card-link btn btn-default"></div>
                </div>
              </div>
            </div>
            @endif
        </div>
        @else
          <div class="row">
            <div class="col">
              <div class="card card1">
                <div class="card-body">
                  <h5 class="card-title">{{\Auth::user()->cliente->isinscripcion->nombre_completo}}</h5>
                  <h6 class="card-subtitle mb-2 text-muted">Atención</h6>
                  <hr>
                  <center>
                    <h3>Tu cuenta se encuentra con baja temporal</h3>
                    <h6>Si crees que puede tratarse de un error, contacta con tu asesor vía correo electrónico</h6>
                    <a href="{{\Auth::user()->cliente->agente->email}}">{{\Auth::user()->cliente->agente->email}}</a>
                  </center>
                </div>
              </div>
            </div>
          </div>
      @endif
    @endif
@endsection
@section('styles')
  <style media="screen">
    hr{
      height:10px;
      background-color:#f6f6f6;
      border:0;
    }
    .line{
      height:2px;
      background-color:#f6f6f6;
      border:0;
      width:30%;
      margin:0;
      padding:0;
    }
    .text-muted{
      color:#BD773E !important;
    }
  </style>
@endsection
