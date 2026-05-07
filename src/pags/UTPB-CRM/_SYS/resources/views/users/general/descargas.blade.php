@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                  <div class="clearfix">
                    <div class="pull-left">
                      <h3>Gestor de descargas</h3>
                    </div>
                    <div class="pull-right">
                    </div>
                  </div>
                  <hr>
                  @php
                    $max = 20;
                    $formatos  = \App\formato::where('level_id',\App\level::where('alias','comunicacion')->first()->id)->where('tipo','2')->paginate($max);
                    $pull = "right";
                    $entrys = $formatos;
                    $where = "branding";
                    $nav = $entrys;
                  @endphp
                  @if (count($formatos) > 0)
                    @include('componentes.navegacion')
                    <table class="table table-responsive">
                      <tr class="bg-primary">
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th></th>
                        <th></th>
                      </tr>
                      @foreach ($formatos as $id => $documento)
                        <tr>
                          <td>{{$documento->asunto}}</td>
                          <td>{{$documento->destino}}</td>
                          <td>{{$documento->full_fecha()}}</td>
                          <td>
                            <div class="pull-right">
                              @if ($documento->archivo == "0")
                                  Descarga no disponible
                                @else
                                  <a class="btn btn-default" href="/formatos/subir/{{md5($documento->id)}}"><i class="fa fa-download"></i> Descargar archivo</a>
                              @endif
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </table>
                  @else
                    <h4>No se han subido archivos</h4>
                  @endif
                </div>
            </div>
        </div>
@endsection
