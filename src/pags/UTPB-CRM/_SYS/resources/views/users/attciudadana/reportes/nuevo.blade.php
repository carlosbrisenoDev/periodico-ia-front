@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                  <div class="clearfix">
                    <div class="pull-left">
                      <h3><a href="/ciudadano/modify/{{md5($ciudadano->id)}}">{{$ciudadano->full_name()}}</a></h3>
                      <h4>Nueva Solicitud</h4>
                    </div>
                    <div class="pull-right">
                    </br>
                    </div>
                  </div>
                  <hr>
                    <form class="form-horizontal" method="POST" action="/reportes/creater">
                    <div class="col-md-6">
                          {{ csrf_field() }}

                          <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                              <label for="nombre" class="control-label">Título</label>

                                  <input placeholder="Nombre" required id="nombre" type="text" class="form-control large" name="nombre" required autofocus>

                                  @if ($errors->has('nombre'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('nombre') }}</strong>
                                      </span>
                                  @endif
                          </div>

                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <label for="email" class="control-label">Ciudadano</label>

                                  <input type="email" disabled class="form-control large" value="{{$ciudadano->full_name()}}">
                                  <input type="hidden" name="ciudadano_id" value="{{$ciudadano->id}}">
                                  @if ($errors->has('email'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('email') }}</strong>
                                      </span>
                                  @endif
                          </div>

                          <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                              <label for="descripcion" class="control-label">Describa a detalle lo que el ciudadano solicita, dirección dónde se requiere la atención, causa, etc.</label>

                                  <textarea class="form-control large" style="height:300px;" placeholder="Descripción" name="descripcion" required autofocus></textarea>

                                  @if ($errors->has('descripcion'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('descripcion') }}</strong>
                                      </span>
                                  @endif
                          </div>


                      <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                          <label for="name" class="control-label">Dirigido a</label>

                              <select requried class="form-control large" name="level_id">
                                @foreach (App\level::where('nivel','>',Auth::user()->level->nivel)->get() as $level)
                                  <option value="{{$level->id}}">{{$level->name}}</option>
                                @endforeach
                              </select>

                              @if ($errors->has('name'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('name') }}</strong>
                                  </span>
                              @endif
                      </div>
                      <div class="form-group{{ $errors->has('prioridad_id') ? ' has-error' : '' }}">
                          <label for="name" class="control-label">Prioridad</label>

                              <select requried class="form-control large" name="prioridad_id">
                                @foreach (App\prioridad::all() as $prioridad)
                                  <option value="{{$prioridad->id}}">{{$prioridad->nombre}}</option>
                                @endforeach
                              </select>

                              @if ($errors->has('name'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('name') }}</strong>
                                  </span>
                              @endif
                      </div>
                    </div>
                    <div class="col-md-12 nopadding">
                      <hr>
                      <div class="col-md-3 nopadding">
                        <button type="submit" class="btn btn-primary large">
                        <i class="fa fa-save"></i>    Guardar
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div>
@endsection
