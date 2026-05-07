@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="row">
            <div class="col">
              <div class="card">
                <div class="card-header">
                  <h3>Sucursal</h3>
                </div>
                  <div class="card-body">
                    @php
                      $sucursal = Auth::user()->franquicia->sucursal;
                    @endphp
                    <form class="form-horizontal" method="POST" action="/sucursales/actualizar">
                      <div class="row">
                        <div class="col-12 col-md-12 col-lg-6">
                              {{ csrf_field() }}

                              <div class="{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                  <label for="nombre" class="control-label">Nombre de la sucursal</label>

                                      <input placeholder="Nombre" required value="{{$sucursal->nombre}}" id="nombre" type="text" class="form-control large" name="nombre" required autofocus>

                                      @if ($errors->has('nombre'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('nombre') }}</strong>
                                          </span>
                                      @endif
                              </div>

                        </div>
                        <div class="col-12 col-md-12 col-lg-6">
                          <div class="{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="email" class="control-label">Correo electrónico de contacto</label>

                                  <input type="email" name="correo" class="form-control large" value="{{$sucursal->correo}}" required  placeholder="contacto{{"@"}}{{$_SERVER['HTTP_HOST']}}">
                                  <input type="hidden" name="sucursal_id" value="{{$sucursal->id}}">
                                  @if ($errors->has('email'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('email') }}</strong>
                                      </span>
                                  @endif
                          </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12 col-md-12 col-lg-6">

                            <div class="{{ $errors->has('direccion') ? ' has-error' : '' }}">
                                <label for="direccion" class="control-label">
                                  Dirección del local
                                </label>
                                    <textarea class="form-control large" style="height:150px;" placeholder="Dirección" name="direccion" required autofocus>{{$sucursal->direccion}}</textarea>

                                    @if ($errors->has('direccion'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('direccion') }}</strong>
                                        </span>
                                    @endif
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-6">
                          <div class="{{ $errors->has('telefono') ? ' has-error' : '' }}">
                              <label for="telefono" class="control-label">
                                Teléfonos (Separados por ,)
                              </label>
                                  <textarea class="form-control large" style="height:150px;" placeholder="555 000 00 00, 555 111 11 11, ..." name="telefono" required autofocus>{{$sucursal->telefono}}</textarea>

                                  @if ($errors->has('telefono'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('telefono') }}</strong>
                                      </span>
                                  @endif
                          </div>
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-12 col-md-12 col-lg-6">
                        <div class="{{ $errors->has('horario') ? ' has-error' : '' }}">
                            <label for="horario" class="control-label">
                              Horario
                            </label>
                                <textarea class="form-control large" style="height:150px;" placeholder="Lunes: 8:00AM - 8:00 PM" name="horario" required autofocus>{{$sucursal->horario}}</textarea>

                                @if ($errors->has('horario'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('horario') }}</strong>
                                    </span>
                                @endif
                        </div>
                      </div>
                      <div class="col-12 col-md-12 col-lg-6">
                        <div class="{{ $errors->has('iframe') ? ' has-error' : '' }}">
                            <label for="iframe" class="control-label">
                              URL Google Maps
                            </label>
                                <textarea class="form-control large" style="height:150px;" placeholder="http://..." name="iframe" required autofocus>{{$sucursal->iframe}}</textarea>

                                @if ($errors->has('iframe'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('iframe') }}</strong>
                                    </span>
                                @endif
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                      </br>
                        <input type="checkbox" {{($sucursal->visible) ? "checked" : ""}} name="visible" value="1" id="visible">
                        <label for="visible">¿Hacer visible en http://{{$_SERVER['HTTP_HOST']}}?</label>
                      </div>
                    </div>
                    <div class="col–12">
                      <hr>
                      <div class="col-md-3 nopadding">
                        <button type="submit" class="btn btn-primary large">
                        <i class="fa fa-save"></i>    Actualizar
                        </button>
                      </div>
                    </div>
                  </form>
              </div>
            </div>
        </div>
@endsection
