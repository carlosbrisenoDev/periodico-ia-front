@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                    <h3>Modificar área</h3>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/levels/update">
                    <div class="col-md-6">
                          {{ csrf_field() }}

                          <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                              <label for="name" class="col-md-4 control-label">Nombre del área</label>
                              <input type="hidden" name="id" value="{{$level->id}}">
                              <div class="col-md-6">
                                  <input placeholder="Nombre del área" required id="name" type="text" class="form-control large" name="name" value="{{$level->name}}" required autofocus>

                                  @if ($errors->has('name'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('name') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>

                          <div class="form-group{{ $errors->has('nivel') ? ' has-error' : '' }}">
                              <label for="nivel" class="col-md-4 control-label">Nivel</label>

                              <div class="col-md-6">
                                  <input placeholder="Nivel" required id="nivel" type="text" class="form-control large" name="nivel" value="{{$level->nivel}}" required>

                                  @if ($errors->has('nivel'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('nivel') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>

                          <div class="form-group{{ $errors->has('alias') ? ' has-error' : '' }}">
                              <label for="alias" class="col-md-4 control-label">Módulo </label>

                              <div class="col-md-6">
                                  <input disabled id="alias" type="text" class="form-control large" name="alias" value="{{$level->alias}}">
                              </div>
                          </div>
                      <div class="form-group">
                          <div class="col-md-6 col-md-offset-4">
                              <button type="submit" class="btn btn-primary large">
                              <i class="fa fa-save"></i>    Actualizar
                              </button>
                          </div>
                          <a class="btn btn-danger" href="/levels/delete/{{md5($level->id)}}"> <i class="fa fa-trash"></i> Eliminar</a>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div>
@endsection
