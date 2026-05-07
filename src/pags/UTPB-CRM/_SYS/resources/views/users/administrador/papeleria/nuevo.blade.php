@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                  <div class="clearfix">
                    <div class="pull-left">
                      <h5>Descarga de formato</h5>
                      <h4>{{$formato["nombre"]}}</h4>
                    </div>
                    <div class="pull-right">
                    </br>
                    </div>
                  </div>
                  <hr>
                    <form class="form-horizontal" method="POST" action="/papeleria/create">
                    <div class="col-md-3">
                          {{ csrf_field() }}
                          <input type="hidden" name="id" value="{{$id}}">
                          <div class="form-group{{ $errors->has('remitente') ? ' has-error' : '' }}">
                              <label for="Destinatario" class="control-label">Remitente</label>

                                  <input disabled type="text" class="form-control large" name="Destinatario" value="{{Auth::user()->level->name}} ({{Auth::user()->name}})">

                                  @if ($errors->has('destinatario'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('destinatario') }}</strong>
                                      </span>
                                  @endif
                          </div>
                          <div class="form-group{{ $errors->has('asunto') ? ' has-error' : '' }}">
                              <label for="Destinatario" class="control-label">Asunto</label>

                                  <input placeholder="Asunto" required id="asunto" type="text" class="form-control large" name="asunto" autofocus>

                                  @if ($errors->has('asunto'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('asunto') }}</strong>
                                      </span>
                                  @endif
                          </div>
                          <div class="form-group{{ $errors->has('destinatario') ? ' has-error' : '' }}">
                              <label for="Destinatario" class="control-label">Destinatario</label>

                                  <input placeholder="Destinatario" id="destinatario" type="text" class="form-control large" name="destinatario" autofocus>

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
                        <i class="fa fa-gear"></i>    Generar referencia
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div>
@endsection
