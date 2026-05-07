@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        @php
          $cl = \App\cliente::whereRAW("md5(id)='".$_REQUEST["cid"]."'")->first();
          $cl->cinfo();
        @endphp
        @if ($cl->status >= 4 || count($cl->carteras) > 0)
          <div class="card-body">
            <div class="clearfix">
              <div class="float-start">
                <h5 class="card-title">{{$cl->isinscripcion->nombre_completo}}</h5>
                <h6 class="card-subtitle mb-2 text-muted">Cr&eacute;ditos</h6>
              </div>
              <div class="float-end">
                @if ($cl->cinfo()->status == "cartera")
                  <a href="/cartera/nuevo/credito?cid={{md5($cl->id)}}" class="btn btn-info">
                    <i class="fas fa-plus"></i> Nuevo
                  </a>
                @endif
                <a href="/creditos/solicitud?cid={{md5($cl->credito_info->id)}}" class="btn btn-success">
                  <i class="fas fa-user"></i> Ver perfil de crédito
                </a>
              </div>
            </div>
            <hr>
              @if (count($cl->carteras) > 0)
                <table class="table table-striped">
                  <thead class="table-dark">
                    <th>#</th>
                    <th>Concepto</th>
                    <th>Inicio de estudios</th>
                    <th>Inicio de pago</th>
                    <th>Monto</th>
                    <th>Plazos</th>
                    <th>Interes</th>
                    <th>Tabla</th>
                    <th></th>
                  </thead>
                  <tbody>
                    @php
                      $l=1;
                    @endphp
                    @foreach ($cl->carteras as $c)
                      <tr>
                        <td style="text-align:center;">
                          {{$l++}}
                        </td>
                        <td>
                          <a href="/creditos/cartera?cid={{md5($c->id)}}">
                            {{$c->concepto}}
                          </a>
                        </td>
                        <td>{{$c->fecha_estudio}}</td>
                        <td>
                          {{$c->fecha_inicio}}
                        </td>
                        <td>{{($c->total == null) ? "Sin definir" : $c->total}}</td>
                        <td>{{$c->plazo}}</td>
                        <td>{{$c->interes}}%</td>
                        <td>{{($c->tablapagos != null)? "Generada":"Sin generar"}}</td>
                        <td>
                        @if ($c->tablapagos == NULL)
                          <a href="/cartera/eliminarcartera/credito?cid={{md5($c->id)}}">
                            <i class="fa fa-trash"></i>
                          </a>
                        @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
                @else
                  @if ($cl->credito_info->status == "cartera")
                    <center>
                      <img src="{{asset("images/what.png")}}" class="img-fluid" style="width:200px;">
                      <h3>¡Ups!, parece que no hay cr&eacute;ditos</h3><br>
                      <a href="/cartera/nuevo/credito?cid={{md5($cl->id)}}" class="btn btn-info">
                        <i class="fas fa-plus"></i> Crear nuevo cr&eacute;dito
                      </a>
                    </center>
                    @else
                      <center>
                        <img src="{{asset("images/what.png")}}" class="img-fluid" style="width:200px;">
                        <h3>¡Ups!, este cliente aún no esta registrado en cartera.</h3>
                        <a href="/creditos/solicitud?cid={{md5($cl->credito_info->id)}}" class="btn btn-success">
                          <i class="fas fa-user"></i> Ver perfil de crédito
                        </a>
                      </center>
                  @endif
              @endif
            </div>
            @else
              <div class="card-body">
                <div class="text-center">
                  <img src="{{asset("images/what.png")}}" class="img-fluid" style="width:200px;">
                  <h4>Este alumno aún no esta inscrito</h4>
                  <p>
                    Es posible que el alumno no haya concluido su inscripción pero completo los trámites de crédito.
                  </p>
                </div>
                  <div class="row">
                    <div class="col">
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                      @php
                        $c = $cl;
                        $n = \App\notas_cliente::where('cliente_id',$c->id)->orderBy("id","desc")->get();
                      @endphp
                      @if (count($n) > 0)
                        @foreach ($n as $no)
                          <div class="card">
                            <div class="card-body">
                                <small>
                                  <div class="row">
                                    <div class="col">
                                      {{$no->usuario->name}}
                                    </div>
                                    <div class="col text-right">
                                      <a class="right" target="_blank"  href="/bandeja/nuevo/enviar?a={{$no->usuario->email}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                                        <i class="far fa-envelope"></i>
                                      </a>
                                    </div>
                                  </div>
                                </small>
                              <p align="text-right">
                                {{$no->nota}}
                              </p>
                              <p align="right">
                                <small>
                                  {{\Carbon\Carbon::parse($no->created_at)->diffForHumans()}}
                                </small>
                              </p>
                            </div>
                          </div>
                          <hr>
                        @endforeach
                      @endif
                    </div>
                    <div class="col">
                    </div>
                  </div>
                  <div class="text-center">
                    <a href="/creditos/listar" class="btn btn-link">Regresar</a>
                  </div>
                </div>
            @endif
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
