@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <h3><a href="/reportes/modify/{{md5($reporte->id)}}">{{$reporte->nombre}}</a></h3>
                    <h4>Notas</h4>
                    <hr>
                    <form class="form-horizontal" method="POST" action="/reportes/write">
                      {{ csrf_field() }}
                      <input type="hidden" name="reporte_id" value="{{$reporte->id}}">
                      <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <div class="col-md-12">
                                <div class="col-md-6 nopadding">
                                  @foreach (\App\nota::where('reporte_id',$reporte->id)->orderBy('id','desc')->get() as $nota)
                                    <div class="leftLine">
                                      <b>{{$nota->usuario->level->name}}</b>
                                      <p align)="justify">
                                        {{$nota->nota}}
                                      </p>
                                      <p align="right">{{$nota->full_fecha()}}</p>
                                    </div>
                                    <hr>
                                  @endforeach
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6 nopadding">
                                  <textarea name="nota" required placeholder="Escriba aquí la nota" class="form-control large"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                              <hr>
                              <div class="col-md-2 nopadding">
                                <button type="submit" class="btn btn-primary large">
                                <i class="fa fa-save"></i>    Guardar
                                </button>
                              </div>
                            </div>
                              @if ($errors->has('email'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('email') }}</strong>
                                  </span>
                              @endif
                      </div>
                    </form>
                </div>
            </div>
        </div>
@endsection
