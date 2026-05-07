@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">

                    <h3>Resultados</h3>
                    <hr>
                      {{ csrf_field() }}
                      @if (count($ciudadanos) > 0)
                        <table class="table table-responsive table-stripped">
                          <tr>
                            <th>Nombre</th>
                            <th>Curp</th>
                            <th>Correo electrónico</th>
                            <th>Localidad</th>
                            <th>Colonia</th>
                          </tr>
                          @foreach ($ciudadanos as $ciudadano)
                            <tr>
                              <td><a href="/ciudadano/modify/{{md5($ciudadano->id)}}">{{$ciudadano->full_name()}}</a></td>
                              <td>{{$ciudadano->curp}}</td>
                              <td>{{$ciudadano->email}}</td>
                              <td>{{$ciudadano->localidad}}</td>
                              <td>{{$ciudadano->colonia}}</td>
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
