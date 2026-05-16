@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                    <h3>Mis tareas</h3>
                          @if (count($reportes) > 0)
                            <table class="table table-responsive table-stripped">
                              <tr>
                                <th><b>Folio</b></th>
                                <th>Tarea</th>
                                <th>Dirigido a</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Fecha de creación</th>
                              </tr>
                              @foreach ($reportes as $reporte)
                                <tr>
                                  <td>{{$reporte->id}}</td>
                                  <td><a href="/reportes/modify/{{md5($reporte->id)}}">{{$reporte->nombre}}</a></td>
                                  <td>{{$reporte->level->name}}</td>
                                  <td style="color:{{$reporte->prioridad->color}};">{{$reporte->prioridad->nombre}}</td>
                                  <td>{{$reporte->estado->nombre}}</td>
                                  <td>{{$reporte->full_fecha()}}</td>
                                </tr>
                              @endforeach
                            </table>
                            @else
                              <h4>No hay resultados</h4>
                          @endif
                        </table>
                </div>
            </div>
        </div>
@endsection
