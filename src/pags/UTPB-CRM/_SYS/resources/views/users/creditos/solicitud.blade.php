@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  @php
    $ci = \App\credito_info::whereRAW("md5(id)='".$_REQUEST["cid"]."'")->first();
    $foto = asset("images/nofoto.png");
    $cl = $c = $ci->cliente;
  @endphp
  <div class="row">
    <div class="col-12 col-md-12 col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Cr&eacute;ditos</h5>
          <h6 class="card-subtitle mb-2 text-muted">Solicitante CRUOV-{{\carbon\carbon::parse($ci->created_at)->format("Y")}}-{{$ci->id}}</h6>
          <hr>
          <div class="row">
            <div class="col-3">
              <img src="{{$foto}}" class="card-img-top"  alt="">
            </div>
            <div class="col-9">
              <table class="table table-strip">
                <tr>
                  <td>Facebook:</td>
                  <td>
                    @if (count($cl->leads) > 0)
                      <a target="_blank" href="https://facebook.com/profile/?psid={{$cl->leads[0]->recipient}}">
                        <i class="fab fa-facebook"></i> {{$cont->first_name." ".$cont->last_name}}
                      </a>
                    @endif
                  </td>
                </tr>
                <tr>
                  <td>Creado:</td>
                  <td>
                    {{\carbon\carbon::parse($cl->created_at)->diffForHumans()}}
                  </td>
                </tr>
                <tr>
                  <td>Tel&eacute;fono 1:</td>
                  <td>
                    <a class="btn" style="color:green;" target="_blank"  href="https://api.whatsapp.com/send?phone=+521{{$cl->telefono}}&text=Hola" data-bs-toggle="tooltip" data-bs-placement="top" title="Enviar WhatsApp">
                      <i class="fab fa-whatsapp"></i> {{$cl->telefono}}
                    </a>
                  </td>
                </tr>
                <tr>
                  <td>Correo electr&oacute;nico:</td>
                  <td>
                    {{$cl->correo}}
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <hr>
          <div class="card card-body">
            <h6 class="card-subtitle mb-2 text-muted">Datos generales</h6>
            <hr>
            <div class="row">
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Nombre completo
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->nombre_completo}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Fecha de nacimiento
                </label>
                <div class="form-control">
                  {{\Carbon\carbon::parse($c->isinscripcion->fecha_nacimiento)}}
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Estado Civil
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->estado_civil}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Domicilio
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->no_domicilio}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Colonia
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->colonia}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Estado
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->estado}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  C&oacute;digo postal
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->cp}}
                </div>
              </div>
            </div>
            <hr>
            <h6 class="card-subtitle mb-2 text-muted">Datos de contacto</h6>
            <hr>
            <div class="row">
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Tel&eacute;fono
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->tel}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Celular
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->celular}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Correo
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->correo}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Redes
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->redes}}
                </div>
              </div>
            </div>
            <hr>
            <h6 class="card-subtitle mb-2 text-muted">Datos de facturaci&oacute;n</h6>
            <hr>
            <div class="row">
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Factura
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->factura}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Raz&oacute;n social
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->razon_social}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  RFC
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->rfc}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Correo Fiscal
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->correo_fiscal}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Domicilio fiscal
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->direccion_fiscal}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Uso CFDI:
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->uso_cfdi}}
                </div>
              </div>
            </div>
            <hr>
            <h6 class="card-subtitle mb-2 text-muted">Datos laborales</h6>
            <hr>
            <div class="row">
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Trabaja
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->trabaja}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Empresa
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->empresa}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Domicilio
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->no_domicilio_empresa}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Colonia
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->colonia_empresa}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Estado
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->estado_empresa}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  CP
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->cp_empresa}}
                </div>
              </div>
            </div>
            <hr>
            <h6 class="card-subtitle mb-2 text-muted">Datos laborales</h6>
            <hr>
            <div class="row">
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  &Uacute;ltimo grado de estudio
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->ultimo}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Instituci&oacute;n de estudio
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->institucion}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Revalidar materias
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->revalidar}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Como conocio ...
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->medio}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Programa educativo
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->programa_educativo}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Periodo
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->periodo}}
                </div>
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label>
                  Materias
                </label>
                <div class="form-control">
                  {{$c->isinscripcion->materias}}
                </div>
              </div>
            </div>

          </div>
        </div>
    </div>
  </div>
  <div class="col-12 col-md-12 col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Solicitud de crédito</h5>
        <h6 class="card-subtitle mb-2 text-muted">Formulario</h6>
        <hr>
        @php
          $cr = $c->cinfo();
        @endphp
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
            @foreach ($c->cinfo()->familiares as $familiar)
              <div class="col-12">
                <div class="row">
                  <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                    <div class="form-control">
                      {{$familiar->nombre}}
                    </div>
                  </div>
                  <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                    <div class="form-control">
                      {{$familiar->edad}}
                    </div>
                  </div>
                  <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                    <div class="form-control">
                      {{$familiar->relacion}}
                    </div>
                  </div>
                  <br>
                  <hr>
                  <br>
                  <div class="col-12 col-md-12 col-lg-6">
                    <div class="form-control">
                      {{$familiar->telefono}}
                    </div>
                  </div>
                  <div class="col-12 col-md-12 col-lg-6">
                    <div class="form-control">
                      {{$familiar->horario}}
                    </div>
                  </div>
                </div>
                <hr>
              </div>
            @endforeach
          </div>
          @php
            $cr = $c->cinfo();
            $co = $c->ccredito();
          @endphp
          @if ($co != null)
            <h6 style="color:red;">Información general (Formulario 2)</h6>
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
          @endif
          <hr>
          <h6 class="card-subtitle mb-2 text-muted">Documentos de inscripción</h6>
          <hr>
          <div class="row table-responsive">
            <table class="table">
            @if (count($c->documentos) > 0)
              @foreach ($c->documentos as $documento)
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
          </div>
          <h6 class="card-subtitle mb-2 text-muted">Acciones</h6>
          <hr>
          @if ($cl->credito == null)
            <div class="alert alert-warning">
              Este usuario no tiene solicitud asignada de crédito. En el siguiente formulario puede anexar la informaciíon correspondiente.
            </div>
            <form class="" action="/clientes/acredito" method="post">
              <div class="col-md-12">
                <label for="">Porcentaje de credito</label>
                <input type="hidden" name="cid" value="{{md5($cl->id)}}">
                <select class="form-control" name="credito">
                  <option  {{(null==$cl->credito) ? "selected=selected" : ""}} value="null">Desactivar crédito</option>
                  @for ($i=0; $i <= 12; $i++)
                    <option {{($i.""==$cl->credito) ? "selected" : ""}} value="{{$i}}">{{$i}}%</option>
                  @endfor
                </select>
                <label for="">Plazo</label>
                <select class="form-control" name="plazo">
                  @for ($i=48; $i >= 1; $i--)
                    <option {{($i.""==$cl->plazo) ? "selected" : ""}} value="{{$i}}">{{$i}} Mes{{$i==1?"":"es"}}</option>
                  @endfor
                </select>
              </div>
              <div class="col-md-12 clearfix">
                <div class="pull-right">
                </br>
                  <button type="submit" class="btn btn-info" value="" data-bs-toggle="tooltip" data-bs-placement="top" title="Asignar formulario de crédito"><i class="fa fa-save"></i></button>
                </div>
              </div>
            </form>
          @endif
          @if ($cl->credito_info->status == "preaprobado" && $cl->ccredito()->status == 1)
            <div class="col-12 col-md-6 col-lg-4 col-xl-4">
            <!--  <a href="#" class="firma btn btn-success">
                Solicitar firma de pagaré dígital
              </a>-->
            </div>
            <!--<div class="col-12">
              <hr>
              <div class="alert alert-info">
                <i class="fa fa-info"></i>
                Solicitar firma envia al alumno una solicitud rellenada con la información del crédito, dónde acepta dígitalmente
              </div>
            </div>-->
          @endif
          @if ($cl->credito_info->status != "cartera")
            <div class="row">
              <div class="col">
                <a href="#" class="cartera btn btn-info" data-toggle="modal" data-target="#calendarmodal1">
                  Agregar a cartera
                </a>
              </div>
            </div>
          @endif
          <hr>
          @if ($cl->credito_info->status == "enviado")
            <div class="row">
              <div class="col">
                <a href="#" class="aprobar btn btn-success" data-toggle="modal" data-target="#calendarmodal4">
                  Pre aprovar crédito
                </a>
              </div>
              <div class="col">

              </div>
              <div class="col">
                <a href="#" class="credito btn btn-danger" data-toggle="modal" data-target="#calendarmodal2">
                  Rechazar
                </a>
              </div>
              <div class="col-12">
                <hr>
                <div class="alert alert-info">
                  <i class="fa fa-info"></i>
                  Pre aprovar otorga al cliente los formularios de información necesarios para recibir el credíto.
                </div></br>
                <div class="alert alert-warning">
                  <i class="fa fa-info"></i>
                  Rechazar un crédito, envia un mensaje al cliente desactivando su formulario de solicitud de crédito.
                </div>
              </div>
            </div>
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
      $(".cartera").bind("click",function(){
        $(".fcartera").modal();
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
      $(".credito").bind("click",function(){
        $(".fcredito").modal();
      });
      $(".aprobar").bind("click",function(){
        $(".faprobar").modal();
      });
      $(".firma").bind("click",function(){
        $(".ffirma").modal();
      });
    });
  </script>
