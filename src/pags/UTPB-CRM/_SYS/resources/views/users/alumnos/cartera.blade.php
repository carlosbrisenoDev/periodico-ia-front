@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  @php
    $ci = \App\cartera::whereRAW("md5(id)='".$_REQUEST["cid"]."'")->first();
    $foto = asset("images/nofoto.png");
    $cl = $c = $ci->cliente;

  @endphp
  <div class="row">
    <div class="col-12 col-md-6 col-lg-4 col-xl-4">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-left">
              <h5 class="card-title">Cartera</h5>
              <h6 class="card-subtitle mb-2 text-muted">Cliente CRUOV-{{\carbon\carbon::parse($ci->created_at)->format("Y")}}-{{$ci->id}}</h6>
            </div>
            <div class="float-right">
              <a href="/">
                <i class="fas fa-arrow-circle-left"></i>
              </a>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-12">
              <label for="">Nombre:</label>
              <div class="form-control disabled">
                {{$cl->isinscripcion->nombre_completo}}
              </div>
            </div>
            <hr>
              <div class="row" style="padding:15px;">
                  @php
                    $val2 = ($ci->valor_titulo == null) ? 18800 : $ci->valor_titulo;
                    $val = ($ci->valor_materia == null) ? 1520 : $ci->valor_materia;
                    $total = ($val * 40) + $val2;

                    $ci->interes = ($ci->interes == null) ? 0 : $ci->interes;
                    $ci->plazo = ($ci->plazo == null) ? 1 : $ci->plazo;
                  @endphp
                <hr>
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                  <label for="">Pago mensual:</label>
                  @php
                    $interes = ($total * $ci->interes/100 * $ci->plazo/12);
                    $interes_mes = $interes/$ci->plazo;
                    $capital = ($total)/$ci->plazo;
                    $pago_mes = $capital+$interes_mes;
                    $pago_mes = round($pago_mes,2);
                  @endphp
                  <input type="text" class="form-control disabled" name="" value="{{$pago_mes}}">
                </div>
                @php
                $meses_ingles = array(
                  "January",
                  "February",
                  "March",
                  "April",
                  "May",
                  "June",
                  "July",
                  "August",
                  "September",
                  "October",
                  "November",
                  "December"
                  );

                  $meses_espanol = array(
                  'Enero',
                  'Febrero',
                  'Marzo',
                  'Abril',
                  'Mayo',
                  'Junio',
                  'Julio',
                  'Agosto',
                  'Septiembre',
                  'Octubre',
                  'Noviembre',
                  'Diciembre',
                  );

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

                  $mes_pago = \Carbon\carbon::parse($ci->fecha_estudio)->addMonth(0);
                  $mes_inicio = \Carbon\carbon::parse($ci->fecha_inicio)->addMonth(0);
                @endphp
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                  <label for="">Inicio:</label>
                  <div class="form-control disabled">
                    {{$mes_pago->format("M")}}
                  </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                  <label for="">Primer pago:</label>
                  <div class="form-control disabled">
                    {{$mes_inicio->format("M")}}
                  </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                  <label for="">Año</label>
                  <div class="form-control disabled">
                    {{\Carbon\carbon::parse($ci->created_at)->format("Y")}}
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"></h5>
          <h6 class="card-subtitle mb-2 text-muted"></h6>
          <hr>
        </div>
    </div>
  </div>
    <div class="col-8">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Tabla de pagos</h5>
          <h6 class="card-subtitle mb-2 text-muted">Cliente CRUOV-{{\carbon\carbon::parse($ci->created_at)->format("Y")}}-{{$ci->id}}</h6>
          @if ($ci->tablapagos != null)
            <hr>
                <table class="table table-striped ">
                  <thead class="table-dark">
                    <th>No. pago</th>
                    <th>Año</th>
                    <th>Mes</th>
                    <th>Acumulado</th>
                    <th>Pago</th>
                    <th>Capital</th>
                    <th>Interes</th>
                  </thead>
                  <tbody>
                    @php
                      $dim2Month = array_flip($dim2Mes);
                    @endphp
                    @foreach ($ci->tablapagos->pagos as $pago)
                      @if (\Carbon\carbon::parse($dim2Month[$pago->mes]." ".$pago->anio)->isPast(\Carbon\carbon::now()) || $pago->pagado != null)
                        @php
                          $status = $pago->status == 1 ? "bg-success" : ($pago->pagado != null ? "bg-warning" : "bg-danger");
                        @endphp
                        <tr class='{{$status}}'>
                          <td>{{$pago->numero}}</td>
                          <td>{{$pago->anio}}</td>
                          <td>1 {{$pago->mes}}</td>
                          <td>{{$pago->acumulado}}</td>
                          <td>{{$pago->pago}}</td>
                          <td>{{$pago->capital}}</td>
                          <td>{{$pago->interes}}</td>
                        </tr>
                        @else
                          <tr>
                            <td>{{$pago->numero}}</td>
                            <td>{{$pago->anio}}</td>
                            <td>1 {{$pago->mes}}</td>
                            <td>{{$pago->acumulado}}</td>
                            <td>{{$pago->pago}}</td>
                            <td>{{$pago->capital}}</td>
                            <td>{{$pago->interes}}</td>
                          </tr>
                      @endif
                    @endforeach
                  </tbody>
                </table>
            @else
              Aún no ha generado su tabla de pagos.
          @endif
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
    .disabled{
      background-color: #f4f4f4;
      cursor:not-allowed;
    }
  </style>
@endsection
@section('scripts')
  <script type="text/javascript">
    $(function(){
      $(".eliminartablab").bind("click",function(){
        $("#eliminartabla").modal();
      });
      $(".eliminartablac").bind("click",function(){
        $("#eliminartabla").toggle();
        $(".modal-backdrop").remove();
        $("body").removeClass("modal-open");
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
            td.html("<a href='/ventas/cliente?cid="+(e.cid)+"'>"+e.nombre+"</a>");
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
@section('modal')
  <div class="modal fade" id="eliminartabla" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pregunta</h5>
          <button type="button" class="btn-close eliminartablac" data-bs-dismiss="modal" aria-label="Close">
            X
          </button>
        </div>
        <div class="modal-body">
          <p>¿Realmente deseas eliminar la tabla de pagos?</br><i>No podr&aacute;s recuperar esta informaci&oacute;n.</i></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary eliminartablac">Cancelar</button>
          @if ($ci->tablapagos != null)
            <a href="/cartera/eliminartabla/delete?cid={{md5($ci->tablapagos->id)}}" class="btn btn-danger">Eliminar</a>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
