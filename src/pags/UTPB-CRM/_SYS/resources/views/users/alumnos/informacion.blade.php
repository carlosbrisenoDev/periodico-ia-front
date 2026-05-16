@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="row">
            <div class="col">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Mi solicitud</h5>
                  @php
                    $c = $empleado = Auth::user()->empleado;
                  @endphp
                  <h6 class="card-subtitle mb-2 text-muted">Inscripción (CUOV-{{date("Y")}}-{{$c->id}})</h6>
                  <hr>
                </div>
                  <div class="card-body">
                    @if ($empleado->status <= 4)
                      <div class="card-text">
                        <div class="row">
                          <div class="col">
                            <h4>Instrucciones</h4>
                          </div>
                        </div>
                        <p align="justify">
                          Agregue los documentos necesarios:
                        </p><p>¿Cómo subir documentos?:
                          <ul class="list">
                            <li>
                              1. Haga click en <i class="fa fa-plus"></i> para anexar documentos nuevos.
                            </li>
                            <li>
                              2. Haga click en <i class="fa fa-upload"></i> para subir los documentos.
                            </li>
                            <li>
                              3. Espere que termine la carga de documentos.
                            </li>
                          </ul>
                        </p>
                        <p align="justify">
                          Una vez que todos los documentos que considere necesarios hayan sido cargados y revisados, haga click en <i class="fa fa-save"></i> para enviar a revisión al departamento de control escolar; control escolar se encargará de contactarse con usted para dar una respuesta o complementar su solicitud.
                        </p>
                        <p align="justify">
                          Enviada la solicitud, no podrá modificar el contenido de los documentos cargados hasta que el departamento de control escolar lo considere necesario o sea descartada.
                        </p>
                        <p align="justify">
                          Para más información acerca del uso de su información consulte nuestro <a href="https://unisant.edu.mx/aviso-de-privacidad-y-politicas-de-pago/">aviso de privacidad</a>
                        </p>
                        <div class="jumbotron">
                          <h5 class="card-title">
                            REQUISITOS DE INSCRIPCIÓN
                          </h5>
                          <h6 class="card-subtitle mb-2 text-muted">
                            DOCUMENTACIÓN REQUERIDA:
                          </h6>
                          <hr>
                          <p align="justify">Los documentos son escaneados del original</p>
                          <li>Llenar solicitud de inscripción con correo electrónico y número celular (fijo o celular).</li>
                          <li>Acta de nacimiento.</li>
                          <li>Identificación Oficial (INE, pasaporte, cartilla militar).</li>
                          <li>Fotografía a color tamaño credencial.</li>
                          <li>Certificado de Bachillerato o equivalente.</li>
                          <li>CURP.</li>
                          <li>Comprobante de domicilio no mayor a 3 meses de antigüedad (Teléfono, servicio de cable, luz, etc.).</li>
                          <li>Pago de inscripción.</li>
                          <li>Pago de número de materias a cursar.</li>
                        </div>
                        @if (Auth::user()->empleado->status == 3)
                          <p align="justify">
                            <h4 style="color:#DC143C;">Solicitud en proceso de revisión</h4>
                            Su solicitud esta en proceso de ser revisada por el departamento de control escolar, pronto recibirá respuesta en su correo electrónico <a href="#">{{Auth::user()->empleado->correo}}</a>.
                          </p>
                        @endif
                      </div>
                    </div>
                </div>
                @else
                    El estado de tu solicitud es <b>Inscrito</b>
                @endif
            </div>
            <div class="col">
                      <div class="card">
                        <div class="card-body">
                          <h5 class="card-title">Documentos</h5>
                          <h6 class="card-subtitle mb-2 text-muted">Inscripción (CUOV-{{date("Y")}}-{{$c->id}})</h6>
                          <hr>
                        <div class="row">
                          @if (Auth::user()->empleado->status != 3)
                            <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                              <div class="card topp">
                                <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="/documentos/saveempleado">
                                  <input type="hidden" name="id" value="{{$empleado->id}}">
                                  {{ csrf_field() }}
                                  <input  id="archivo"  type="file" style="display:none;" multiple name="documento[]" placeholder="Seleccione los documentos">
                                  <div class="seleccionar">
                                    <i class="fa fa-plus"></i>
                                  </div>
                                  <span class="texto">1. Agregar documentos </span>
                                  <div class="clearfix">
                                    <button type="submit" class="btn btn-primary large" id="titulo">
                                    <i class="fa fa-upload"></i>    2. Subir documentos
                                    </button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          @endif
                          @if (count($empleado->documentos) > 0)
                          @foreach ($empleado->documentos as $documento)
                            <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                              <div class="card topp">
                                <div class="img img-responsive text-center">
                                  <i class="fa {{$documento->fa()}}"></i>
                                </div>
                                <span class="texto">{{str_replace("."," ",$documento->titulo)}}</span>
                                <div class="row bg-secondary" style="margin:0;">

                                  <div class="col">
                                    <div class="input-group">
                                      <div class="input-group-prepend">
                                        <a href="/documentos/download/{{md5($documento->id)}}" class="btn btn-secondary"><i class="fa fa-download"></i></a>
                                        <a target="_blank" href="/documentos/watchar/{{md5($documento->id)}}" class="btn btn-secondary"><i class="fa fa-eye"></i></a>
                                        @if (!Auth::user()->empleado->status == 3)
                                          <a href="/documentos/delete/{{md5($documento->id)}}" class="btn btn-primary"><i class="fa fa-trash"></i></a>
                                        @endif
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>

                            </div>
                          @endforeach
                          @else
                            <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                              <div class="card topp">
                                <div class="img img-responsive text-center">
                                  <div class="seleccionar">
                                  </div>
                                </div>
                                <span class="texto">No hay documentos</span>
                                <div class="row " style="margin:0;height:38px;">

                                  <div class="col">
                                    <div class="">

                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                        @endif
                        </div>
                      </div>
                </div>
                @if (Auth::user()->empleado->status != 3)
                  <hr>
                  <form class="" action="/clientes/set" method="post">
                    <div class="row">
                          <div class="col"></div>
                          <div class="col-3">
                            <button type="submit" class="btn btn-primary large" {{(Auth::user()->empleado->status == 3) ? "disabled" : ""}}>
                            <i class="fa fa-save"></i>    Enviar a revisión
                            </button>
                          </div>
                        </div>
                  </form>
                @endif
              </div>
            </div>

@endsection
@section('scripts')
  <script type="text/javascript">
      $(document).ready(function(){
        $(".seleccionar").on("click",function(){
           $("#archivo").click();
        });
        $("#archivo").on("change",function(){
          $("#titulo").text("Subir "+$("#archivo").val().split('\\')[$("#archivo").val().split('\\').length-1]);
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
