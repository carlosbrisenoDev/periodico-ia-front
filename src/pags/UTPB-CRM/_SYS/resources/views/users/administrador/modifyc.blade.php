@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                    <h3>Información del ciudadano</h3>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/ciudadano/refresh">
                      <input type="hidden" name="id" value="{{$ciudadano->id}}">
                      {{ csrf_field() }}
                    <div class="col-md-12">
                      <div class="col-md-4 pad15">
                              {{ csrf_field() }}
                              <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                  <label for="nombre" class="control-label">Código de ciudadano</label>

                                      <input placeholder="Nombre" disabled id="codigo" type="text" class="form-control large" name="nombre" value="{{$ciudadano->codigo}}"  autofocus>

                                      @if ($errors->has('nombre'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('nombre') }}</strong>
                                          </span>
                                      @endif
                              </div>
                              <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                  <label for="name" class=" control-label">Nombre(s)</label>

                                      <input placeholder="Nombre(s)" disabled value="{{$ciudadano->nombre}}" required id="name" type="text" class="form-control large" name="nombre"  autofocus>

                                      @if ($errors->has('nombre'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('name') }}</strong>
                                          </span>
                                      @endif
                              </div>
                              <div class="form-group{{ $errors->has('apellidopat') ? ' has-error' : '' }}">
                                  <label for="apellidopat" class=" control-label">Apellido paterno</label>

                                      <input placeholder="Apellido paterno" disabled value="{{$ciudadano->apellidopat}}" required id="apellidopat" type="text" class="form-control large" name="apellidopat" autofocus>

                                      @if ($errors->has('apellidopat'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('apellidopat') }}</strong>
                                          </span>
                                      @endif
                              </div>
                              <div class="form-group{{ $errors->has('apellidomat') ? ' has-error' : '' }}">
                                  <label for="apellidomat" class=" control-label">Apellido materno</label>

                                      <input placeholder="Apellido materno" disabled value="{{$ciudadano->apellidomat}}" required id="apellidomat" type="text" class="form-control large" name="apellidomat"  autofocus>

                                      @if ($errors->has('apellidomat'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('apellidomat') }}</strong>
                                          </span>
                                      @endif
                              </div>



                      </div>
                      <div class="col-md-4 pad15">
                        <div class="form-group{{ $errors->has('telefono') ? ' has-error' : '' }}">
                            <label for="telefono" class=" control-label">Teléfono</label>

                                <input placeholder="Teléfono" required id="telefono" value="{{$ciudadano->telefono}}" type="text" class="form-control large" name="telefono"  autofocus>

                                @if ($errors->has('telefono'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('cp') ? ' has-error' : '' }}">
                            <label for="cp" class=" control-label">Código postal</label>

                                <input placeholder="Código postal" required id="cp" value="{{$ciudadano->cp}}" type="text" class="form-control large" name="cp"  autofocus>

                                @if ($errors->has('cp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cp') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">Correo electrónico</label>

                                <input placeholder="Correo electrónico" id="email" value="{{$ciudadano->email}}" type="email" class="form-control large" name="email">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <div class="form-group{{ $errors->has('localidad') ? ' has-error' : '' }}">
                            <label for="localidad" class=" control-label">Localidad</label>

                                <input placeholder="Localidad" required id="localidad" type="text" value="{{$ciudadano->localidad}}" class="form-control large" name="localidad" autofocus>

                                @if ($errors->has('localidad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('localidad') }}</strong>
                                    </span>
                                @endif
                        </div>
                      </div>
                      <div class="col-md-4 pad15">

                        <div class="form-group{{ $errors->has('curp') ? ' has-error' : '' }}">
                            <label for="CURP" class=" control-label">CURP</label>

                                <input placeholder="CURP" disabled value="{{$ciudadano->curp}}" required id="CURP" type="text" class="form-control large" name="curp"  autofocus>

                                @if ($errors->has('CURP'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('CURP') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}">
                            <label for="fecha_nacimiento" class=" control-label">Fecha de nacimiento</label>

                                <input placeholder="Fecha de nacimiento" disabled value="{{$ciudadano->fecha_nacimiento}}" required id="fecha_nacimiento" type="text" class="form-control large" name="fecha_nacimiento"  autofocus>

                                @if ($errors->has('fecha_nacimiento'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('numero') ? ' has-error' : '' }}">
                            <label for="numero" class=" control-label">Número</label>

                                <input placeholder="Número" required id="numero" type="text" value="{{$ciudadano->numero}}" class="form-control large" name="numero" autofocus>

                                @if ($errors->has('numero'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('numero') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('numeroint') ? ' has-error' : '' }}">
                            <label for="numeroint" class=" control-label">Número int.</label>

                                <input placeholder="Número int." required id="numeroint" type="text" value="{{$ciudadano->numeroint}}" class="form-control large" name="numeroint" autofocus>

                                @if ($errors->has('numeroint'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('numeroint') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="control-label">Dirección</label>

                                <textarea placeholder="Dirección" required id="direccion" class="form-control large" name="direccion" >{{$ciudadano->direccion}}</textarea>

                                @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('colonia') ? ' has-error' : '' }}">
                            <label for="colonia" class=" control-label">Colonia</label>

                                <input placeholder="Colonia" required id="colonia" value="{{$ciudadano->colonia}}" type="text" class="form-control large" name="colonia"  autofocus>

                                @if ($errors->has('colonia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('colonia') }}</strong>
                                    </span>
                                @endif
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <hr>
                        <div class="col-md-3">
                          <button type="submit" class="btn btn-primary large">
                          <i class="fa fa-refresh"></i>    Actualizar
                          </button>
                        </form>
                        </div>
                        <div class="col-md-3">
                          <form class="form-horizontal" method="POST" action="/ciudadano/trash">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$ciudadano->id}}">
                          <button type="submit" class="btn btn-danger large">
                          <i class="fa fa-trash"></i>   Borrar
                          </button>
                        </form>
                        </div>
                        <div class="col-md-3">
                          <a href="/ciudadano/reportes/{{md5($ciudadano->id)}}" class="btn btn-primary large">
                          <i class="fa fa-list"></i>    Reportes
                        </a>
                        </div>
                        <div class="col-md-3">
                          <a href="/tarjeta/modify/{{md5($ciudadano->id)}}" class="btn btn-success large">
                          <i class="fa fa-address-card"></i>    Credencial
                        </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
