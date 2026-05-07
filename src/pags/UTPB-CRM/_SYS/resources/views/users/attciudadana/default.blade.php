@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                  <div class="col-md-12">
                        <h3>Reportes</h3>
                        @php
                          $datos = \App\estado::all();
                        @endphp
                        @foreach ($datos as $estado)
                          @if($datos[count($datos)-1] != $estado)
                            <hr>
                            Reportes <i>{{$estado->nombre}}</i>
                            <table class="table table-responsive">
                              @php
                                $reportes = \App\reporte::where('estado_id',$estado->id)
                                ->where('level_id',Auth::user()->level->id)
                                ->get();
                              @endphp
                              @if (count($reportes) > 0)
                                <table class="table table-responsive table-stripped">
                                  <tr>
                                    <th><b>Folio</b></th>
                                    <th>Título del reporte</th>
                                    <th>Dirigido a</th>
                                    <th>Estado</th>
                                    <th>Fecha de creación</th>
                                  </tr>
                                  @foreach ($reportes as $reporte)
                                    <tr>
                                      <td>{{$reporte->id}}</td>
                                      <td><a href="/reportes/modify/{{md5($reporte->id)}}">{{$reporte->nombre}}</a></td>
                                      <td>{{$reporte->level->name}}</td>
                                      <td>{{$reporte->estado->nombre}}</td>
                                      <td>{{$reporte->full_fecha()}}</td>
                                    </tr>
                                  @endforeach
                                </table>
                                @else
                                  <h4>No hay resultados</h4>
                              @endif
                            </table>
                          @endif
                        @endforeach
                        <hr>
                    <h3>Registrar ciudadano</h3>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/ciudadano/create">
                      {{ csrf_field() }}
                    <div class="col-md-12">
                      <div class="col-md-4 pad15">
                              {{ csrf_field() }}

                              <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                  <label for="name" class=" control-label">Nombre(s)</label>

                                      <input placeholder="Nombre(s)" required id="name" type="text" class="form-control large" name="nombre" value="{{ old('nombre') }}" autofocus>

                                      @if ($errors->has('nombre'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('name') }}</strong>
                                          </span>
                                      @endif
                              </div>
                              <div class="form-group{{ $errors->has('apellidopat') ? ' has-error' : '' }}">
                                  <label for="apellidopat" class=" control-label">Apellido paterno</label>

                                      <input placeholder="Apellido paterno" required id="apellidopat" type="text" class="form-control large" name="apellidopat" value="{{ old('apellidopat') }}" autofocus>

                                      @if ($errors->has('apellidopat'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('apellidopat') }}</strong>
                                          </span>
                                      @endif
                              </div>
                              <div class="form-group{{ $errors->has('apellidomat') ? ' has-error' : '' }}">
                                  <label for="apellidomat" class=" control-label">Apellido materno</label>

                                      <input placeholder="Apellido materno" required id="apellidomat" type="text" class="form-control large" name="apellidomat" value="{{ old('apellidomat') }}" autofocus>

                                      @if ($errors->has('apellidomat'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('apellidomat') }}</strong>
                                          </span>
                                      @endif
                              </div>
                              <div class="form-group{{ $errors->has('curp') ? ' has-error' : '' }}">
                                  <label for="CURP" class=" control-label">CURP</label>

                                      <input placeholder="CURP" id="CURP" type="text" class="form-control large" name="curp" value="{{ old('CURP') }}" autofocus>

                                      @if ($errors->has('CURP'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('CURP') }}</strong>
                                          </span>
                                      @endif
                              </div>
                              <div class="form-group{{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}">
                                  <label for="fecha_nacimiento" class=" control-label">Fecha de nacimiento</label>

                                      <input placeholder="Fecha de nacimiento" required id="fecha_nacimiento" type="text" class="form-control large" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" autofocus>

                                      @if ($errors->has('fecha_nacimiento'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('fecha_nacimiento') }}</strong>
                                          </span>
                                      @endif
                              </div>



                      </div>
                      <div class="col-md-4 pad15">
                        <div class="form-group{{ $errors->has('telefono') ? ' has-error' : '' }}">
                            <label for="telefono" class=" control-label">Teléfono</label>

                                <input placeholder="Teléfono" required id="telefono" type="text" class="form-control large" name="telefono" value="{{ old('telefono') }}" autofocus>

                                @if ($errors->has('telefono'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('cp') ? ' has-error' : '' }}">
                            <label for="cp" class=" control-label">Código postal</label>

                                <input placeholder="Código postal" required id="cp" type="text" class="form-control large" name="cp" value="{{ old('cp') }}" autofocus>

                                @if ($errors->has('cp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cp') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">Correo electrónico</label>

                                <input placeholder="Correo electrónico" id="email" type="email" class="form-control large" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <div class="form-group{{ $errors->has('localidad') ? ' has-error' : '' }}">
                            <label for="localidad" class=" control-label">Localidad</label>

                                <input placeholder="Localidad" required id="localidad" type="text" class="form-control large" name="localidad" value="{{ old('localidad') }}" autofocus>

                                @if ($errors->has('localidad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('localidad') }}</strong>
                                    </span>
                                @endif
                        </div>

                                                <div class="form-group{{ $errors->has('colonia') ? ' has-error' : '' }}">
                                                    <label for="colonia" class=" control-label">Colonia</label>

                                                        <input placeholder="Colonia" required id="colonia" type="text" class="form-control large" name="colonia" value="{{ old('colonia') }}" autofocus>

                                                        @if ($errors->has('colonia'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('colonia') }}</strong>
                                                            </span>
                                                        @endif
                                                </div>
                      </div>
                      <div class="col-md-4 pad15">

                        <div class="form-group{{ $errors->has('numero') ? ' has-error' : '' }}">
                            <label for="numero" class=" control-label">Número</label>

                                <input placeholder="Número" required id="numero" type="text" class="form-control large" name="numero" value="{{ old('numero') }}" autofocus>

                                @if ($errors->has('numero'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('numero') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('numeroint') ? ' has-error' : '' }}">
                            <label for="numeroint" class=" control-label">Número int.</label>

                                <input placeholder="Número int." required id="numeroint" type="text" class="form-control large" name="numeroint" value="{{ old('numeroint') }}" autofocus>

                                @if ($errors->has('numeroint'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('numeroint') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="control-label">Dirección</label>

                                <textarea placeholder="Dirección" required id="direccion" class="form-control large" name="direccion" value="{{ old('direccion') }}"></textarea>

                                @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                @endif
                        </div>
                        <div class="form-group">
                          <button type="submit" class="btn btn-primary large">
                          <i class="fa fa-save"></i>    Guardar
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div>
@endsection
