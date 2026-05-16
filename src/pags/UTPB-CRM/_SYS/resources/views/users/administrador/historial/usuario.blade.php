@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="">
            <div class="card card-default">
              <div class="card-header">
                <div class="clearfix">
                  <div class="pull-left">
                    <h3><a href="/{{Auth::user()->level->alias}}/reportesarea">Áreas</a></h3>
                  </div>
                </div>
              </div>
                <div class="card-body">
                        <h4>{{$area->name}}</h4>

                    <hr>
                    <div class="col-md">
                      <div class="col-4 pull-right nopadding">
                        <form class="" action="/historial/calificar" method="post">
                          {{ csrf_field() }}
                          <div class="input-group">
                            <input type="hidden" name="id" value="{{$area->id}}">
                            <input type="text" class="form-control" name="calificacion" value="{{($area->calificacion == "") ? "Sin calificación" : $area->calificacion}}">
                            <div class="input-group-btn">
                              <input type="submit" class="btn btn-primary" name="cal" value="Asignar calificación">
                            </div>
                          </div>
                        </form>
                      </div>
                    </br>
                    </br>
                      <hr>
                    </div>
                    <div class="row">
                      <div class="col-12 col-md-12 col-lg-6">
                        <h3>Tareas</h3>
                        <table class="table table-striped">
                          @foreach (\App\estado::all() as $estado)
                            @php
                              $reportes = \App\reporte::where('estado_id',$estado->id)->where('level_id',$area->id)->get();
                            @endphp
                            <tr>
                              <td><a href="/tareas/estado/{{md5($estado->id)}}?area={{md5($area->id)}}">{{$estado->nombre}}</a></td>
                              <td>{{count($reportes)}}</td>
                            </tr>
                          @endforeach
                          <tr>
                            <td><b>Total</b></td>
                            <td><b>{{count($area->reportes)}}</b></td>
                          </tr>
                        </table>
                        <hr>
                        <div class="thumbnail">
                            <div id="reportes" style="height:500px;" class="img img-responsive"></div>
                        </div>
                        <hr>
                        <h3>Finalizados</h3>
                        <table class="table table-striped">
                          <tr>
                            <td><a href="/tareas/satisfactorios/{{md5($area->id)}}">Tareas que procedieron satisfactoriamente</a></td>
                            @php
                              $total = count(\App\reporte::where('estado_id',\App\estado::where('nombre','Finalizado')->first()->id)->where('level_id',$area->id)->get());
                              $si = count(\App\reporte::where('estado_id',\App\estado::where('nombre','Finalizado')->first()->id)->where('level_id',$area->id)->where('procedio','1')->get());

                            @endphp
                            <td>{{$si}} | {{(($si>0) ? $si/$total : $si)*100}}%</td>
                          </tr>
                          <tr>
                            <td><a href="/tareas/insatisfactorios/{{md5($area->id)}}">Tareas que no procedieron</a></td>
                            @php
                              $total = count(\App\reporte::where('estado_id',\App\estado::where('nombre','Finalizado')->first()->id)->where('level_id',$area->id)->get());
                              $si = count(\App\reporte::where('estado_id',\App\estado::where('nombre','Finalizado')->first()->id)->where('level_id',$area->id)->where('procedio','0')->get());

                            @endphp
                            <td>{{$si}} | {{(($si>0) ? $si/$total : $si)*100}}%</td>
                          </tr>
                        </table>
                        <div class="thumbnail">
                            <div id="finalizados" style="height:500px;" class="img img-responsive"></div>
                        </div>
                      </div>
                      <div class="col-12 col-md-12 col-lg-6">
                        <h3>Formatos</h3>
                        <table class="table table-striped">
                          <tr>
                            <td>Documentos generados</td>
                            <td>{{count($area->formatos)}}</td>
                          </tr>
                          <tr>
                            <td><a href="/formatos/estado/{{md5($area->id)}}?archivo=1">Documentos comprobados</a></td>
                            <td>{{count(\App\formato::where('level_id',$area->id)->where('archivo','1')->get())}}</td>
                          </tr>
                          <tr>
                            <td><a href="/formatos/estado/{{md5($area->id)}}?archivo=0">Documentos <b>sin</b> comprobar</a></td>
                            <td>{{count(\App\formato::where('level_id',$area->id)->where('archivo','0')->get())}}</td>
                          </tr>
                        </table>
                        <h3>Usuarios</h3>
                        <table class="table table-striped">
                          <tr>
                            <td>Número de usuarios</td>
                            <td>{{count(\App\user::where('level_id',$area->id)->get())}}</td>
                          </tr>
                          <tr>
                            <td colspan="2" class="nopadding">
                              <table class="table nopadding">
                                <tr>
                                  <th>Nombre</th>
                                  <th>Correo</th>
                                </tr>
                                @foreach (\App\user::where('level_id',$area->id)->get() as $u)
                                  <tr>
                                    <td><a href="/user/modify/{{md5($u->id)}}">{{$u->name}}</a></td>
                                    <td>{{$u->email}}</td>
                                  </tr>
                                @endforeach
                              </table>
                            </td>
                          </tr>
                        </table>
                        <div class="thumbnail">
                            <div id="formatos" style="height:500px;" class="img img-responsive"></div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6 col-xl-6 table-responsive">
                      <h3>Movimientos ({{count(\App\historial::whereHas('usuario',function($q) use ($area){$q->where('level_id',$area->id);})->get())}})</h3>
                      @php
                      $pull = "right";
                      $max = 20;
                      $entrys = \App\historial::whereHas('usuario',function($q) use ($area){$q->where('level_id',$area->id);})->orderBy("id","desc")->paginate($max);
                      $where = "branding";
                      $nav = $entrys;
                      @endphp
                      @include('componentes.navegacion')
                      <table class="table">
                        <tr>
                          <th>Usuario</th>
                          <th>Acción</th>
                          <th>Fecha</th>
                        </tr>
                        @foreach ($entrys as $h)
                          <tr>
                            <td><a href="/user/modify/{{md5($h->usuario->id)}}">{{$h->usuario->name}}</a></td>
                            <td>{{$h->accion}}</td>
                            <td>{{$h->full_fecha()}}</td>
                          </tr>
                        @endforeach
                      </table>
                    </div>
            </div>
        </div>
