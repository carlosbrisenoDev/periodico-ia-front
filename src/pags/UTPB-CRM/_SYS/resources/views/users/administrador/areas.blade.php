@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="">
            <div class="card card-default">
              <div class="card-header">
                <div class="clearfix">
                  <div class="pull-left">
                    <h3>Áreas</h3>
                  </div>
                  <div class="pull-right">
                  </br>
                    <a href="/levels/nuevo/area" class="pull-right btn btn-default"><i class="fa fa-plus"></i> Nueva área</a>
                  </div>
                </div>
              </div>
                <div class="card-body">
                    <form class="form-horizontal" method="POST" action="/user/search">
                      {{ csrf_field() }}
                      @php
                        $areas = App\level::all()->except([0,1]);
                      @endphp
                      @if (count($areas) > 0)
                        <table class="table table-bordered">
                          <tr class="">
                            <th>Nombre del área</th>
                            <th>Alias (Módulo)</th>
                            <th>Nivel</th>
                          </tr>
                          @foreach ($areas as $level)
                            <tr>
                              <td><a href="/levels/modify/{{md5($level->id)}}">{{$level->name}}</a></td>
                              <td><a href="/levels/modifymodule/{{md5($level->id)}}">{{$level->alias}} <i class="fa fa-edit"></i></a></td>
                              <td>{{$level->nivel}}</td>
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
@endsection
