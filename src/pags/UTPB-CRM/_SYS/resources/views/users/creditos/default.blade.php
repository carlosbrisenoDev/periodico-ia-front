@extends('users.'.Auth::user()->level->alias.'.home')
@section('styles')
  <style media="screen">
  .background-perfil100
  {
    height:100px;
    width:auto;
    text-align: center;
    line-height: 100px;
    font-size: 12mm;
  }
  .background-perfil
  {
    height:200px;
    width:auto;
    text-align: center;
    line-height: 200px;
    font-size: 12mm;
  }
  .background-1{
    background-color:#F59606;
  }
  .background-2{
    background-color:#03677F;
  }
  .pulse {
    margin:100px;
    display: block;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgb( 39, 174, 96 );
    cursor: pointer;
    box-shadow: 0 0 0 rgba( 39, 174, 96 , 0.4);
    animation: pulse 2s infinite;
    position:absolute;
    top:-110px;
    left:-110px;
    }
    .pulse:hover {
    animation: none;
    }
    .bordi{
      border:solid #03677F 1px;
      border-left:solid #03677F 8px;
      color:#03677F;
    }
    .bordiyellow{
      border:solid #ebba11 1px;
      border-left:solid #ebba11 8px;
      color: #ebba11;
    }
    .bordigreen{
      border:solid green 1px;
      border-left:solid green 8px;
      color: green;
    }
    @-webkit-keyframes pulse {
    0% {
      -webkit-box-shadow: 0 0 0 0 rgba( 39, 174, 96 , 0.4);
    }
    70% {
        -webkit-box-shadow: 0 0 0 10px rgba( 39, 174, 96 , 0);
    }
    100% {
        -webkit-box-shadow: 0 0 0 0 rgba( 39, 174, 96 , 0);
    }
    }
    @keyframes pulse {
    0% {
      -moz-box-shadow: 0 0 0 0 rgba( 39, 174, 96 , 0.4);
      box-shadow: 0 0 0 0 rgba( 39, 174, 96 , 0.4);
    }
    70% {
        -moz-box-shadow: 0 0 0 10px rgba( 39, 174, 96 , 0);
        box-shadow: 0 0 0 10px rgba( 39, 174, 96 , 0);
    }
    100% {
        -moz-box-shadow: 0 0 0 0 rgba( 39, 174, 96 , 0);
        box-shadow: 0 0 0 0 rgba( 39, 174, 96 , 0);
    }
    }
  </style>
