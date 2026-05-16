@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                  <div class="clearfix">
                    <div class="pull-left">
                      <h3>Oficios</h3>
                    </div>
                    <div class="pull-right">
                      <a href="/juridico/oficio" class="btn btn-primary"><i class="fa fa-plus"></i> Crear</a>
                    </div>
                  </div>
                  <hr>
                  @php
                    $max = 20;
                    $formatos  = \App\formato::where('level_id',Auth::user()->level->id)->where('tipo','1')->paginate($max);
                    $pull = "right";
                    $entrys = $formatos;
                    $where = "branding";
                    $nav = $entrys;
                  @endphp
                  @if (count($formatos) > 0)
                    @include('componentes.navegacion')
                    <table class="table table-responsive">
                      <tr class="bg-primary">
                        <th>Asunto</th>
                        <th>Origen</th>
                        <th>Destinatario</th>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th></th>
                      </tr>
                      @foreach ($formatos as $id => $documento)
                        <tr>
                          <td>{{$documento->asunto}}</td>
                          <td>{{Auth::user()->level->name}} ({{\App\User::find($documento->user_id)->name}})</td>
                          <td>{{$documento->destino}}</td>
                          <td>{{"JUR-".str_pad($documento->referencia, 4, "0", STR_PAD_LEFT)."/".Date("Y")}}</td>
                          <td>{{$documento->full_fecha()}}</td>
                          <td>
                            @if ($documento->archivo == "0")
                                <a href="/formatos/subir/{{md5($documento->id)}}" style="color:red;"><i class="fa fa-upload"></i> (Subir documento escaneado)</a>
                              @else
                                <a href="/formatos/subir/{{md5($documento->id)}}"><i class="fa fa-download"></i> Descargar documento escaneado</a>
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    </table>
                  @else
                    <h4>No se han generado folios</h4>
                  @endif
                </div>
            </div>
        </div>
@endsection
