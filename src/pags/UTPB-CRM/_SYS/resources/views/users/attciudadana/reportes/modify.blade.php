@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                  <div class="clearfix">
                    <div class="pull-left">
                      @php
                        $ciudadano = $reporte->ciudadano;
                      @endphp
                      <h3><a href="/ciudadano/modifyr/{{md5($ciudadano->id)}}">{{$ciudadano->full_name()}}</a></h3>
                      <h4>Información</h4>
                    </div>
                    <div class="pull-right">
                    </br>
                    </div>
                  </div>
                  <hr>
                    <form class="form-horizontal" method="POST" action="/reportes/refresh">
                      <input type="hidden" name="id" value="{{$reporte->id}}">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-3">
                              {{ csrf_field() }}
                              <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                  <label for="nombre" class="control-label">Folio</label>
                                      <input placeholder="Nombre" disabled id="nombre" type="text" class="form-control large"  value="{{$reporte->id}}"  autofocus>
                              </div>
                              <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                  <label for="nombre" class="control-label">Nombre</label>

                                      <input placeholder="Nombre" disabled id="nombre" type="text" class="form-control large" name="nombre" value="{{$reporte->nombre}}"  autofocus>

                                      @if ($errors->has('nombre'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('nombre') }}</strong>
                                          </span>
                                      @endif
                              </div>
                              <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                  <label for="nombre" class="control-label">Código</label>

                                      <input placeholder="Nombre" disabled id="codigo" type="text" class="form-control large" name="nombre" value="{{$reporte->ciudadano->codigo}}"  autofocus>

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
                                  <label for="descripcion" class="control-label">Descripción</label>

                                      <textarea class="form-control large" placeholder="Descripción" name="descripcion" required autofocus>{{$reporte->descripcion}}</textarea>

                                      @if ($errors->has('descripcion'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('descripcion') }}</strong>
                                          </span>
                                      @endif
                              </div>


                          <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                              <label for="name" class="control-label">Dirigido a</label>

                                  <select requried class="form-control large" name="level_id">
                                    @foreach (App\level::where('nivel','>',1)->get() as $level)
                                      <option @if($level->id == $reporte->level_id) selected @endif value="{{$level->id}}">{{$level->name}}</option>
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
                                      <option @if($prioridad->id == $reporte->prioridad_id) selected @endif value="{{$prioridad->id}}">{{$prioridad->nombre}}</option>
                                    @endforeach
                                  </select>

                                  @if ($errors->has('name'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('name') }}</strong>
                                      </span>
                                  @endif
                          </div>
                        </div>
                        <div class="col-md-9">
                          <div class="col-md-12 leftLine">
                            <div class="col-md-12">
                              @if (count($reporte->documentos) > 0)
                                @foreach ($reporte->documentos as $documento)
                                  <div class="col-md-2">
                                    <div class="thumbnail topp">
                                      <div class="img img-responsive text-center">
                                        <i class="fa {{$documento->fa()}}"></i>
                                      </div>
                                      <span class="texto">{{str_replace("."," ",$documento->titulo)}}</span>
                                      <div class="clearfix">
                                        <div class="col-md-4 nopadding">
                                          <a href="/documentos/download/{{md5($documento->id)}}" class="btn btn-default"><i class="fa fa-download"></i></a>
                                        </div>
                                        <div class="col-md-4 nopadding">
                                          <a target="_blank" href="/documentos/watchar/{{md5($documento->id)}}" class="btn btn-default"><i class="fa fa-eye"></i></a>
                                        </div>
                                        <div class="col-md-4 nopadding">
                                          <a href="/documentos/trash/{{md5($documento->id)}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                        </div>
                                      </div>
                                    </div>

                                  </div>
                                @endforeach
                                @else
                                  <div class="col-md-12">
                                    <h4>No hay documentos</h4>
                                  </div>
                              @endif
                            </div>
                          </div>
                          <div class="col-md-12">
                            <hr>
                            Última nota agregada
                              @php
                                $nota = \App\nota::where('reporte_id',$reporte->id)->orderBy('id','desc')->first();
                              @endphp
                              @if ($nota != null)
                                <div class="leftLine">
                                  <b>{{$nota->usuario->level->name}}</b>
                                  <p align)="justify">
                                    {{$nota->nota}}
                                  </p>
                                  <p align="right">{{$nota->full_fecha()}}</p>
                                </div>
                                <hr>
                              @endif
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 nopadding">
                      <hr>
                      <div class="row">
                        <div class="col-md-3">
                          <button type="submit" class="btn btn-primary large">
                          <i class="fa fa-refresh"></i>    Actualizar
                          </button>
                        </form>
                        </div>
                        <div class="col-md-3">
                          <a href="/reportes/uploadr/{{md5($reporte->id)}}" type="submit" class="btn btn-success large">
                            <i class="fa fa-upload"></i>   Agregar documentos
                          </a>
                        </div>
                        <div class="col-md-3">
                          <a href="/reportes/notasr/{{md5($reporte->id)}}" class="btn btn-success large">
                          <i class="fa fa-pencil"></i>    Notas
                        </a>
                        </div>
                        <div class="col-md-3">
                          <a target="_blank" href="/reportes/imprimirr/{{md5($reporte->id)}}" class="btn btn-warning large">
                          <i class="fa fa-print"></i>    Imprimir
                        </a>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
