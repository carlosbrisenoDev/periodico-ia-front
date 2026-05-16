@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          @php
            $i = 1;
            $cobrar = isset($_REQUEST["cobrar"]) ? "YES" : NULL;
            $all = isset($_REQUEST["all"]) ? "YES" : NULL;
            $andmonth = isset($_REQUEST["and"]) ? \Carbon\carbon::parse("01-".$_REQUEST["and"]."-2021")->format("m") : NULL;
            $mes = isset($_REQUEST["mes"]) ? \Carbon\carbon::parse("01-".$_REQUEST["mes"]."-2021")->format("m"): \Carbon\carbon::now()->format("m");
            $anio = isset($_REQUEST["anio"]) ? $_REQUEST["anio"] : \Carbon\carbon::now()->format("Y");
            $dim2Mes = ["Jan" => "Enero","Feb" => "Febrero","Mar" => "Marzo","Apr" => "Abril","May" => "Mayo","Jun" => "Junio","Jul" => "Julio","Aug" => "Agosto","Sep" => "Septiembre","Nov" => "Noviembre","Oct" => "Octubre","Dec" => "Diciembre",];
            $meses_espanol = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre',);
            $total_pago = 0;
            $total_descuento = 0;
            $alumnos_costo = 0;
            $descuento = 700;
            if($andmonth != NULL){
              $pagos = \App\pagos::whereHas("documento",function($q) use($mes,$andmonth){
                $q->whereRAW("MONTH(created_at) <= '$mes' or MONTH(created_at) = '$andmonth'");
              })->where("anio",">=",$anio)->where("pagado","<>",NULL);
            } else {
              $pagos = \App\pagos::whereHas("documento",function($q) use($mes){
                $q->whereRAW("MONTH(created_at) <= '$mes'");
              })->where("anio",">=",$anio)->where("pagado","<>",NULL);
            }
            if($all != NULL){
              $pagos = \App\pagos::whereHas("documento")->where("pagado","<>",NULL);
            }
          @endphp
          <div class="clearfix">
            <div class="float-start">
              <h5 class="card-title">Cartera</h5>
              <h6 class="card-subtitle mb-2 text-muted">Pagos recibidos <b>{{$meses_espanol[$mes-1]}}</b></h6>
            </div>
            <div class="float-end pr-2">
              <div class="row">
                <div class="col pr-1">
                  <select class="form-control mes" name="mes" onchange="locateInTime()">
                    @foreach ($dim2Mes as $_month => $_mes)
                      <option value="{{$_month}}" {{$dim2Mes[$_month] == $meses_espanol[$mes-1] ? "selected" : ""}}>{{$_mes}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col p-0">
                  <select class="form-control anio" name="anio" onchange="locateInTime()">
                    @for ($i=2020; $i < 2030; $i++)
                      <option value="{{$i}}" {{$i == date("Y") ? "selected" : ""}}>{{$i}}</option>
                    @endfor
                  </select>
                </div>
                <div class="col">
                  @if ($andmonth == NULL)
                      <a class="btn btn-success" href="?mes={{$mes}}&anio={{$anio}}&and={{\Carbon\carbon::parse("01-".$mes."-2021")->subMonth(1)->format("M")}}{{$cobrar!=NULL ? "&cobrar=x" : ""}}">
                        <i class="far fa-calendar-alt"></i>
                      </a>
                    @else
                        <a class="btn btn-danger" href="?mes={{$mes}}&anio={{$anio}}{{$cobrar!=NULL ? "&cobrar=x" : ""}}">
                          <i class="far fa-calendar-times"></i>
                        </a>
                  @endif
                  @if ($cobrar == NULL)
                      <a href="?mes={{$mes}}&anio={{$anio}}&cobrar=x{{$andmonth!=NULL ? "&and=$andmonth" : ""}}" class="btn btn-info">
                        <i class="fas fa-coins"></i>
                      </a>
                    @else
                      <a href="?mes={{$mes}}&anio={{$anio}}{{$andmonth!=NULL ? "&and=$andmonth" : ""}}" class="btn btn-danger">
                        <i class="fas fa-coins"></i>
                      </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
          <hr>
          @php
            $h = 1;
          @endphp
            <table class="table table-sm table-striped table-hover pagos">
              <thead>
                <td></td>
                <td>#</td>
                <td>Folio</td>
                <td>No. Pago</td>
                <td>Concepto</td>
                <td>Nombre</td>
                <td>Pago recibido</td>
                <td>Comprobante</td>
                <td>Fecha de registro de pago</td>
                <td>Mes</td>
                <td><i class="fas fa-bolt"></i></td>
              </thead>
              <tbody>
                @php
                  $sort = $pagos->get()->sortBy('documento.created_at',SORT_REGULAR,false);
                  if($cobrar != NULL){
                    $sort = $pagos->get()->sortBy('documento.created_at',SORT_REGULAR,false)->reverse();
                  }
                @endphp
                @foreach ($sort as $pago)
                    @php
                      if ($pago->status != "beca") {
                        if($pago->numero <= 20 && $pago->extra == NULL && $pago->tabla->cartera->concepto == 'Crédito de estudios')
                          {
                            if($pago->split == NULL){
                              $total_pago += str_replace("$","",str_replace(",","",$pago->pago));
                              $total_descuento += $descuento;
                              $alumnos_costo++;
                            }
                          }
                      }
                      if($cobrar != NULL && $pago->split != NULL){
                        break;
                      }
                    @endphp
                    <tr>
                      <td>
                        <div class="text-dark">
                          {{$h++}}
                        </div>
                      </td>
                      <td>
                        @if ($pago->split != NULL)
                          <a href="/pagos/unsplit/do?cid={{md5($pago->id)}}&mes={{$mes}}&anio={{$anio}}">
                            <i class="fas fa-minus-circle"></i>
                          </a>
                        @else
                          <a href="/pagos/split/do?cid={{md5($pago->id)}}&mes={{$mes}}&anio={{$anio}}">
                            <i class="fas fa-plus-circle"></i>
                          </a>
                        @endif
                      </td>
                      <td>{{$pago->id}}</td>
                      <td class="text-center" style="color:{{$pago->status == "beca" ? "blue": (($pago->numero <= 20 && $pago->tabla->cartera->concepto == 'Crédito de estudios')  ? ($pago->extra == NULL ? "red" : "green") : "black")}};">
                        @if ($pago->status == "beca")
                          {{$pago->numero}} (BECA)
                          @else
                            {{$pago->numero}} ({{($pago->numero <= 20  && $pago->tabla->cartera->concepto == 'Crédito de estudios') ? ($pago->extra == NULL ? "- $$descuento" : "Pago extra") : (($pago->tabla->cartera->concepto != 'Crédito de estudios') ? $pago->tabla->cartera->concepto :"$0")}})
                        @endif
                        @if ($pago->tabla->cartera->concepto == "Restructuración crédito de estudios")
                          @php
                          $url = "https://plataformaunisant.mx/unisant/apiEstudy/externos/alumno/consulta.php?token=4ba07dd78a8a6bc15844adebebffc342&matricula=".$pago->tabla->cliente->matricula;
                          $ch = curl_init($url);

                          curl_setopt($ch,CURLOPT_FOLLOWLOCATION,FALSE);
                          curl_setopt($ch, CURLOPT_HEADER, 0);
                          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                          curl_setopt($ch, CURLOPT_TIMEOUT, 6); //timeout in seconds


                          $res = curl_exec($ch);
                          curl_close($ch);
                          $data = json_decode($res,true);

                          if(isset($data["response"])){
                            $al = (object) $data["response"];
                          }
                          @endphp
                          <br>
                          <small>{{count($al->materias_cursadas ?? [])}} Materias</small>
                        @endif
                      </td>
                      <td>{{mb_strtoupper($pago->tabla->cartera->concepto)}}</td>
                      <td>{{mb_strtoupper($pago->tabla->cliente->isinscripcion->nombre_completo)}}</td>
                      <td>{{$pago->pago}}</td>
                      <td>
                        @if ($pago->status == 9 || $pago->status == "beca")
                            Becado
                          @else
                            <a target="_blank" href="{{config('app.url')}}/documentos/watchar/{{md5($pago->pagado)}}">
                              Ver comprobante
                            </a>
                        @endif
                      </td>
                      <td>
                        {{$pago->updated_at}}
                      </td>
                      <td>{{$pago->mes}}/{{$pago->anio}}</td>
                      <td>
                        @if ($pago->recibo == NULL)
                          <a class="btn btn-link" href="/pagos/recibo/enviar?cid={{md5($pago->id)}}">
                              <i class="fas fa-file-invoice"></i><i class="fas fa-angle-double-right"></i>
                          </a>
                          @else
                            <a class="btn btn-link" href="/pagos/recibo/enviar?cid={{md5($pago->id)}}">
                              <i class="fas fa-file-invoice"></i><i class="fas fa-check-circle text-success"></i>
                            </a>
                        @endif
                      </td>
                    </tr>
                    @if ($pago->split != NULL)
                      <tr>
                        <td colspan="10" class="bg-info">
                          <div class="text-center ">
                            Corte realizado hace {{\Carbon\carbon::parse($pago->split)->diffForHumans()}}
                          </div>
                        </td>
                      </tr>
                    @endif
                @endforeach
              </tbody>
            </table>
            <br>
            <table class="table table-striped">
              <tr>
                <td>Total recibido:</td>
                <td class="text-center">${{number_format($total_pago,2,".",",")}}</td>
              </tr>
              <tr>
                <td>Descuento por costos:</td>
                <td class="text-center">${{number_format($total_descuento,2,".",",")}} ($700 x {{$alumnos_costo}} alumnos)</td>
              </tr>
              <tr>
                <td>Total:</td>
                @php
                  $t_1 = $total_pago-$total_descuento;
                @endphp
                <td class="text-center">${{number_format($t_1,2,".",",")}}</td>
              </tr>
              <tr>
                <td>Recuperación:</td>
                <td class="text-center">${{number_format($t_1*.3,2,".",",")}} (30%)</td>
              </tr>
              <tr>
                <td>Rectoria:</td>
                <td class="text-center">${{number_format($t_1*.7,2,".",",")}} (70%)</td>
              </tr>
            </table>
            <br>
            <a target='_blank' href="/cartera/pagoscsv/download?mes={{$mes}}&anio={{$anio}}{{$andmonth!=NULL ? "&and=$andmonth" : ""}}{{$cobrar!=NULL ? "&cobrar=x" : ""}}" class="btn btn-success">
              <i class="fas fa-download"></i>
            </a>
            <a href="/pagos/recibos/send?mes={{$mes}}&anio={{$anio}}{{$andmonth!=NULL ? "&and=$andmonth" : ""}}" class="btn btn-success">
              <i class="fas fa-file-invoice"></i><i class="fas fa-angle-double-right"></i>
               Enviar todos los recibos
            </a>
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
@section('scripts')
  <script type="text/javascript">
    $(function(){
      $(".pagos").DataTable(lang);
      $(".bmaterias").bind("click",function(){
        $(".fmaterias").modal();
      });
    });
    function locateInTime(){
      location.href = "?mes="+$(".mes").val()+"&anio="+$(".anio").val();
    }
  </script>
@endsection
