@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
            <div class="card card-default">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12 col-md-12 col-lg-6">
                      <h3>Reportes</h3>
                      <table class="table table-striped">
                        @foreach (\App\estado::all() as $estado)
                          @php
                            $reportes = \App\reporte::where('estado_id',$estado->id)->get();
                          @endphp
                          <tr>
                            <td>{{$estado->nombre}}</td>
                            <td>{{count($reportes)}}</td>
                          </tr>
                        @endforeach
                        <tr>
                          <td><b>Total</b></td>
                          <td><b>{{count(\App\reporte::all())}}</b></td>
                        </tr>
                      </table>
                      <hr>
                      <div class="thumbnail">
                          <div id="reportes" style="height:500px;" class="img img-responsive"></div>
                      </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
                      <h3>Sistema</h3>
                      <table class="table table-striped">
                        <tr>
                          <td>Usuarios</td>
                          <td>{{count(\App\ciudadano::all())}}</td>
                        </tr>
                        <tr>
                          <td>Usuarios</td>
                          <td>{{count(\App\User::all())}}</td>
                        </tr>
                        <tr>
                          <td>Movimientos de usuarios</td>
                          <td>{{count(\App\historial::all())}}</td>
                        </tr>
                      </table>
                      <hr>
                    </div>
                  </div>
                    <div class="row">
                      <div class="col">
                        <h3>Historial</h3>
                        <h4>Últimos movimientos</h4>
                        <table class="table table-striped" id="historymov">
                          <thead>
                            <tr>
                              <th style="color:#67748e;">Usuario</th>
                              <th style="color:#67748e;">Acción</th>
                              <th style="color:#67748e;">Fecha</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach (\App\historial::orderBy("id","desc")->paginate(20) as $h)
                            <tr>
                              <td>
                                @if($h->usuario)
                                  {{$h->usuario->full_name()}}
                                @else
                                  App Movil
                                @endif
                              </td>
                              <td>{{$h->accion}}</td>
                              <td>{{$h->full_fecha()}}</td>
                            </tr>
                          @endforeach
                          </tbody>
                        </table>
                      </div>
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
          ['Reportes', 'Estado de los reportes'],
          @foreach (\App\estado::all() as $estado)
            @php
              $reportes = \App\reporte::where('estado_id',$estado->id)->get();
            @endphp
            ['{{$estado->nombre}}',{{count($reportes)}}],
          @endforeach
        ]);

        var options1 = {
          title: 'Reportes',
          pieHole: 0.4,
        };
        var chart = new google.visualization.PieChart(document.getElementById('reportes'));
        chart.draw(data1, options1);
      }
    </script>
      <script>
        $('#historymov').DataTable();
        </script>
@endsection