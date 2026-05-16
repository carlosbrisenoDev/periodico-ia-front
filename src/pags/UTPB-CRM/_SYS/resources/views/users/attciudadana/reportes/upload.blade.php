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
                      <h3><a href="/ciudadano/modify/{{md5($ciudadano->id)}}">{{$ciudadano->full_name()}}</a></h3>
                      <h4><a href="/reportes/modify/{{md5($reporte->id)}}">{{$reporte->nombre}}</a></h4>
                      Información de reporte
                    </div>
                    <div class="pull-right">
                    </br>
                    </div>
                  </div>
                  <hr>
                    <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="/documentos/save">
                      <input type="hidden" name="id" value="{{$reporte->id}}">
                      {{ csrf_field() }}

                    <div class="col-md-12 nopadding">
                      <div class="col-md-12 nopadding">
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
                      <div class="col-md-12">
                        <hr>
                        <input type="file" multiple name="documento[]" placeholder="Seleccione los documentos">
                      </div>
                    </div>
                    <div class="col-md-12 nopadding">
                      <hr>
                      <div class="col-md-3">
                        <button type="submit" class="btn btn-primary large">
                        <i class="fa fa-upload"></i>    Subir documentos
                        </button>
                      </form>
                      </div>
                      <div class="col-md-3">
                        
                      </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
