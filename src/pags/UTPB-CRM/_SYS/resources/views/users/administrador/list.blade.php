@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                    <h3 class="titulo">Resultados</h3>
                    <form class="form-horizontal" method="POST" action="/user/search">
                      {{ csrf_field() }}
                      @if (count($users) > 0)
                        <table class="table table-stripped">
                          <tr>
                            <th>Encargado</th>
                            <th>Correo electrónico</th>
                            <th>Nivel</th>
                          </tr>
                          @foreach ($users as $user)
                            <tr>
                              <td><a href="/user/modify/{{md5($user->id)}}">{{$user->name}}</a></td>
                              <td>{{$user->email}}</td>
                              <td>{{(isset($user->level->name)) ? $user->level->name : "Sin àrea"}}</td>
                            </tr>
                          @endforeach
                        </table>
                        @else
                          <h4>No hay resultados</h4>
                      @endif
                    </form>
                </div>
            </div>
        </div>
@endsection
