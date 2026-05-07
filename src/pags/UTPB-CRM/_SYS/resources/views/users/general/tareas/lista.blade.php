@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3>Tareas</h3>

      </div>
        <div class="card-body">
                  @php
                    $datos = \App\estado::all();
                  @endphp
                  <div class="row">
                    @foreach ($datos as $estado)
                      @if($datos[count($datos)-1] != $estado)
                        <div class="col-12 col-md-12 col-lg-6 topp">
                        <div class="card">
                          <div class="card-header">
                            Tareas <i>{{$estado->nombre}}</i>
                          </div>
                            <div class="card-body table-responsive">
                                <table class="table">
                                  @php
                                    $reportes = \App\reporte::where('estado_id',$estado->id)
                                    ->where('level_id',Auth::user()->level->id)
                                    ->get();
                                  @endphp
                                  @if (count($reportes) > 0)
                                    <table class="table">
                                      <tr>
                                        <th><b>Folio</b></th>
                                        <th>Título</th>
                                        <th>Dirigido a</th>
                                        <th>Estado</th>
                                        <th>Fecha de creación</th>
                                      </tr>
                                      @foreach ($reportes as $reporte)
                                        <tr>
                                          <td>{{$reporte->id}}</td>
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
                                </table>
                              </div>
                          </div>
                          </div>
                      @endif
                    @endforeach
  </div>
</div>
</div>
</div>
</div>
@endsection
