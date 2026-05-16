@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="row">
            <div class="col">
              <div class="card">
                  <div class="card-body">
                    @php
                      $ciudadano = $reporte->usuario;
                    @endphp
                    <h3 class="titulo">Asginación</h3>
                    <form class="form-horizontal" method="POST" action="/tareas/refresh">
                      <div class="row">
                          <input type="hidden" name="id" value="{{$reporte->id}}">
                        <div class="col-12 col-md-12 col-lg-6">
                              {{ csrf_field() }}
                              <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                  <label for="nombre" class="control-label">Nombre</label>

                                      <input placeholder="Nombre" disabled id="nombre" type="text" class="form-control large" name="nombre" value="{{$reporte->nombre}}"  autofocus>

                                      @if ($errors->has('nombre'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('nombre') }}</strong>
                                          </span>
                                      @endif
                              </div>
                              <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                  <label for="email" class="control-label">Solicitante</label>

                                      <input type="email" disabled class="form-control large" value="{{$ciudadano->level->name}}">
                                      @if ($errors->has('email'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('email') }}</strong>
                                          </span>
                                      @endif
                              </div>

                              <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                                  <label for="descripcion" class="control-label">Descripción</label>
                                  <hr>

                                      {!!nl2br($reporte->descripcion)!!}

                                      @if ($errors->has('descripcion'))
                                          <span class="help-block">
                                              <strong>{{ $errors->first('descripcion') }}</strong>
                                          </span>
                                      @endif
                              </div>
                              @if (Auth::user()->level->id == $ciudadano->level->id)

                          <div class="form-group{{ $errors->has('estado_id') ? ' has-error' : '' }}">
                          <label for="name" class="control-label">Estado</label>

                              <select requried class="form-control large" name="estado_id">
                                @foreach (App\estado::all() as $estado)
                                  <option @if($estado->id == $reporte->estado_id) selected @endif value="{{$estado->id}}">{{$estado->nombre}}</option>
                                @endforeach
                              </select>

                              @if ($errors->has('name'))
                                  <span class="help-block">
                                      <strong>{{ $errors->first('name') }}</strong>
                                  </span>
                              @endif
                            </div>
                          @endif

                        </div>
                        <div class="col-12 col-md-12 col-lg-6">
                          <div class="row leftLine">
                            <div class="col-md-12">
                              @if (count($reporte->documentos) > 0)
                                <div class="row">
                                @foreach ($reporte->documentos as $documento)
                                      <div class="card topp col-md-3">
                                        <div class="img img-responsive text-center">
                                          <i class="fa {{$documento->fa()}}"></i>
                                        </div>
                                        <span class="texto">{{str_replace("."," ",$documento->titulo)}}</span>
                                        <div class="row bg-secondary" style="margin:0;">

                                          <div class="col-12">
                                            <div class="input-group">
                                              <div class="input-group-prepend">
                                                <a href="/documentos/download/{{md5($documento->id)}}" class="btn btn-secondary"><i class="fa fa-download"></i></a>
                                                <a target="_blank" href="/documentos/watchar/{{md5($documento->id)}}" class="btn btn-secondary"><i class="fa fa-eye"></i></a>
                                                <a href="/documentos/delete/{{md5($documento->id)}}" class="btn btn-primary"><i class="fa fa-trash"></i></a>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>

                                @endforeach
                                </div>
                                @else
                                  <div class="col-12">
                                    <h4>No hay documentos</h4>
                                  </div>
                              @endif
                            </div>
                            </div>
                          </div>

                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12" style="padding:50px;">
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
                        <div class="row">
                          <hr>
                          @if (Auth::user()->level->id == $ciudadano->level->id)
                          <div class="col-3">
                            <button type="submit" class="btn btn-primary large">
                            <i class="fa fa-refresh"></i>    Actualizar
                            </button>
                          </div>
                        @endif
                        <div class="col-3">
                          <a href="/tareas/upload/{{md5($reporte->id)}}" type="submit" class="btn btn-info large">
                            <i class="fa fa-upload"></i>   Agregar documentos
                          </a>
                        </div>
                        <div class="col-3">
                          <a href="/tareas/notas/{{md5($reporte->id)}}" class="btn btn-success large">
                          <i class="fa fa-pencil"></i>    Notas
                        </a>
                        </div>
                        <div class="col-3">
                          <a target="_blank" href="/tareas/imprimir/{{md5($reporte->id)}}" class="btn btn-warning large">
                          <i class="fa fa-print"></i>    Imprimir
                        </a>
                        </div>
                      </div>
                  </div>
                </form>
              </div>
            </div>
        </div>
@endsection
