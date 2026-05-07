@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header">
          Solicitud de empleado
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-12 col-lg-6 jumbotron">
              <h4>Información del solicitante</h4>
              <div class="row">
                  <div class="col-8">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" name="" class="form-control" value="{{$empleado->nombre}}">
                  </div>
                  <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                    <label for="nombre">Fecha de solicitud</label>
                    <input type="text" name="" class="form-control" value="{{$empleado->created_at}}">
                  </div>
              </div>

              <div class="row">
                  <div class="col-2">
                    <label for="nombre">Edad</label>
                    <input type="text" name="" class="form-control" value="{{$empleado->edad}}">
                  </div>
                  <div class="col-10">
                    <label for="nombre">Correo electrónico  </label>
                    <input type="text" name="" class="form-control" value="{{$empleado->correo}}">
                  </div>
              </div>

              <div class="row">
                  <div class="col-12 col-md-12 col-lg-6">
                    <label for="nombre">Estado civil</label>
                    <input type="text" name="" class="form-control" value="{{$empleado->estadocivil}}">
                  </div>
                  <div class="col-12 col-md-12 col-lg-6">
                    <label for="nombre">Hijos  </label>
                    <input type="text" name="" class="form-control" value="{{$empleado->hijos}}">
                  </div>
              </div>

              <div class="row">
                  <div class="col-12 col-md-12 col-lg-6">
                    <label for="nombre">Nivel de estudios</label>
                    <input type="text" name="" class="form-control" value="{{$empleado->nivelestudios}}">
                  </div>
                  <div class="col-12 col-md-12 col-lg-6">
                    <label for="nombre">Especidalidad  </label>
                    <input type="text" name="" class="form-control" value="{{$empleado->especialidad}}">
                  </div>
              </div>

              <div class="row">
                  <div class="col-12 col-md-12 col-lg-6">
                    <label for="nombre">Experiencia</label>
                    <input type="text" name="" class="form-control" value="{{$empleado->experiencia}}">
                  </div>
                  <div class="col-12 col-md-12 col-lg-6">
                    <label for="nombre">Puesto(s) buscado</label>
                    <input type="text" name="" class="form-control" value="{{$empleado->puesto}}">
                  </div>
              </div>

              <div class="row">
                <div class="col-12 col-md-12 col-lg-6">
                  <label for="nombre">Dirección  </label>
                  <input type="text" name="" class="form-control" value="{{$empleado->direccion}}">
                </div>
                  <div class="col-12 col-md-12 col-lg-6">
                    <label for="nombre">Nacionalidad</label>
                    <input type="text" name="" class="form-control" value="{{$empleado->nacionalidad}}">
                  </div>
              </div>

              <div class="row">
                  <div class="col-12 col-md-12 col-lg-6">
                    <label for="nombre">Ciudad</label>
                    <input type="text" name="" class="form-control" value="{{$empleado->ciudad}}">
                  </div>
                  <div class="col-12 col-md-12 col-lg-6">
                    <label for="nombre">Estado  </label>
                    <input type="text" name="" class="form-control" value="{{$empleado->estado}}">
                  </div>
              </div>

              <div class="row">
                  <div class="col-12 col-md-12 col-lg-6">
                    <label for="nombre">Teléfono</label>
                    <input type="text" name="" class="form-control" value="{{$empleado->telefono}}">
                  </div>
                  <div class="col-12 col-md-12 col-lg-6">

                  </div>
              </div>

              <div class="row">
                  <div class="col-12">
                    <label for="nombre">¿Cómo nos conoció?</label>
                    <input type="text" name="" class="form-control" value="{{$empleado->como}}">
                  </div>
              </div>

              <div class="row">
                  <div class="col-12">
                    <label for="nombre">¿Por qué quiere trabajar con nosotros?</label>
                    <input type="text" name="" class="form-control" value="{{$empleado->porque}}">
                  </div>
              </div>

            </div>
            <div class="col-12 col-md-12 col-lg-6">
              <h4>Documentos anexos</h4>
              <div class="row">
                  @if (count($empleado->documentos) > 0)
                  @foreach ($empleado->documentos as $documento)
                    <div class="col-12 col-md-12 col-lg-6">
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
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                  @endforeach
                  @else
                    <div class="col-2">
                      <div class="card topp">
                        <div class="img img-responsive text-center">
                          <div class="seleccionar">
                          </div>
                        </div>
                        <span class="texto">No hay documentos</span>
                        <div class="row bg-secondary" style="margin:0;height:38px;">

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
          <div class="row">
            <div class="col">
              @if ($empleado->status == 4)
                  <a href="/empleados/desarchivar/{{md5($empleado->id)}}" class="btn btn-primary"><i class="fa fa-archive"></i> Desarchivar</a>
                @elseif($empleado->status == 5)
                  <a href="/empleados/despedir/{{md5($empleado->id)}}" class="btn btn-primary"><i class="fa fa-fire"></i> Dar de baja</a>
                @elseif($empleado->status == 6)
                  Este empleado fue dado de baja del sistema
                @else
                    <a href="/empleados/archivar/{{md5($empleado->id)}}" class="btn btn-primary"><i class="fa fa-archive"></i> Archivar</a>
                    <a href="/empleados/rechazar/{{md5($empleado->id)}}" class="btn btn-secondary"> <i class="fa fa-fire"></i> Rechazar</a>
                    <a href="/empleados/contratar/{{md5($empleado->id)}}" class="btn btn-primary"> <i class="fa fa-check"></i> Dar alta</a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
