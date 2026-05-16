@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="row">
            <div class="col">
              <div class="card">
                <div class="card-header">
                  <h3>Franquicias</h3>
                </div>
                  <div class="card-body">
                      <form class="form-horizontal" method="POST" action="/user/search">
                        {{ csrf_field() }}
                        @if (count($franqs) > 0)
                          <table class="table table-bordered">
                            <tr class="">
                              <th scope="col">#</th>
                              <th scope="col">Nombre del solicitante</th>
                              <th scope="col">Teléfono</th>
                              <th scope="col">Dirección</th>
                              <th scope="col">Fecha de solicitud</th>
                              <th scope="col">Acciones</th>
                            </tr>
                            @foreach ($franqs as $usuario)
                              <tr>
                                <td>{{$usuario->id}}</td>
                                <td>{{$usuario->name}}</td>
                                <td>{{$usuario->franquicia->nombre}}</td>
                                <td>{{$usuario->franquicia->direccion}}</td>
                                <td>{{\Carbon\Carbon::parse($usuario->updated_at)->toDayDateTimeString()}}</td>
                                <td>
                                  
                                </td>
                              </tr>
                            @endforeach
                          </table>
                          @else
                            <div class="col-md-12">
                              <h4>No hay resultados</h4>
                            </div>
                        @endif
                      </form>
                  </div>
              </div>
            </div>
        </div>
@endsection
