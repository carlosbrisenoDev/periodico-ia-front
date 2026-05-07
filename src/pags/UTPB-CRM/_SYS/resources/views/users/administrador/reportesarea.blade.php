@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="">
            <div class="card">
              <div class="card-header">
                <div class="clearfix">
                  <div class="pull-left">
                    <h3>Reportes por área</h3>
                  </div>
                  <div class="pull-right">
                  </div>
                </div>
              </div>
                <div class="card-body">
                    <div class="row">
                    @foreach (App\level::whereNotIn('name',["Administrador","Empleado","Franquiciatario"])->get() as $area)
                      <div class="col-3" style="">
                          <div class="card thumbnail" style="margin-top:10px;">
                            <div class="card-body table-responsive">
                              <h4 class="text-center btn-link">{{$area->name}}</h4>
                              <p class="card-text">
                                <table class="table">
                                  <tr>
                                    <td>Tareas</td>
                                    @php
                                      $reportesf = \App\reporte::where('level_id',$area->id)
                                      ->where('estado_id','<>',\App\estado::where('nombre','Finalizado')->first()->id)->get();
                                    @endphp
                                    <td></td>
                                  </tr>
                                  <tr>
                                    <td>Formatos</td>
                                    <td>{{count($area->formatos)}}</td>
                                  </tr>
                                  <tr>
                                    <td>Calificación</td>
                                    <td>{{($area->calificacion == "") ? "Sin calificación" : $area->calificacion}}</td>
                                  </tr>
                                </table>
                              </p>
                              <a href="/historial/area/{{md5($area->id)}}" style="width:100%;" class="btn btn-primary"><i class="fa fa-check"></i> Revisar</a>
                            </div>
                          </div>
                      </div>
                    @endforeach
                  </div>
                </div>
            </div>
        </div>
@endsection
