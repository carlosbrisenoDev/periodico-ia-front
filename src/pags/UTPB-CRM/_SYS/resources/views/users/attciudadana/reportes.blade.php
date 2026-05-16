@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
@php
    $ciudadano = \App\ciudadano::where('id',1)->first();
    $reportes = \App\reporte::where('ciudadano_id',1)->get();
@endphp
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                      <div class="clearfix">
                        <div class="pull-left">
                          <h3><a href="/ciudadano/modify/{{md5($ciudadano->id)}}">{{$ciudadano->full_name()}}</a></h3>
                          <h4>Solicitudes</h4>
                        </div>
                        <div class="pull-right">
                        </br>
                            <a href="/reportes/nuevor/{{md5($ciudadano->id)}}" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo</a>
                        </div>
                      </div>
                      <hr>
                      {{ csrf_field() }}
                      @if (count($reportes) > 0)
                        <table class="table table-responsive table-stripped">
                          <tr>
                            <th>Título del reporte</th>
                            <th>Dirigido a</th>
                            <th>Estado</th>
                            <th>Fecha de creación</th>
                          </tr>
                          @foreach ($reportes as $reporte)
                            <tr>
                              <td><a href="/reportes/modify/{{md5($reporte->id)}}">{{$reporte->nombre}}</a></td>
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
