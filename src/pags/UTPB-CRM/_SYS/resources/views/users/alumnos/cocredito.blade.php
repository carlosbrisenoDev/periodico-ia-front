@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="row">
            <div class="col">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">UNISANT ORIZABA</h5>
                  @php
                    $c = $empleado = Auth::user()->empleado;
                  @endphp
                  <h6 class="card-subtitle mb-2 text-muted">Solicitud (CRUOV-{{date("Y")}}-{{$c->id}})</h6>
                  <hr>
                    @if ($empleado->status <= 4)
                        <div style="padding:20px;background-color:white;">
                          <p align="justify">
                            Somos una sociedad financiera que otorgamos créditos para estudiar diversas licenciaturas en línea de la Universidad Santander.
                          </p>
                          <hr>
                          <h6 class="card-subtitle mb-2 text-muted">
                            DESCRIPCIÓN DEL PRODUCTO
                          </h6>
                          <hr>
                          <p align="center">
                            <ul>
                              <li>Préstamo simple con garantía siendo el destino específico para pagar los costos de estudios.</li>
                              <li>Monto: Variable según necesidades y capacidad de pago.</li>
                              <li>Plazos: Desde 2 hasta 4 años.</li>
                              <li>Pagos quincenales o mensuales.</li>
                              <li>El pago incluye capital, interés e IVA.</li>
                              <li>El cálculo de intereses es sobre saldos insolutos.</li>
                              <li>El plazo es fijo al igual que la tasa de interés es fija y será del {{$empleado->credito}}% anual sobre saldos insolutos.</li>
                              <li>La tasa se mantiene fija durante la vigencia del crédito.</li>
                              <li>La tasa moratoria será del 50 por ciento de la tasa normal, calculando la mora a partir de la falta de pago.</li>
                              <li>CAT del 12.7%</li>
                            </ul>
                          </p>
                          <hr>
                          <h6 class="card-subtitle mb-2 text-muted">DOCUMENTOS A SOLICITAR</h6>
                          <hr>
                          <p>
                            <ul>
                              <li>Identificación oficial vigente (INE, pasaporte, cédula profesional, con máximo 10 años de antigüedad.</li>
                              <li>Registro Federal de Contribuyentes RFC o Cédula Fiscal.</li>
                              <li>Si el solicitante es extranjero es necesario presentar:
                                <ul>
                                  <li>Tarjeta de residente temporal o permanente.</li>
                                  <li>Forma migratoria FM2 o FM3.</li>
                                </ul>
                              </li>
                              <li>Comprobante de domicilio no mayor a tres meses de antigüedad (teléfono, CFE, agua o predial).</li>
                            </ul>
                          </p>
                          <hr>
                          <h6 class="card-subtitle mb-2 text-muted">REQUISITOS</h6>
                          <hr>
                          <b>PERSONAS ASALARIADAS</b>
                          <p>
                            <ol>
                              <li>Ser mexicano con residencia permanente en el país, mayor de 18 años y menor de 70 años.</li>
                              <li>Contar con un ingreso mensual comprobable (nómina, estados de cuenta donde depositen la nómina).</li>
                              <li>Antigüedad laboral mínima de 6 meses.</li>
                              <li>Ser mexicano con residencia permanente en el país, mayor de 18 años y menor de 70 años.</li>
                              <li>Contar con un ingreso mensual comprobable (nómina, estados de cuenta donde depositen la nómina).</li>
                              <li>Antigüedad laboral mínima de 6 meses.</li>
                            </ol>
                          </p>
                          <b>PERSONAS NO ASALARIADAS</b>
                          <p>
                            <ol>
                              <li>LOS MISMOS REQUISITOS Y ADICIONALMENTE</li>
                              <li>Declaración de impuestos anual (últimos 2 ejercicios) y las últimas 3 declaraciones mensuales de impuestos IVA e ISR.</li>
                            </ol>
                          </p>
                        </div>
                        @if (Auth::user()->cliente->ccredito()->status == 1)
                          <div class="alert alert-danger">
                            <p align="justify">
                              <h4 style="color:#DC143C;">Solicitud en proceso de revisión</h4>
                              Su solicitud esta en proceso de ser revisada por el departamenteo de créditos, pronto recibirá respuesta en su correo electrónico <a href="#">{{Auth::user()->empleado->correo}}</a>.
                            </p>
                          </div>
                        @endif

                </div>
                </div>
                @else
                    El estado de tu solicitud es <b>Inscrito</b>
                @endif
            </div>
            <div class="col">
                      <div class="card">
                        <div class="card-body">
                          <h5 class="card-title">Crédito</h5>
                          <h6 class="card-subtitle mb-2 text-muted">Llenado de solicitud (CRUOV-{{date("Y")}}-{{$c->id}})</h6>
                          <hr>
                            <div class="alert alert-info">
                              <i class="fa fa-info"></i> Ingresa toda la información necesaria para aprobar tu crédito, datos, documentos; al terminar presiona el botón "Enviar solicitud".
                            </div>
                          <hr>
                          @php
                            $cr = Auth::user()->cliente->cinfo();
                            $co = Auth::user()->cliente->ccredito();
                          @endphp
                          @if (Auth::user()->cliente->isinscripcion != null && $empleado->status != 2)
                            <form class="" action="/credito/actualizarc" method="post">
                              <h6>Información general</h6>
                              <hr>
                              <div class="row">
                                <div class="col-12 col-md-12 col-lg-6">
                                  <label for="formGroupExampleInput" class="form-label">Nombre</label>
                                  <input type="text" name="nombre" class="form-control as" w="/credito/seto" placeholder="Nombre completo" value="{{$co->nombre}}">
                                </div>
                                <div class="col-12 col-md-12 col-lg-6">
                                  <label for="">Identificación <i class="fas fa-info-circle"  data-bs-toggle="tooltip" data-bs-placement="top" data-html="true" title="<img class='img-fluid' src='{{asset("images/elector.jpg")}}'>"></i></label>
                                  <input type="text" w="/credito/seto" class="as form-control" name="identificacion" value="{{$co->identificacion}}" placeholder="VLRSJR89090330Z655">
                                </div>
                                <div class="col-12 col-md-12 col-lg-6">
                                  <label for="">Lugar donde reside</label>
                                  <input type="text" w="/credito/seto" class="as form-control" name="reside" placeholder="Veracruz" value="{{$co->reside}}">
                                </div>
                                <div class="col-12 col-md-12 col-lg-6">
                                  <label for="">Teléfono</label>
                                  <input type="text" w="/credito/seto" class="as form-control" name="telefono" placeholder="555 555 5555" value="{{$co->telefono}}">
                                </div>
                                <div class="col-12 col-md-12 col-lg-6">
                                  <label for="">Celular</label>
                                  <input type="text" w="/credito/seto" class="as form-control" name="celular" placeholder="555 555 5555" value="{{$co->celular}}">
                                </div>
                                <div class="col-12 col-md-12 col-lg-6">
                                  <label for="">Correo electrónico</label>
                                  <input type="text" w="/credito/seto" class="as form-control" name="correo" placeholder="alguien@ejemplo.com" value="{{$co->correo}}">
                                </div>
                                <div class="col-12">
                                  <label for="">Dirección</label>
                                  <input type="text" w="/credito/seto" class="as form-control" name="direccion" placeholder="Avenida principal #1903" value="{{$co->direccion}}">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                                  <label for="">Col.</label>
                                  <input type="text" w="/credito/seto" class="as form-control" name="col" placeholder="Centro" value="{{$co->col}}">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                                  <label for="">Municipio</label>
                                  <input type="text" w="/credito/seto" class="as form-control" name="municipio" placeholder="Orizaba, Ver." value="{{$co->municipio}}">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                                  <label for="">Código postal</label>
                                  <input type="text" w="/credito/seto" class="as form-control" name="cp" placeholder="94500" value="{{$co->cp}}">
                                </div>
                                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                                  <label for="">Ingreso mensual básico</label>
                                  <input type="text" w="/credito/seto" class="as form-control" placeholder="$0.00" name="ingreso" value="{{$co->ingreso}}">
                                </div>
                                <div class="col-8">
                                  <label for="">Otros ingresos:</label>
                                  <input type="text" w="/credito/seto" class="as form-control" placeholder="$0.00" name="otroingreso" value="{{$co->otroingreso}}">
                                </div>
                                <input type="hidden" class="cid" value="{{md5($co->id)}}">
                              </div>
                              <hr>
                              <div class="">
                                <h6>Deudor solidario</h6>
                                <hr>
                                <div class="row">
                                  <div class="col-12 col-md-12 col-lg-6">
                                    <label for="formGroupExampleInput" class="form-label">Nombre</label>
                                    <input type="text" w="/credito/seto" class="as form-control" placeholder="Nombre completo" name="nombre_s" value="{{$co->nombre_s}}">
                                  </div>
                                  <div class="col-12 col-md-12 col-lg-6">
                                    <label for="">Identificación <i class="fas fa-info-circle"  data-bs-toggle="tooltip" data-bs-placement="top" data-html="true" title="<img class='img-fluid' src='{{asset("images/elector.jpg")}}'>"></i></label>
                                    <input type="text" w="/credito/seto" class="as form-control" name="identificacion_s" placeholder="VLRSJR89090330Z655" value="{{$co->identificacion_s}}">
                                  </div>
                                  <div class="col-12 col-md-12 col-lg-6">
                                    <label for="">Lugar donde reside</label>
                                    <input type="text" w="/credito/seto" class="as form-control" name="reside_s" placeholder="Veracruz" value="{{$co->reside_s}}">
                                  </div>
                                  <div class="col-12 col-md-12 col-lg-6">
                                    <label for="">Teléfono</label>
                                    <input type="text" w="/credito/seto" class="as form-control" name="telefono_s" placeholder="555 555 5555" value="{{$co->telefono_s}}">
                                  </div>
                                  <div class="col-12 col-md-12 col-lg-6">
                                    <label for="">Celular</label>
                                    <input type="text" w="/credito/seto" class="as form-control" name="celular_s" placeholder="555 555 5555" value="{{$co->celular_s}}">
                                  </div>
                                  <div class="col-12 col-md-12 col-lg-6">
                                    <label for="">Correo electrónico</label>
                                    <input type="text" w="/credito/seto" class="as form-control" name="correo_s" placeholder="alguien@ejemplo.com" value="{{$co->correo_s}}">
                                  </div>
                                  <div class="col-12">
                                    <label for="">Dirección</label>
                                    <input type="text" w="/credito/seto" class="as form-control" name="direccion_s" placeholder="Avenida principal #1903" value="{{$co->direccion_s}}">
                                  </div>
                                  <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label for="">Col.</label>
                                    <input type="text" w="/credito/seto" class="as form-control" name="col_s" placeholder="Centro" value="{{$co->col_s}}">
                                  </div>
                                  <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label for="">Municipio</label>
                                    <input type="text" w="/credito/seto" class="as form-control" name="municipio_s" placeholder="Orizaba, Ver." value="{{$co->municipio_s}}">
                                  </div>
                                  <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label for="">Código postal</label>
                                    <input type="text" w="/credito/seto" class="as form-control" name="cp_s" placeholder="94500" value="{{$co->cp_s}}">
                                  </div>
                                  <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label for="">Ingreso mensual básico</label>
                                    <input type="text" w="/credito/seto" class="as form-control" placeholder="$0.00" name="ingreso_s" value="{{$co->ingreso_s}}">
                                  </div>
                                  <div class="col-8">
                                    <label for="">Otros ingresos:</label>
                                    <input type="text" w="/credito/seto" class="as form-control" placeholder="$0.00" name="otroingreso_s" value="{{$co->otroingreso_s}}">
                                  </div>
                                </div>
                              </div>
                              <div class="solidario">

                              </div>
                            </form>
                            <hr>
                              <h6>Documentos adicionales:</h6>
                            <hr>
                            <div class="alert alert-warning">
                              <i class="fa fa-info"></i> No subas documentos que hayas subido previamente en tu inscripción.
                            </div>
                            <hr>
                            <table class="table table-striped">
                                @if (count($c->documentosc) > 0)
                                  @foreach ($c->documentosc as $documento)
                                    <tr>
                                      <td style="width:20px;">
                                        <div class="btn btn-link">
                                          <i class="fa {{$documento->fasm()}}"></i>
                                        </div>
                                      </td>
                                      <td style="line-height:35px;">
                                        {{str_replace("."," ",$documento->titulo)}}
                                      </td>
                                      <td>
                                        <a href="/documentos/download/{{md5($documento->id)}}" class="btn btn-sm btn-info">
                                          <i class="fa fa-download"></i>
                                        </a>
                                        <a target="_blank" href="/documentos/watchar/{{md5($documento->id)}}" class="btn btn-sm btn-success">
                                          <i class="fa fa-eye"></i>
                                        </a>
                                      </td>
                                    </tr>
                                  @endforeach
                                @else
                                <tr>
                                  <td><span class="texto">No hay documentos</span></td>
                                </tr>
                              @endif
                          </table>
                          @if (Auth::user()->cliente->ccredito()->status == 0)
                            <hr>
                              <div class="col-12" id="drop" style="border:dashed #6f6f6f 2px;">
                                <form action="/documentos/subircredit?id={{$c->id}}" enctype="multipart/form-data" method="POST"  class="dropzone" id="dropzone">
                                  <div class="fallback">
                                    <input type="hidden" name="id" value="{{$c->id}}">
                                    <input name="file" type="file" multiple />
                                    <input type="submit" name="" value="subir">
                                  </div>
                                </form>
                              </div>
                            @endif
                          @endif
                      </div>
                </div>
                @if (Auth::user()->empleado->status >= 3)
                  <hr>
                  @if (Auth::user()->cliente->ccredito()->status == 0)
                      <div class="row">
                            <div class="col"></div>
                            <div class="col-3">
                              <button type="submit" w="/credito/seto" name="status" class="rs btn btn-primary large" value="1">
                              <i class="fa fa-save"></i>    Enviar a revisión
                              </button>
                            </div>
                          </div>
                  @endif
                @endif
              </div>
            </div>

@endsection
@section('styles')
  <link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
@endsection
@section('scripts')
<script src="{{ asset('js/dropzone.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    @if (Auth::user()->cliente->ccredito()->status == 0)
    $(".as").bind("change",function(){
      $(this.parentNode).find("label").append("<i class='fas fa-cog fa-spin'></i>");
      $.post($(this).attr("w")+"?seto="+$(this).prop("name")+"&cid="+$(".cid").val()+"&v="+$(this).val(),function(data){
        $("label").find("i").remove()
      });
    });
    $(".rs").bind("click",function(){
      $(this.parentNode).find("label").append("<i class='fas fa-cog fa-spin'></i>");
      $.post($(this).attr("w")+"?seto="+$(this).prop("name")+"&cid="+$(".cid").val()+"&v="+$(this).val(),function(data){
        $("label").find("i").remove();
        location.reload();
      });
    });
    @endif
    var myDropzone = new Dropzone("#dropzone");
    $(".dz-message").text("Arrastra y suelta aquí archivos para adjuntarlos");
    myDropzone.on("success", function(file,data) {
      data = JSON.parse(data);
      if($(".table").find(".texto"))
        $(".table").empty();
      $(".table").append($("<tr>")
        .append(
          $("<td>")
            .css({"width":"200px"})
            .append($("<div>")
              .addClass("btn btn-link")
                .append($("<li>")
                  .addClass("fa fa-file"))))
          .append($("<td>").css({"line-height":"35px"}).text(data.titulo))
          .append($("<td>")));
    });
  });
</script>
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
