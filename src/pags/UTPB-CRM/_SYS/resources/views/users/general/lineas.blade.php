@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <h3>Directorio corporativo</h3>
                    @php
                      $lineas = \App\linea::orderBy('departamento','asc')->get();
                    @endphp
                      {{ csrf_field() }}
                      @if (count($lineas) > 0)
                        <table class="table table-stripped">
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
                              <td>{{$linea->departamento}}</td>
                              <td>{{$linea->encargado}}</td>
                              <td>{{$linea->telefono}}</td>
                              <td>
                                <a href="/bandeja/nuevo/enviar?a={{$linea->correo}}">{{$linea->correo}}</a>
                              </td>
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
