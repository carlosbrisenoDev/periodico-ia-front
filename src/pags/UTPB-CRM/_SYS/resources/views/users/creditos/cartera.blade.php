@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  @php
    $ci = \App\cartera::whereRAW("md5(id)='".$_REQUEST["cid"]."'")->first();
    $foto = asset("images/nofoto.png");
    $cl = $c = $ci->cliente;

  @endphp
  <div class="row">
    <div class="col-md-4 col-xs">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-start">
              <h5 class="card-title">Cartera</h5>
              <h6 class="card-subtitle mb-2 text-muted">Cliente CRUOV-{{\carbon\carbon::parse($ci->created_at)->format("Y")}}-{{$ci->id}}</h6>
            </div>
            <div class="float-end">
              <a href="/creditos/creditos?cid={{md5($cl->id)}}">
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
            <div class="col-12">
              <label for="">Correo:</label>
              <div class="form-control disabled">
                {{$cl->correo}}
              </div>
            </div>
          <form class="col-12" action="/cartera/actualizar" method="post">
            <div class="col-12">
              <label for="">Concepto:</label>
              @if ($ci->concepto == "Crédito de estudios")
                  <div class="form-control disabled">
                    {{$ci->concepto}}
                  </div>
                @else
                  <input type="text" class="form-control" name="concepto" value="{{$ci->concepto}}">
              @endif
            </div>
            <div class="col-12">
              <br>
              <h6 class="card-subtitle mb-2 text-muted">
                Información de pago
              </h6>
            </div>
            <hr>
              <div class="row">
                <input type="hidden" name="cid" value="{{md5($ci->id)}}">
                <div class="col-12 col-md-12 col-lg-6">
                  @php
                    $val2 = ($ci->valor_titulo == null) ? 0 : $ci->valor_titulo;
                    $val = ($ci->valor_materia == null) ? 1520 : $ci->valor_materia;
                    $total = ($val * 40) + $val2;

                    $ci->interes = ($ci->interes == null) ? 0 : $ci->interes;
                    $ci->plazo = ($ci->plazo == null) ? 1 : $ci->plazo;
                  @endphp
                  <label for="">Total:</label>
                  <input type="text" class="form-control disabled" name="" value="{{$total}}">
                </div>
                <div class="col-12 col-md-12 col-lg-6">
                  <label for="">Valor materia:</label>
                  <input type="text" class="form-control" name="valor_materia" value="{{$val}}">
                </div>
                <div class="col-12 col-md-12 col-lg-6">
                  <label for="">Valor título:</label>
                  <input type="text" class="form-control" name="valor_titulo" value="{{$val2}}">
                </div>
                <div class="col-12 col-md-12 col-lg-6">
                  <label for="">Interes:</label>
                  <input type="text" class="form-control" name="interes" value="{{$ci->interes}}">
                </div>
                <div class="col-12 col-md-12 col-lg-6">
                  <label for="">Plazo:</label>
                  <input type="text" class="form-control" name="plazo" value="{{$ci->plazo}}">
                </div>
                <div class="col-12">
                  <br>
                  <h6 class="card-subtitle mb-2 text-muted">
                    Información de inicio
                  </h6>
                </div>
                <div class="col-12 col-md-12 col-lg-6">
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
                <div class="col-12 col-md-12 col-lg-6">
                  <label for="">Inicio: ({{$cl->isinscripcion->periodo}})</label>
                  <select class="form-control" name="fecha_estudio">
                    @foreach ($dim2Mes as $k => $m)
                      <option value="{{$m}}" {{($k==$mes_pago->format("M")) ? "selected" : ""}}>{{$m}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12 col-md-12 col-lg-6">
                  <label for="">Primer pago:</label>
                  <select class="form-control" name="fecha_inicio">
                    @foreach ($dim2Mes as $k => $m)
                      <option value="{{$m}}" {{($k==$mes_inicio->format("M")) ? "selected" : ""}}>{{$m}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6 col-xs">
                  <label for="">Año</label>
                  <div class="form-control disabled">
                    {{\Carbon\carbon::parse($ci->created_at)->format("Y")}}
                  </div>
                </div>
              </div>
              @if ($ci->tablapagos == null)
                <hr>
                <input type="submit" class="btn btn-success" value="Guardar y generar tabla de pagos">
              @endif
            </form>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Información adicional</h5>
          <hr>
          @if ($ci->cliente->baja != NULL)
            <div class="alert alert-warning">
              <i class="fa fa-warning"></i>
              El alumno tiene una baja temporal
            </div>
          @endif
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Notas</h5>
          <h6 class="card-subtitle mb-2 text-muted">&Uacute;ltima: </h6>
          <hr>
          <div class="row">
            <div class="col">
              @php
                $c = $ci->cliente;
                $n = \App\notas_cliente::where('cliente_id',$c->id)->orderBy("id","desc")->get();
                $beca = false;
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
                      {{$no->nota}}
                      @php
                        $beca = strstr($no->nota,"beca") ? true : false;
                      @endphp
                      <p align="right">
                        <small>
                          {{\Carbon\Carbon::parse($no->created_at)->diffForHumans()}}
                        </small>
                      </p>
                      <p>
                        @if (count($no->likes) > 0)
                          @php
                            $nombres = "";
                            $is = false;
                            foreach ($no->likes as $like)
                              {
                                $nombres .= $like->usuario->name.", ";
                                if($like->usuario_id == Auth::user()->id){
                                  $is = true;
                                }
                              }
                            $islike = ($is==true) ? "unlike" : "like";
                          @endphp
                              <a href="/cartera/{{$islike}}/dar?cid={{md5($no->id)}}" class="btn btn-link btn-sm"
                                 data-bs-toggle="tooltip" data-bs-placement="top" title="{{"$nombres ha reaccionado a este comentario"}}"
                                >
                                {{count($no->likes)}} <i class="far fa-hand-spock"></i>
                              </a>
                            @else
                              <a href="/cartera/like/dar?cid={{md5($no->id)}}" class="btn btn-link btn-sm"
                                 data-bs-toggle="tooltip" data-bs-placement="top" title="Reacciona"
                                >
                                0 <i class="far fa-hand-spock"></i>
                              </a>
                        @endif
                      </br>
                      </p>
                    </div>
                  </div>
                  <hr>
                @endforeach
              @endif
            </div>
          </div>
          <form class="" action="/cartera/nota" method="post">
          <input type="hidden" name="cliente_id" value="{{$c->id}}">
          <input type="hidden" name="cartera_id" value="{{$ci->id}}">
          <div class="row">
              <div class="col-12">
                <textarea name="comentario" class="form-control" placeholder="Agregar nota ..."></textarea>
                <hr>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar comentario">
                  <i class="fas fa-comment-alt"></i> Escribir comentario
                </button>
              </div>
          </div>
        </form>
        </div>
    </div>
  </div>
    <div class="col-xs-12 col-md-8">
      <div class="card">
        <div class="card-body">
          @if ($ci->tablapagos != null)
          <h5 class="card-title">Tabla de pagos
            @if ($ci->tablapagos->status != NULL)
                (PAUSADA)
            @endif
          </h5>
          <h6 class="card-subtitle mb-2 text-muted">Cliente CRUOV-{{\carbon\carbon::parse($ci->created_at)->format("Y")}}-{{$ci->id}}</h6>
            <hr>
              <div class="clearfix">
                <div class="float-start">
                  <a class="btn btn-info" href="/cartera/reformar/enviar?cid={{md5($ci->id)}}">
                    <i class="fas fa-refresh" style="color:white;"></i>
                  </a>
                  <a class="btn btn-info" href="/cartera/enviartabla/enviar?cid={{md5($ci->tablapagos->id)}}">
                    <i class="fas fa-envelope-open-text" style="color:white;"></i>
                    Enviar tabla de pagos al cliente.
                  </a>
                  <a class="btn btn-success" href="/cartera/csv/excel?cid={{md5($ci->tablapagos->id)}}">
                    <i class="fas fa-file-excel" style="color:white;"></i>
                    Exportar Excel
                  </a>
                  @if ($ci->tablapagos->status == NULL)
                      <a class="btn btn-warning text-light" href="/cartera/pausar/tabla?cid={{md5($ci->tablapagos->id)}}">
                        <i class="far fa-pause-circle"></i>
                        Pausar pagos
                      </a>
                    @else
                      <a class="btn btn-info text-light" href="/cartera/play/tabla?cid={{md5($ci->tablapagos->id)}}">
                        <i class="far fa-play-circle"></i>
                        Reanudar pagos
                      </a>
                  @endif
                  @if ($ci->tablapagos->derivada == NULL)
                    <a class="btn btn-danger" href="/cartera/derivar/once?cid={{md5($ci->tablapagos->id)}}">
                      <i class="far fa-money-bill-alt" style="color:white;"></i>
                      Derivar cr&eacute;dito
                    </a>
                    @else
                      <a class="btn btn-danger" href="/cartera/restructurar/once?cid={{md5($ci->tablapagos->id)}}">
                        <i class="far fa-money-bill-alt" style="color:white;"></i>
                        Crear nuevo crédito a partir de este
                      </a>
                  @endif
                  </div>
                  @if ($ci->tablapagos->derivada == NULL)
                    <div class="float-end">
                      <a class="btn btn-danger eliminartablab" href="#" data-bs-toggle="modal" data-bs-target="#eliminartabla">
                        <i class="fas fa-trash" style="color:white;"></i>
                      </a>
                    </div>
                  @endif
              </div>
            <hr>
                <table class="table table-xs table-striped ">
                  <thead class="table-dark">
                    <th>No. pago</th>
                    <th>Año</th>
                    <th>Mes</th>
                    <th>Acumulado</th>
                    <th>Pago</th>
                    <th>Capital</th>
                    <th>Interes</th>
                    <th></th>
                  </thead>
                  <tbody>
                    @php
                      $dim2Month = array_flip($dim2Mes);
                    @endphp
                    @foreach ($ci->tablapagos->pagos as $pago)
                      @if (\Carbon\carbon::parse($dim2Month[$pago->mes]." ".$pago->anio)->isPast(\Carbon\carbon::now()) || ($pago->pagado != null))
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
                          <td><a style="color:white;" href="/documentos/download/{{md5($pago->pagado)}}"><i class="fas fa-download"></i> Descargar</a></li></td>
                        </tr>
                        <tr style="height:100px;">
                          <td colspan="8">
                            @include('componentes.pago')
                          </td>
                        </tr>
                        @else
                          <tr>
                            <td>{{$pago->numero}}</td>
                            <td>{{$pago->anio}}</td>
                            <td>{{$pago->mes}}</td>
                            <td>{{$pago->acumulado}}</td>
                            <td>{{$pago->pago}}</td>
                            <td>{{$pago->capital}}</td>
                            <td>{{$pago->interes}}</td>
                            <td>
                              <a href="/cartera/adelantar/pago?cid={{md5($pago->id)}}">
                                Adelantar pago
                              </a>
                            </td>
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
      $(".extrapay").bind("click",function(){
        $("#extrapay").modal();
        $(".cid-e").val($(this).attr("cid"));
      });
      $(".eliminartablac").bind("click",function(){
        $("#eliminartabla").toggle();
        $(".modal-backdrop").remove();
        $("body").removeClass("modal-open");
      });
      $(".dmisspay").bind("click",function(){
        $("#extrapay").toggle();
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

  <div class="modal fade" id="extrapay" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Añadir pago extra</h5>
          <button type="button" class="btn-close dmisspay" data-bs-dismiss="modal" aria-label="Close">
            X
          </button>
        </div>
        <form class="" action="/cartera/extrapay" method="post">
          <div class="modal-body">
            <p>
              <label for="">Cargo extra del abono:</label>
              <input type="hidden" class="cid-e" name="cid" value="">
              <input type="text" class="form-control" name="cargo" value="0" placeholder="0.00">
            </p>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Añadir pago extra</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