@endsection
@section('modal')
  <div class="modal fcartera fade" id="calendarmodal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Cartera</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="" action="/creditos/carteraadd" method="post">
              <label for="">¿Agregar cliente a cartera?</label>
              <p>
                Al agregar al cliente a la cartera indica que ya ha realizado la validación correspondiente de los datos dentro o fuera de la plataforma.
              </p>
              <input type="hidden" name="cid" value="{{$cl->cid()}}">
              <div class="col-md-12 clearfix">
                <div class="pull-right">
                </br>
                  <button type="submit" class="btn btn-danger" value="" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar a cartera"><i class="fa fa-save"></i></button>
                </div>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fcredito fade" id="calendarmodal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Contestar a solicitud de crédito</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="" action="/creditos/reject" method="post">
              <label for="">Contestar solicitud:</label>
              <textarea name="razon" style="overflow-y:auto;" class="form-control" rows="5" placeholder="Escribe la razón de rechazo"></textarea>
              <input type="hidden" name="cid" value="{{$cl->cid()}}">
              <div class="col-md-12 clearfix">
                <div class="pull-right">
                </br>
                  <button type="submit" class="btn btn-danger" value="" data-bs-toggle="tooltip" data-bs-placement="top" title="Rechazar solicitud"><i class="fa fa-save"></i></button>
                </div>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal ffirma fade" id="calendarmodal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Solicitar firma de pagaré</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="" action="/creditos/requestsign" method="post">
              <label for="">Comentario adicional:</label>
              <textarea name="razon" style="overflow-y:auto;" class="form-control" rows="5" placeholder="Escribe un comentario adicional"></textarea>
              <input type="hidden" name="cid" value="{{$cl->cid()}}">
              <div class="col-md-12 clearfix">
                <div class="pull-right">
                </br>
                  <button type="submit" class="btn btn-success" value="" data-bs-toggle="tooltip" data-bs-placement="top" title="Solicitar firma"><i class="fa fa-save"></i></button>
                </div>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal faprobar fade" id="calendarmodal4" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Contestar a solicitud de crédito</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="" action="/creditos/aprobar" method="post">
              <label for="">Notas para el cliente:</label>
              <textarea name="razon" style="overflow-y:auto;" class="form-control" rows="5" placeholder="Escribe una nota para el cliente si la hay"></textarea>
              <input type="hidden" name="cid" value="{{$cl->cid()}}">
              <div class="col-md-12 clearfix">
                <div class="pull-right">
                </br>
                  <button type="submit" class="btn btn-success" value="" data-bs-toggle="tooltip" data-bs-placement="top" title="Pre aprobar solicitud"><i class="fa fa-save"></i></button>
                </div>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>
@endsection
