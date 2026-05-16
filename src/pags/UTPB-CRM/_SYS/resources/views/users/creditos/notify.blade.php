@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Cartera</h5>
          <h6 class="card-subtitle mb-2 text-muted">Notificar pagos pendientes</h6>
          <hr>

          @php
            $i = 1;
          @endphp
          <table>
          @foreach (\App\pagos::where("pagado",NULL)->orderBy("created_at","desc")->groupBy("tabla_id")->get() as $pago)
            @if ((\Carbon\carbon::parse($pago->anio."-".$pago->mes_en."-1")->subDays(2)->isPast() && $pago->tabla->cliente->baja == NULL) && $pago->status != 9)
              @php
                $atraso = \Carbon\carbon::parse($pago->anio."-".$pago->mes_en."-1")->diffInDays(\Carbon\carbon::now());
                $bg = $atraso >= 60 ? "bg-danger" : ($atraso >= 30 ? "bg-warning" : "");

                // <tr>
                //   <td>{{$pago->tabla->cliente->isinscripcion->nombre_completo}}</td>
                //   <td>{{$pago->tabla->cliente->telefono}}</td>
                //   <td>{{$pago->tabla->cliente->correo}}</td>
                //   <td>{{$atraso}} días de atraso</td>
                //   <td>{{$pago->pago}}</td>
                // </tr>
              @endphp
              {{$pago->tabla->cliente->correo}},
            @endif
          @endforeach
        </table>
            <div class="row">
                @php
                  $i = 1;
                @endphp

                @foreach (\App\pagos::where("pagado",NULL)->orderBy("created_at","desc")->get() as $pago)
                  @if ((\Carbon\carbon::parse($pago->anio."-".$pago->mes_en."-1")->subDays(2)->isPast() && $pago->tabla->cliente->baja == NULL) && $pago->status != 9)
                    @php
                      $atraso = \Carbon\carbon::parse($pago->anio."-".$pago->mes_en."-1")->diffInDays(\Carbon\carbon::now());
                      $bg = $atraso >= 60 ? "bg-danger" : ($atraso >= 30 ? "bg-warning" : "");
                    @endphp
                    <div class="col-12 {{$bg}} card card-body">
                      <div class="row">
                        <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                            <div class="clearfix">
                              <div class="float-start">
                                <div class="">
                                  <a class="btn" href="/creditos/cartera?cid={{md5($pago->tabla->cartera->id)}}">
                                    #{{$i++}}
                                    {{$pago->tabla->cliente->isinscripcion->nombre_completo}}
                                  </a>
                                </div>
                                @if ($pago->agenda != NULL)
                                  <div style="padding-left:12px;" class="text-dark">
                                    <small>
                                        Promesa de pago: {{$pago->agenda}}
                                    </small>
                                  </div>
                                @endif
                              </div>
                              <div class="float-end">
                                <a class="m-1" href="/ventas/cliente?cid={{md5($pago->tabla->cliente->id)}}">
                                  <i class="fas fa-user"></i>
                                </a>
                                <a class="m-1" class="text-success" target="_blank"  href="https://api.whatsapp.com/send?phone=+521{{$pago->tabla->cliente->telefono}}&text=Hola" data-bs-toggle="tooltip" data-bs-placement="top" title="Enviar WhatsApp">
                                  <i class="fab fa-whatsapp"></i>
                                </a>
                                <a class="m-1" class="text-info" target="_blank"  href="mailto:{{$pago->tabla->cliente->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Enviar Correo">
                                  <i class="fa fa-envelope"></i>
                                </a>
                                <a class="m-1" class="text-success" href="/creditos/creditos/?cid={{md5($pago->tabla->cliente->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Créditos">
                                  <i class="fas fa-credit-card"></i>
                                </a>
                            </div>
                          </div>
                          @if ($pago->tabla->cliente->nota_credito != NULL)
                            <div class="col-12">
                              <div class="alert alert-danger">
                                <small>
                                  Nota de crédito:
                                  {{$pago->tabla->cliente->nota_credito}}
                                </small>
                              </div>
                            </div>
                          @endif
                        </div>
                        <div class="col-8">
                          <div class="card">
                            <div class="card-body">
                              <div class="float-start">
                                {{$pago->mes."/".$pago->anio}}
                                <small class="text-info">
                                   {{$atraso}} días de atraso
                                </small>
                              </div>
                              <div class="float-end">
                                {{$pago->pago}}
                              </div>
                            </div>
                            <div class="card-body">
                              <div class="float-start">
                                <small>  Última notificación hace
                                  {{\Carbon\carbon::parse($pago->updated_at)->diffInHours(\Carbon\carbon::now())}} horas</small>
                              </div>
                              <div class="float-end">
                              @if ($pago->notify == 0)
                                  <a class="btn btn-link" href="/pagos/notify/this?cid={{md5($pago->id)}}">
                                    <i class="fas fa-bell"></i>
                                  </a>
                                @else
                                  <i class="fas fa-check-circle text-success"></i>
                              @endif
                              @if ($pago->notifysms == 0)
                                  <a class="btn btn-link" href="/pagos/notifysms/this?cid={{md5($pago->id)}}">
                                    <i class="fas fa-sms"></i>
                                  </a>
                                @else
                                  <i class="fas fa-check-circle text-success"></i>
                              @endif
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endif
                @endforeach
              </div>
            <hr>
            <div class="clearfix">
              <div class="float-start">
                <a href="/pagos/notifys/all" class="btn btn-success">
                  <i class="fas fa-bell"></i>
                </a>
              </div>
              <div class="float-start ml-1">
                <a href="/pagos/notifyssms/all" class="btn btn-success">
                  <i class="fas fa-sms"></i>
                </a>
              </div>
              <div class="float-end">
                <a href="/pagos/renotifys/all" class="btn btn-warning">
                  <i class="fas fa-refresh"></i>
                </a>
              </div>
              <div class="float-end mr-1">
                <a href="/pagos/renotifyssms/all" class="btn btn-warning">
                  <i class="fas fa-refresh"></i> SMS
                </a>
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="col-12 col-md-12 col-lg-6">

    </div>
  </div>
@endsection
@section('styles')
  <style media="screen">
    hr{
      height:10px;
      background-color:#f6f6f6;
      border:0;
    }
    .text-muted{
      color:#BD773E !important;
    }
  </style>
@endsection
