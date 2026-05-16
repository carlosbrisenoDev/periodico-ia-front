@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                      <div class="clearfix">
                        <div class="pull-left">
                          <h3><a href="/historial/area/{{md5($area->id)}}">{{$area->name}}</a></h3>
                          <h4>Reportes {{((isset($title)) ? $title : "")}}</h4>
                        </div>
                        <div class="pull-right">
                        </div>
                      </div>
                      <hr>
                      {{ csrf_field() }}
                      @if (count($reportes) > 0)
                        <table class="table table-responsive table-stripped">
                          <tr>
                            <th>Folio</th>
                            <th>Título del reporte</th>
                            <th>Ciudadano</th>
                            <th>Dirigido a</th>
                            <th>Estado</th>
                            <th>Fecha de creación</th>
                          </tr>
                          @foreach ($reportes as $reporte)
                            <tr>
                              <td>{{$reporte->id}}</td>
                              <td><a href="/reportes/modify/{{md5($reporte->id)}}">{{$reporte->nombre}}</a></td>
                              <td>{{$reporte->ciudadano->full_name()}}</td>
                              <td>{{$reporte->level->name}}</td>
                              <td>{{$reporte->estado->nombre}}</td>
                              <td>{{$reporte->full_fecha()}}</td>
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
