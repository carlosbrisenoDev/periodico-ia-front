@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                  <div class="clearfix">
                    <div class="pull-left">
                      <h3>Nuevo archivo</h3>
                    </div>
                    <div class="pull-right">
                    </br>
                    </div>
                  </div>
                  <hr>
                    <form class="form-horizontal" method="POST" action="/formatos/createdescarga">
                    <div class="col-md-3">
                          {{ csrf_field() }}
                          <div class="form-group{{ $errors->has('asunto') ? ' has-error' : '' }}">
                              <label for="Destinatario" class="control-label">Título del archivo</label>

                                  <input placeholder="Título" required id="asunto" type="text" class="form-control large" name="asunto" autofocus>

                                  @if ($errors->has('asunto'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('asunto') }}</strong>
                                      </span>
                                  @endif
                          </div>
                          <div class="form-group{{ $errors->has('destinatario') ? ' has-error' : '' }}">
                              <label for="Destinatario" class="control-label">Descripción</label>

                                  <input placeholder="Descripción" id="destinatario" type="text" class="form-control large" name="destinatario" autofocus>

                                  @if ($errors->has('destinatario'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('destinatario') }}</strong>
                                      </span>
                                  @endif
                          </div>
                    </div>
                    <div class="col-md-12 nopadding">
                      <hr>
                      <div class="col-md-3 nopadding">
                        <button type="submit" class="btn btn-primary large">
                        <i class="fa fa-gear"></i>    Generar referencia de archivo
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div>
@endsection
