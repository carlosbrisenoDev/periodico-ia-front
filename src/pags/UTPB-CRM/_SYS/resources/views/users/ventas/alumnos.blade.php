@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          @php
            $i = 1;
            $mes = isset($_REQUEST["mes"]) ? \Carbon\carbon::parse("01-".$_REQUEST["mes"]."-2021")->format("m"): \Carbon\carbon::now()->format("m");
            $anio = isset($_REQUEST["anio"]) ? $_REQUEST["anio"] : \Carbon\carbon::now()->format("Y");
            $i = 1;
            $semana = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado"];
            $meses = ["","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
          @endphp
          <div class="clearfix">
            <div class="float-left">
              <h5 class="card-title">Alumnos</h5>
              <h6 class="card-subtitle mb-2 text-muted">{{$meses[\Carbon\carbon::parse("2021-".$mes."-1")->format("m")*1]}}</h6>
            </div>
            @include('componentes.month_nav')
          </div>
          <hr>
            <table class="table table-sm table-striped table-hover">
              <tbody>
                @php
                  $dp = \App\level::select("id")->whereIn("name",["Ventas","Control escolar"]);
                  $agentes = \App\User::whereIn("level_id",$dp->get());
                @endphp
                @foreach ($agentes->get() as $agente)
                  @php
                    $alumnos = \App\cliente::whereHas("comprobante_pago",function($q) use($mes){
                      $q->whereRAW("MONTH(created_at) = '$mes'");
                    })->where("agente_id",$agente->id)->get();
                  @endphp
                @if (count($alumnos)>0)
                </table>
                <br>
                <div class="" role="alert">
                  {{$agente->name}} ({{count($alumnos)}})
                </div>
                <br>
                <table class="table table-striped">
                  <tr class="bg-dark text-light">
                    <td>#</td>
                    <td>Código</td>
                    <td>Nombre</td>
                    <td>Paterno</td>
                    <td>Materno</td>
                    <td>T&eacute;lefono</td>
                    <td>Correo</td>
                    <td>Fecha pago</td>
                  </tr>
                @endif
                  @foreach ($alumnos as $c)
                    <tr>
                      <td>
                        {{$i++}}
                      </td>
                      <td style="text-align:center;">
                        <a href="/ventas/cliente?cid={{md5($c->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para ver">
                          UOV-{{\Carbon\Carbon::parse($c->created_at)->format("Y")}}-{{$c->id}}
                        </a>
                      </td>
                      <td>{{$c->nombre}}</td>
                      <td>{{$c->apat}}</td>
                      <td>{{$c->amat}}</td>
                      <td>{{$c->telefono}}</td>
                      <td>
                        <a href="/bandeja/nuevo/enviar?a={{$c->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                          {{$c->correo}}
                        </a>
                      </td>
                      <td>
                        {{\Carbon\carbon::parse($c->get_comprobante->created_at)->format("Y-M-d")}}
                      </td>
                    </tr>
                  @endforeach
                @endforeach
              </tbody>
            </table>
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