@endsection
@section('content')

  @include('componentes.inscritos2')
  <hr>
    <div class="alert fade alert-simple alert-warning alert-dismissible show" style="text-shadow: 0px 0px 0px #333;">
        <div class="clearfix">
          <div class="float-start">
            <i class="fa fa-times fas fa-exclamation-triangle"></i>
            <strong class="font__weight-semibold">¡Atención!</strong> Hay pagos con más de 60 días de retraso.
          </div>
          <div class="float-end">
            <a href="/creditos/noventa">
              Ver lista
            </a>
          </div>
        </div>
    </div>
  <hr>
  <div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
      <div class="card">
        <div class="card-body">
          @php
            $status = ["enviado","preaprobado","firmando"];
          @endphp
          @foreach ($status as $sa => $s)
          <h5 class="card-title">Cr&eacute;ditos</h5>
          <h6 class="card-subtitle mb-2 text-muted">Solicitantes {{$s}}s</h6>
          <hr>
          <div class="row">
          @php
            $a = 0;
            $n = 0;
          @endphp
          @foreach (\App\credito_info::where('status',$s)->orderBy("id","desc")->get() as $ci)
            @php
              $cl = $ci->cliente;
            @endphp
            @if ($cl->status >= 4)
              <div class="col-xs-12 col-sm-6 col-md-4">
                <div class="card">
                  @php
                    $foto = asset("images/perfil$n.jpeg");
                    if($n==1)
                      $n = 2;
                    else
                      $n = 1;
                  @endphp
                  <div class="background-perfil background-{{$n}} text-center text-light d-none d-sm-block">
                    {{substr($cl->nombre,0,1)}}
                  </div>
                  <div class="card-body">
                    @if ($s == "preaprobado" && $cl->ccredito()->status == 1)
                      <div class="pulse" data-bs-toggle="tooltip" data-bs-placement="top" title="El usuario ha efectuado cambios en su solicitud preaprobada">

                      </div>
                    @endif
                    <p class="card-text">
                          <center>
                            {{strtoupper($cl->nombre)}}
                            <br><small>{{$cl->credito}}% a {{$cl->plazo}}</small>
                          </center>
                          <hr class="line">
                          @include('componentes.iconos')
                    </p>
                    <p align="center">
                      <a href="/creditos/solicitud?cid={{md5($ci->id)}}" class="btn btn-info">
                        Ver solicitud
                      </a></br>
                      <small>
                        Actualizado {{\Carbon\carbon::parse($ci->updated_at)->diffForHumans(\Carbon\carbon::now())}}
                      </small>
                    </p>
                  </div>
                </div>
              </div>
              @if (++$a % 3 == 0)
                </div>
                <hr>
                <div class="row">
              @endif
            @endif
          @endforeach
          </div>
        @endforeach
        </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-12 col-sm-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Cartera</h5>
        <h6 class="card-subtitle mb-2 text-muted">Busqueda de clientes</h6>
        <hr>
        <div class="input-group">
          <input type="text" class="bus form-control" placeholder="Busqueda ..." aria-label="Busqueda ..." aria-describedby="button-addon2">
          <button class="btn buscar btn-outline-secondary" type="button" id="button-addon2">Buscar</button>
        </div>
        <div class="busqueda">

        </div>
      </div>
    </div>
    <br>
    <div class="card">
      <div class="card-body">
        <div class="clearfix">
          <div class="float-start">
            <h5 class="card-title">Créditos</h5>
            <h6 class="card-subtitle mb-2 text-muted">Video firmas</h6>
          </div>
          <div class="float-end">
            <a href="#">Ver todas las firmas</a>
          </div>
        </div>
        <hr>
        <table class="table table-striped">
          @foreach (\App\firma::where('status',0)->get() as $firma)
            <tr>
              <td>
                <i class="fas fa-photo-video text-danger"></i>
              </td>
              <td>
                <a href="/creditos/firmar?cid={{md5($firma->cartera->id)}}" class="text-danger">
                  {{$firma->cliente->isinscripcion->nombre_completo}}
                </a>
              </td>
              <td>{{$firma->cartera->concepto}}</td>
              <td>{{$firma->cartera->concepto}}</td>
              <td><small>Subido {{\Carbon\carbon::parse($firma->updated_at)->diffForHumans(\Carbon\carbon::now())}}</small></td>
            </tr>
          @endforeach
        </table>
      </div>
    </div>
    <br>
    <div class="card">
      <div class="card-body">
        <div class="clearfix">
          <div class="float-start">
            <h5 class="card-title">Cartera</h5>
            <h6 class="card-subtitle mb-2 text-muted">Pagos pendientes</h6>
          </div>
          <div class="float-end">
            <a href="/creditos/notify">
              Notificar pagos
            </a>
          </div>
        </div>
        <hr>
        @php
          $dats = \App\pagos::select(\DB::RAW('*,STR_TO_DATE(concat("1-",mes_en,"-",anio), "%d-%b-%Y") as fecha'))->havingRAW('fecha <= CURRENT_DATE')->limit(10);
        @endphp
        <div class="row">
          @foreach ($dats->get() as $pago)
            @if ($pago->pagado == null && $pago->tabla->cartera != null && $pago->tabla->cliente->baja == NULL)
              <div class="col-12 col-md-12 col-lg-6">
                <div class="alert alert-warning">
                  <div class="clearfix">
                    <div class="float-start">
                      <span style="color:black;">{{isset($pago->tabla->cliente->isinscripcion->nombre_completo) ? $pago->tabla->cliente->isinscripcion->nombre_completo : $pago->tabla->cliente->nombre}}</span>
                      <p>
                        <i>{{$pago->mes}} {{$pago->anio}}</i> - <span style="color:black;">{{$pago->pago}}</span>
                      </p>
                    </div>
                    <div class="float-end">
                      <i class="fas fa-money-check-alt"></i> Saldo pendiente:</br>
                      <a class="btn btn-link" href="/creditos/cartera?cid={{md5($pago->tabla->cartera->id)}}">
                        Ver tabla de pagos
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          @endforeach
        </div>
      </div>
    </div>
    <br>
    <div class="card">
      <div class="card-body">
        <div class="clearfix">
          <div class="float-start">
            <h5 class="card-title">Cartera</h5>
            <h6 class="card-subtitle mb-2 text-muted">Últimos pagos recibidos (4)</h6>
          </div>
          <div class="float-end">
            <a href="/creditos/pagos">
              Ver pagos
            </a>
          </div>
        </div>
        <hr>
        @php
          $dats = \App\pagos::select(\DB::RAW('*,STR_TO_DATE(concat("1-",mes_en,"-",anio), "%d-%b-%Y") as fecha'))->havingRAW('fecha <= CURRENT_DATE and pagado is not null')->orderBy("updated_at","desc")->limit(4);
        @endphp
        <div class="row">
          @foreach ($dats->get() as $pago)
            @if ($pago->pagado != null && $pago->tabla != null)
              <div class="col-12 col-md-12 col-lg-6">
                <div class="alert alert-success">
                  <div class="clearfix">
                    <div class="float-start">
                      <span style="color:black;">{{mb_strtoupper($pago->tabla->cliente->isinscripcion->nombre_completo)}}</span>
                      <p>
                        <i>{{$pago->mes}} {{$pago->anio}}</i> - <span style="color:black;">{{$pago->pago}}</span>  <small>{{\Carbon\carbon::parse($pago->updated_at)->diffForHumans(\Carbon\carbon::now())}}</small>
                      </p>
                    </div>
                    <div class="float-end">
                      <i class="fas fa-check-circle"></i> Pagado</br>
                      <a class="btn btn-link" href="/creditos/cartera?cid={{md5($pago->tabla->cartera->id)}}">
                        Ver tabla de pagos
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          @endforeach
        </div>
      </div>
    </div>
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
@section('scripts')
  <script type="text/javascript">
    $(function(){
      $(".fecha").bind("click",function(){
        $(".horamodal").modal();
      });
      $(".fb").bind("click",function(){
        $(".fbmodal").modal();
      });
      $(".formulario").bind("click",function(){
        $("#collapseExample").toggle();
      });
      $(".bus").bind("keyup",function(e){
        var k = e.keyCode;
        if(k == 13)
          $(".buscar").click();
      });
      $(".buscar").bind("click",function(){
        $(".busqueda").html("<hr><center><i class='fas fa-cog fa-spin'></i></center></hr>");
        $.post("/clientes/buscar?t="+$(".bus").val(),function(data){
          var t = $("<table>").addClass("table");
          var tr = $("<tr>");
          var td = $("<td>");
          var data = JSON.parse(data);
          $.each(data,function(i,e){
            tr = $("<tr>");
            td = $("<td>");
            td.html("<a href='/creditos/creditos?cid="+(e.cid)+"'>"+e.nombre+"</a>");
            tr.append(td);
            td = $("<td>");
            td.text(e.apat);
            tr.append(td);
            td = $("<td>");
            td.text(e.amat);
            tr.append(td);
            td = $("<td>");
            td.text(e.correo);
            tr.append(td);
            t.append(tr);
          });
          $(".busqueda").empty();
          $(".busqueda").append(t);
        });
      });
    });
  </script>
@endsection