@endsection
@section('scripts')
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data1 = google.visualization.arrayToDataTable([
          ['Tareas', 'Estado de las tareas'],
          @foreach (\App\estado::all() as $estado)
            @php
              $reportes = \App\reporte::where('estado_id',$estado->id)->where('level_id',$area->id)->get();
            @endphp
            ['{{$estado->nombre}}',{{count($reportes)}}],
          @endforeach
        ]);
        var data2 = google.visualization.arrayToDataTable([
          ['Finalizados', 'Tareas finalizados'],
          ['No finalizados',{{count(\App\reporte::where('estado_id',\App\estado::where('nombre','<>','Finalizado')->first()->id)->where('level_id',$area->id)->get())}}],
          ['Satisfactorios',{{count(\App\reporte::where('estado_id',\App\estado::where('nombre','Finalizado')->first()->id)->where('level_id',$area->id)->where('procedio','1')->get())}}],
          ['Insatisfactorios',{{count(\App\reporte::where('estado_id',\App\estado::where('nombre','Finalizado')->first()->id)->where('level_id',$area->id)->where('procedio','0')->get())}}],
        ]);
        var data3 = google.visualization.arrayToDataTable([
          ['Formatos', 'Formatos generados'],
          ['Comprobados',{{count(\App\formato::where('level_id',$area->id)->where('archivo','1')->get())}}],
          ['Sin comprobados',{{count(\App\formato::where('level_id',$area->id)->where('archivo','0')->get())}}],
        ]);

        var options1 = {
          title: 'Tareas de atención ciudadana',
          pieHole: 0.4,
        };
        var options2 = {
          title: 'Tareas finalizados',
          pieHole: 0.5,
        };
        var options3 = {
          title: 'Formatos generados',
          pieHole: 0.3,
        };

        var chart = new google.visualization.PieChart(document.getElementById('reportes'));
        chart.draw(data1, options1);

        var chart2 = new google.visualization.PieChart(document.getElementById('finalizados'));
        chart2.draw(data2, options2);

        var chart3 = new google.visualization.PieChart(document.getElementById('formatos'));
        chart3.draw(data3, options3);
      }
    </script>
@endsection
