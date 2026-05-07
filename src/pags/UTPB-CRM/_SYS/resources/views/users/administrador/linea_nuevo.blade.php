@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                    <h3>Modificar linea</h3>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/lineas/create">
                    <div class="col-md-6">
                          {{ csrf_field() }}

                          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                              <label for="name" class="col-md-4 control-label">Encargado</label>

                              <div class="col-md-6">
                                  <input placeholder="Nombre del encargado" value="" required id="name" type="text" class="form-control" name="encargado" autofocus>

                                  @if ($errors->has('name'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('name') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>

                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="email" class="col-md-4 control-label">Correo electrónico</label>

                              <div class="col-md-6">
                                  <input placeholder="Correo electrónico"  id="email" type="email" class="form-control" name="correo">

                                  @if ($errors->has('email'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('email') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>
                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="email" class="col-md-4 control-label">Extensión</label>

                              <div class="col-md-6">
                                  <input placeholder="Extensión" id="email" type="text" class="form-control" name="extension">

                                  @if ($errors->has('email'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('email') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>
                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="email" class="col-md-4 control-label">Departamento</label>

                              <div class="col-md-6">
                                  <input placeholder="Departamento"  id="email" type="text" class="form-control" name="departamento">

                                  @if ($errors->has('email'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('email') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>
                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="email" class="col-md-4 control-label">Teléfono</label>

                              <div class="col-md-6">
                                  <input placeholder="Télefono"  id="email" type="text" class="form-control" name="telefono">

                                  @if ($errors->has('email'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('email') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>

                    </div>
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                          <div class="col-md-9">

                          </div>
                          <div class="col-md-3">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary large">
                                <i class="fa fa-save"></i>    Guardar
                                </button>
                            </div>                  </form>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
