@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                          <h3>Archivo</h3>
                          <h4>Reportes finalizados</h4>
                      <hr>
                      {{ csrf_field() }}
                      @php
                        $pull = "right";
                        $max = 20;
                        $reportes = \App\reporte::whereHas('estado',function($query){
                          $query->where('nombre','Finalizado');
                        })->where('level_id',Auth::user()->level->id)->paginate($max);
                        $where = "branding";
                        $nav = $reportes;
                      @endphp
                      @include('componentes.navegacion')
                      @if (count($reportes) > 0)
                        <table class="table table-responsive table-stripped">
                          <tr>
                            <th>Folio</th>
                            <th>Título del reporte</th>
                            <th>Dirigido a</th>
                            <th>Estado</th>
                            <th>Fecha de creación</th>
                          </tr>
                          @foreach ($reportes as $reporte)
                            <tr>
                              <td><b>{{$reporte->id}}</b></td>
                              <td><a href="/reportes/modify/{{md5($reporte->id)}}">{{$reporte->nombre}}</a></td>
                              <td>{{$reporte->level->name}}</td>
                              <td>{{$reporte->estado->nombre}}</td>
                              <td>{{$reporte->full_fecha()}}</td>
                            </tr>
                          @endforeach
                        </table>
                        @include('componentes.navegacion')

                        @else
                          <h4>No hay resultados</h4>
                      @endif
                </div>
            </div>
        </div>
@endsection
