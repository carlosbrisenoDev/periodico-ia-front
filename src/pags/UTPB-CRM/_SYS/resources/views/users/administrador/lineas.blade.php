@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="">
            <div class="card">
              <div class="card-header">
                <div class="clearfix">
                  <div class="pull-left">
                    <h3>Lineas</h3>
                  </div>
                  <div class="pull-right">
                    <a href="/{{Auth::user()->level->alias}}/linea_nuevo" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo</a>
                  </div>
                </div>
              </div>
                <div class="card-body table-responsive">
                    @php
                      $lineas = \App\linea::orderBy('departamento','asc')->get();
                    @endphp
                      {{ csrf_field() }}
                      @if (count($lineas) > 0)
                        <table class="table">
                          <tr>
                            <th>Extensión</th>
                            <th>Departamento</th>
                            <th>Encargado</th>
                            <th>Teléfono</th>
                            <th>Correo electrónico</th>
                          </tr>
                          @foreach ($lineas as $linea)
                            <tr>
                              <td>{{$linea->extension}}</td>
                              <td><a href="/lineas/editar/{{md5($linea->id)}}">{{$linea->departamento}}</a></td>
                              <td>{{$linea->encargado}}</td>
                              <td>{{$linea->telefono}}</td>
                              <td>{{$linea->correo}}</td>
                            </tr>
                          @endforeach
                        </table>
                        @else
                          <h4>No hay resultados</h4>
                      @endif
                </div>
            </div>
        </div>
@endsection
