@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
@php
  $c = $empleado = Auth::user()->empleado;
@endphp
@if (Auth::user()->cliente->isinscripcion == null)
  <form action="/alumnos/inscribirse" method="post">
        <div class="row">
            <div class="col">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Mi inscripción</h5>
                  <h6 class="card-subtitle mb-2 text-muted">Información personal (CUOV-{{date("Y")}}-{{$c->id}})</h6>
                  <hr>
                </div>
                  <div class="card-body">
                    @if ($empleado->status <= 4)
                      <div class="card-text">
                        <h5 class="card-title">Información general</h5>
                        <hr>
                        <div class="row">
                          <div class="col">
                              <label for="fi1" class="form-label">Introduce tu nombre completo</label>
                              <input type="text" required class="form-control" name="nombre_completo" id=fi1 placeholder="Nombre completo">
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Fecha de nacimiento</label>
                              <input type="date" required class="form-control" name="fecha_nacimiento" id=fi1 placeholder="DD/MM/AAAA">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Estado de nacimiento</label>
                              <input type="text" required class="form-control" name="estado_nacimiento" id=fi1 placeholder="Veracruz">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">CURP</label>
                              <input type="text" required class="form-control" name="curp" id=fi1 placeholder="RARJ940901HRZLR002">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Estado civil</label>
                              <select class="form-control" required name="estado_civil" aria-label="Selecciona">
                                <option selected>Seleccionar</option>
                                <option value="Soltero">Soltero</option>
                                <option value="Casado">Casado</option>
                                <option value="Divorciado">Divorciado</option>
                                <option value="Otro">Three</option>
                              </select>
                          </div>
                        </div>
                        <hr>
                        <h5 class="card-title">Domicilio particular</h5>
                        <hr>
                        <div class="row">
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Calle</label>
                              <input type="text" required class="form-control" name="no_domicilio" id=fi1 placeholder="Calle 21">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">No. interior</label>
                              <input type="text" required class="form-control" name="no_interior" id=fi1 placeholder="43">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">No. exterior</label>
                              <input type="text" required class="form-control" name="no_exterior" id=fi1 placeholder="43 B">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Colonia</label>
                              <input type="text" required class="form-control" name="colonia" id=fi1 placeholder="Las flores">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Localidad, Municipio, Estado</label>
                              <input type="text" required class="form-control" name="estado" id=fi1 placeholder="Localidad, Municipio, Estado">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">C&oacute;digo postal</label>
                              <input type="text" required class="form-control" name="cp" id=fi1 placeholder="Código postal">
                          </div>
                        </div>
                        <hr>
                        <h5 class="card-title">Contacto</h5>
                        <hr>
                        <div class="row">
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Télefono de casa</label>
                              <input type="text" required class="form-control" name="tel" id=fi1 placeholder="(555) 555 55 5555">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Celular</label>
                              <input type="text" required class="form-control" name="celular" id=fi1 placeholder="(555) 555 55 5555">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Correo electrónico</label>
                              <input type="text" required class="form-control" name="correo" id=fi1 placeholder="alguien@outlook.com">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Redes sociales</label>
                              <input type="text" required class="form-control" name="redes" id=fi1 placeholder="/redsocial">
                          </div>
                        </div>
                        <hr>
                        <h5 class="card-title">Datos fiscales</h5>
                        <p>En caso de requerir factura, anexar los datos fiscales.</p>
                        <hr>
                        <div class="row">
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">¿Requiere factura?</label>
                              <select class="form-control"  required name="factura" aria-label="Default select example">
                                <option selected>Seleccionar</option>
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                              </select>
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Nombre o razón social</label>
                              <input type="text" class="form-control" name="razon_social" id=fi1 placeholder="Nombre o razón social">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">RFC</label>
                              <input type="text" class="form-control" name="rfc" id=fi1 placeholder="RFC">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Correo electrónico para envio de factura</label>
                              <input type="text" class="form-control" name="correo_fiscal" id=fi1 placeholder="alguien@gmail.com">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Domicilio fiscal</label>
                              <input type="text" class="form-control" name="direccion_fiscal" id=fi1 placeholder="Calle 21">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Uso CFDI</label>
                              <input type="text" class="form-control" name="uso_cfdi" id=fi1 placeholder="Gastos en general">
                          </div>
                        </div>
                        <hr>
                        <h5 class="card-title">Información laboral</h5>
                        <hr>
                        <div class="row">
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Actualmente, ¿trabaja?</label>
                              <select class="form-control"  required name="trabaja" aria-label="Default select example">
                                <option selected>Seleccionar</option>
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                              </select>
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">¿En que empresa labora actualmente?</label>
                              <input type="text" class="form-control" name="empresa" id=fi1 placeholder="Nombre de la empresa, rublo">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Puesto</label>
                              <input type="text" class="form-control" name="puesto" id=fi1 placeholder="Auxiliar de ventas, Gerente, etc, ...">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">No. interior/exterior, Calle</label>
                              <input type="text" class="form-control" name="no_domicilio_empresa" id=fi1 placeholder="Calle 21, 43 B">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Colonia</label>
                              <input type="text" class="form-control" name="colonia_empresa" id=fi1 placeholder="Las flores">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">Localidad, Municipio, Estado</label>
                              <input type="text" class="form-control" name="estado_empresa" id=fi1 placeholder="Localidad, Municipio, Estado">
                          </div>
                          <div class="col-12 col-md-12 col-lg-6">
                              <label for="fi1" class="form-label">C&oacute;digo postal</label>
                              <input type="text" class="form-control" name="cp_empresa" id=fi1 placeholder="Código postal">
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="col">
                      <div class="card">
                        <div class="card-body">
                          <h5 class="card-title">Información académica</h5>
                          <h6 class="card-subtitle mb-2 text-muted">Inscripción (CUOV-{{date("Y")}}-{{$c->id}})</h6>
                          <hr>
                          <div class="row">
                            <div class="col-12">
                                <label for="fi1" class="form-label">Último grado de estudios</label>
                                <select class="form-control"  required name="ultimo" aria-label="Default select example">
                                  <option selected>Selecciona</option>
                                  <option value="Bachillerato">Bachillerato</option>
                                  <option value="Preparatoria">Preparatoria</option>
                                  <option value="Licenciatura">Licenciatura</option>
                                  <option value="Técnico Superior Universitario">Técnico Superior Universitario</option>
                                  <option value="Maestría">Maestría</option>
                                  <option value="Doctorado">Doctorado</option>
                                  <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="fi1" class="form-label">Nombre de la institución educativa donde realizó  su último grado de estudios</label>
                                <input type="text" required class="form-control" name="institucion" id=fi1 placeholder="CBTIS 43">
                            </div>
                            <div class="col-12">
                                <label for="fi1" class="form-label">¿Requiere revalidación de materias?</label>
                                <select class="form-control" required name="revalidar" aria-label="">
                                  <option selected>Seleccionar</option>
                                  <option value="Si">Si</option>
                                  <option value="No">No</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-12 col-lg-6">
                                <label for="fi1" class="form-label">¿Por cuál medio conociste a la Universidad Santander?</label>
                                <select class="form-control" required name="medio" aria-label="">
                                  <option selected>Seleccionar</option>
                                  <option value="Facebook">Facebook</option>
                                  <option value="Instagram">Instragram</option>
                                  <option value="Instalaciones">Instalaciones</option>
                                  <option value="Recomendación de un amigo">Recomendación de un amigo</option>
                                  <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-12 col-lg-6">
                                <label for="fi1" class="form-label">Escoga el programa educativo al que desea inscribirse</label>
                                <select class="form-control" required name="programa_educativo" aria-label="">
                                  <option selected>Seleccionar</option>
                                  <option value="Licenciatura en informática">Licenciatura en informática</option>
                                  <option value="Licenciatura en educación">Licenciatura en educación</option>
                                  <option value="Licenciatura en Derecho">Licenciatura en Derecho</option>
                                  <option value="Licenciatura en Contaduría">Licenciatura en Contaduría</option>
                                  <option value="Licenciatura en Administración de empresas">Licenciatura en Administración de empresas</option>
                                  <option value="Licenciatura en Mercadotecnia">Licenciatura en Mercadotecnia</option>
                                  <option value="Licenciatura en Turismo">Licenciatura en Turismo</option>
                                  <option value="Maestría en Educación">Maestría en Educación</option>
                                  <option value="Doctorado en Ciencias de la Educación">Doctorado en Ciencias de la Educación</option>
                                  <option value="Maestría + Doctorado en Educación">Maestría + Doctorado en Educación</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-12 col-lg-6">
                                <label for="fi1" class="form-label">¿En que periodo desea ingresar?</label>
                                <select class="form-control" required name="periodo" aria-label="">
                                  <option selected>Seleccionar</option>
                                  <option value="4 Enero">4 Enero</option>
                                  <option value="1 Febrero">1 Febrero</option>
                                  <option value="1 Marzo">1 Marzo</option>
                                  <option value="29 Marzo">29 Marzo</option>
                                  <option value="26 Abril">26 Abril</option>
                                  <option value="24 Mayo">24 Mayo</option>
                                  <option value="21 Junio">21 Junio</option>
                                  <option value="19 Julio">19 Julio</option>
                                  <option value="16 Agosto">16 Agosto</option>
                                  <option value="13 Septiembre">13 Septiembre</option>
                                  <option value="11 Octubre">11 Octubre</option>
                                  <option value="8 Noviembre">8 Noviembre</option>
                                  <option value="6 Diciembre">6 Diciembre</option>
                                  <option value="5 de Julio">Posgrados: 5 de Julio</option>
                                  <option value="2 de Agosto">Posgrados:2 de Agosto</option>
                                  <option value="30 de Agosto">Posgrados:30 de Agosto</option>
                                  <option value="27 de Septiembre">Posgrados:27 de Septiembre</option>
                                  <option value="25 de Octubre">Posgrados:25 de Octubre</option>
                                  <option value="22 de Noviembre">Posgrados:22 de Noviembre</option>
                                  <option value="20 de Diciembre">Posgrados:20 de Diciembre</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-12 col-lg-6">
                                <label for="fi1" class="form-label">Indique el número de materias a cursar</label>
                                <select class="form-control" required name="materias" aria-label="">
                                  <option selected>Seleccionar</option>
                                  @for ($i=1; $i < 5; $i++)
                                    <option value="{{$i}} materias">{{$i}} materias</option>
                                  @endfor
                                </select>
                            </div>
                            <div class="col-12">
                              <hr>
                              <input type="submit" class="btn btn-primary" name="" value="Enviar inscripción">
                            </div>
                          </div>
                        </div>
                      </div>
                </div>
            </div>
          </div>
</form>
  @else
    <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Mi inscripción</h5>
              <h6 class="card-subtitle mb-2 text-muted">Información personal (CUOV-{{date("Y")}}-{{$c->id}})</h6>

                Tu solicitud ya ha sido enviada
            </div>
          </div>
        </div>
    </div>
@endif
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
