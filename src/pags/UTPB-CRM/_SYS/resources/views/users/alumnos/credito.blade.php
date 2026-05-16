@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="row">
            <div class="col">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Crédito</h5>
                  @php
                    $c = $empleado = Auth::user()->empleado;
                  @endphp
                  <h6 class="card-subtitle mb-2 text-muted">Solicitud (CRUOV-{{date("Y")}}-{{$c->id}})</h6>
                  <hr>
                </div>
                  <div class="card-body">
                    @if ($empleado->status <= 4)
                      <div class="card-text">
                        <div class="jumbotron">
                          <h5 class="card-title">
                            CRÉDITO
                          </h5>
                          <h6 class="card-subtitle mb-2 text-muted">
                            REQUISITOS:
                          </h6>
                          <hr>
                          <li>Llenar tu solicitud de inscripción ({!!($empleado->isinscripcion==null) ? "<a href='/alumnos/formulario'>Llenar</a>" : "Listo"!!})</li>
                          <li>Subir los documentos requeridos para tu inscripción ({!!($empleado->status==2) ? "<a href='/alumnos/informacion'>Llenar</a>" : "Listo"!!})</li>
                          <li>Llenar el formulario presentado a continuación</li>
                        </div>
                        @if (Auth::user()->cliente->credito == 2)
                          <p align="justify">
                            <h4 style="color:#DC143C;">Solicitud en proceso de revisión</h4>
                            Su solicitud esta en proceso de ser revisada por el departamenteo de control escolar, pronto recibirá respuesta en su correo electrónico <a href="#">{{Auth::user()->empleado->correo}}</a>.
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
                          <h5 class="card-title">Crédito</h5>
                          <h6 class="card-subtitle mb-2 text-muted">Llenado de solicitud (CRUOV-{{date("Y")}}-{{$c->id}})</h6>
                          <hr>
                          @php
                            $cr = Auth::user()->cliente->cinfo();
                          @endphp
                          @if ($cr->status != null)
                            <div class="alert alert-info">
                              <i class="fa fa-info"></i> Tu solicitud se encuentra en revisión, pronto recibirás una respuesta por parte de nuestro personal.
                            </div>
                            <h6>Información proporcionada</h6>
                            <div class="row">
                              <div class="col-12 col-md-12 col-lg-6">
                                <label for="formGroupExampleInput" class="form-label">¿A qué se dedica?:</label>
                                <div class="form-control">{{$cr->dedica}}</div>
                              </div>
                              <div class="col-12 col-md-12 col-lg-6">
                                <label for="formGroupExampleInput" class="form-label">¿Trabaja?</label>
                                <div class="form-control">
                                  {{$cr->trabaja}}
                                </div>
                              </div>
                              <div class="col-12 col-md-12 col-lg-6">
                                <label for="formGroupExampleInput" class="form-label st">¿Cuál es su ingreso mensual?:</label>
                                <div class="form-control">
                                  {{$cr->ingreso}}
                                </div>
                              </div>
                              <div class="col-12 col-md-12 col-lg-6">
                                <label for="formGroupExampleInput" class="form-label">Su casa, ¿Es propia o rentada?:</label>
                                <div class="form-control">
                                  {{$cr->casa}}
                                </div>
                              </div>
                              <div class="col-12 col-md-12 col-lg-6">
                                <label for="formGroupExampleInput" class="form-label">¿Cuánto tiempo ha vivido en su actual residencia?:</label>
                                <div class="form-control">
                                  {{$cr->anios}}
                                </div>
                              </div>
                              <div class="col-12 col-md-12 col-lg-6">
                                <label for="formGroupExampleInput" class="form-label">RFC:</label>
                                <div class="form-control">
                                  {{$cr->RFC}}
                                </div>
                              </div>
                            </div>
                            <hr>
                            <h6>Información familiar</h6>
                            <hr>
                            <div class="row familiares">
                              @foreach (Auth::user()->cliente->cinfo()->familiares as $familiar)
                                <div class="col-12">
                                  <div class="row">
                                    <div class="col-12 col-md-12 col-lg-6">
                                      <div class="form-control">
                                        {{$familiar->nombre}}
                                      </div>
                                    </div>
                                    <div class="col-2">
                                      <div class="form-control">
                                        {{$familiar->edad}}
                                      </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                                      <div class="form-control">
                                        {{$familiar->relacion}}
                                      </div>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col-12 col-md-6 col-lg-4 col-xl-4" style="margin-top:10px;">
                                      <div class="form-control">
                                        {{$familiar->horario}}
                                      </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-4 col-xl-4" style="margin-top:10px;">
                                      <div class="form-control">
                                        {{$familiar->telefono}}
                                      </div>
                                    </div>
                                  </div>
                                  <hr>
                                </div>
                              @endforeach
                            </div>
                          @else
                          @if (Auth::user()->cliente->isinscripcion != null && $empleado->status != 2)
                            <form class="" action="/creditos/actualizar" method="post">
                              <h6>Información general</h6>
                              <div class="row">
                                <div class="col-12 col-md-12 col-lg-6">
                                  <label for="formGroupExampleInput" class="form-label">¿A qué se dedica?:</label>
                                  <input type="text" w="/creditos/seto" class="as form-control" name="dedica" placeholder="Yo ..." value="{{$cr->dedica}}">
                                </div>
                                <div class="col-12 col-md-12 col-lg-6">
                                  <label for="formGroupExampleInput" class="form-label">¿Trabaja?</label>
                                  <select class="form-control as aval" w="/creditos/seto" onchange="root()" name="trabaja">
                                    <option value="Si" {{($cr->trabaja == "Si") ? "selected":""}}>Si</option>
                                    <option value="No" {{($cr->trabaja == "No") ? "selected":""}}>No</option>
                                  </select>
                                </div>
                                <div class="col-12 col-md-12 col-lg-6">
                                  <label for="formGroupExampleInput" class="form-label st">¿Cuál es su ingreso mensual?:</label>
                                  <input type="text" w="/creditos/seto" class="as form-control" name="ingreso" placeholder="..." value="{{$cr->ingreso}}">
                                </div>
                                <div class="col-12 col-md-12 col-lg-6">
                                  <label for="formGroupExampleInput" class="form-label">Su casa, ¿Es propia o rentada?:</label>
                                  <select class="form-control as aval" w="/creditos/seto" name="casa">
                                    <option value="Propia" {{($cr->casa == "Propia") ? "selected":""}}>Propia</option>
                                    <option value="Rentada" {{($cr->casa == "Rentada") ? "selected":""}}>Rentada</option>
                                  </select>
                                </div>
                                <div class="col-12 col-md-12 col-lg-6">
                                  <label for="formGroupExampleInput" class="form-label">¿Cuánto tiempo ha vivido en su actual residencia?:</label>
                                  <input type="text" w="/creditos/seto" class="as form-control" name="anios" placeholder="..." value="{{$cr->anios}}">
                                </div>
                                <div class="col-12 col-md-12 col-lg-6">
                                  <label for="formGroupExampleInput" class="form-label">RFC:</label>
                                  <input type="text" required w="/creditos/seto" class="as form-control" name="rfc" placeholder="RFC" value="{{$cr->rfc}}">
                                </div>
                                <input type="hidden" class="cid" value="{{md5($cr->id)}}">
                              </div>
                              <hr>
                              <h6>Información familiar</h6>
                              <hr>
                              <div class="row familiares">

                              </div>
                              <div class="row">
                                <div class="col-12 h">
                                  <a href='#h' class="btn btn-link fami">Agregar familiar</a>
                                </div>
                              </div>
                              <hr>
                              <div class="row">
                                <div class="col">

                                </div>
                                <div class="col-3">
                                  <input type="submit" class="btn btn-primary" value="Enviar solicitud">
                                </div>
                              </div>
                            </form>
                          @endif
                        @endif
                      </div>
                </div>
                @if (Auth::user()->empleado->status != 3)
                  <hr>
                  {{-- @if (Auth::user()->cliente->isinscripcion != null && $empleado->status != 2)
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
                    @else
                      Debes de llenar tu formulario de inscripción y subir tus documentos antes.
                        <div class="row">
                          <div class="col"></div>
                          <div class="col-3">
                            <button type="submit" class="btn btn-primary large disabled" {{(Auth::user()->empleado->status == 3) ? "disabled" : ""}}>
                            <i class="fa fa-save"></i>    Enviar a revisión
                            </button>
                          </div>
                        </div>
                  @endif --}}
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
        $(".fami").bind("click",function(){
          var n = $("<div>").addClass("col-12");
          var f = $("<div>").addClass("row");
          var r = $("<div>").addClass("col-4");
          n.append($("<div>").addClass("col-12").append($("<h6>").text("Familiar")));
          r.append($("<input type='text' name='nombre[]'>").addClass("form-control mb-3").prop("placeholder","Nombre completo"));
          f.append(r);
          r = $("<div>").addClass("col-4");
          r.append($("<input type='text' name='edad[]'>").addClass("form-control mb-3").prop("placeholder","Edad"));
          f.append(r);
          r = $("<div>").addClass("col-4");
          r.append($("<input type='text' name='relacion[]'>").addClass("form-control mb-3").prop("placeholder","Relación"));
          f.append(r);
          r = $("<div>").addClass("col-4");
          r.append($("<input type='text' name='telefono[]'>").addClass("form-control mb-3").prop("placeholder","(555) 555 5555"));
          f.append(r);
          r = $("<div>").addClass("col-4");
          r.append($("<input type='text' name='horario[]'>").addClass("form-control mb-3").prop("placeholder","13:30, Lunes, Martes y Jueves"));
          f.append(r);
          n.append(f);
          n.append($("<hr>"));
          $(".familiares").append(n);
        });
      });
      var root = function(){
        if($(".aval").val() == "Si"){
          $(".st").text("¿Cuál es su ingreso mensual?:");
        } else if($(".aval").val() == "No"){
          $(".st").text("¿Cuenta con un aval solidario?:");
        } else {
          $(".st").text("");
        }
      }
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
