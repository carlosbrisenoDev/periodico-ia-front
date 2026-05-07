@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="col-md-12">
    @include('componentes.inscritos2')
    @php
        $mes2 = Request::has("m") ? Request::get("m") : Date("m")*1;
        $year = Request::has("y") ? Request::get("y") : Date("y")*1;
    @endphp
  </div>
  <br>
  <div class="row">
    <div class="card">
      <div class="card-body">
          <h5 class="card-title">Ventas por mes</h5>
          <h6 class="card-subtitle mb-2 text-muted">
            Cada mes
          </h6>
          <hr>
          @php
              $mes = explode(",",",Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre");
              $meses = \DB::select(\DB::RAW('SELECT count(*) as total, month(doc.created_at) as monthly, year(doc.created_at) as yearly FROM `clientes` as c join documentos as doc on doc.id = c.comprobante where comprobante is not null group by month(doc.created_at), year(doc.created_at)'));
          @endphp
          <table class="table table-striped" id="meses">
            <thead class="bg-dark">
              <th>Año</th>
              <th>Mes</th>
              <th>Inscritos</th>
            </thead>
            <tbody>
              @foreach ($meses as $item)
                <tr>
                  <td>{{$item->yearly}}</td>
                  <td>
                    <a href="?y={{$item->yearly}}&m={{$item->monthly}}#ventasmes">{{$mes[$item->monthly]}}</a>
                  </td>
                  <td>{{$item->total}}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
      </div>
    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-md-6">
        <div class="card card-default large">
            <div class="card-body">
              <h5 class="card-title">Ventas generales</h5>
              <h6 class="card-subtitle mb-2 text-muted">
                Estadística general
              </h6>
              <hr>
              <div id="piechart" style="width: 100%; height: 500px;"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-default large">
            <div class="card-body">
              <h5 class="card-title" id="ventasmes">Vendedores</h5>
              <h6 class="card-subtitle mb-2 text-muted">
                Estadística de {{$mes[$mes2]}} {{$year}}
              </h6>
              <hr>
              <div id="piechart2" style="width: 100%; height: 500px;"></div>
            </div>
        </div>
    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-md-6">
        <div class="card card-default large">
            <div class="card-body">
              <h5 class="card-title">Clientes/Vendedores</h5>
              <h6 class="card-subtitle mb-2 text-muted">
              Leads recibidos este {{$mes[$mes2]}} {{$year}}
              </h6>
              <hr>
              <div id="piechart3" style="width: 100%; height: 500px;"></div>
            </div>
        </div>
    </div>
  </div>
@endsection
@section('scripts')
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
    $("#meses").DataTable(lang);
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Vendedor', 'Ventas'],
          @foreach (\App\cliente::where("agente_id","<>",0)->with("agente")->selectRAW("*, count(*) as total")->where("status",4)->groupBy("agente_id")->get() as $cuenta)
            ['{{$cuenta->agente->name ?? "None"}}',{{$cuenta->total}}],
          @endforeach
        ]);

        var options = {
          title: 'Top ventas'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Vendedor', 'Ventas'],
          @php
            $sql = \App\cliente::whereRAW($mes2." = month(created_at) and ".$year." = year(created_at)")->where("agente_id","<>",0)->with("agente")->selectRAW("*, count(*) as total")->where("status",4)->groupBy("agente_id");
          @endphp
          @foreach ($sql->get() as $cuenta)
            ['{{$cuenta->agente->name ?? "None"}}',{{$cuenta->total}}],
          @endforeach
        ]);

        var options = {
          title: 'Top ventas {{$mes[$mes2]}}'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart2'));

        chart.draw(data, options);
      }
    </script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Vendedor', 'Ventas {{$mes[$mes2]}} {{$year}}'],
          @php
            $sql = \App\cliente::whereRAW("MONTH(created_at) = ".($mes2)." and YEAR(created_at) = ".($year)." and agente_id <> 0")
              ->with("agente")
                ->selectRAW("*, count(*) as total")
                  ->groupBy("agente_id");
            $clientes = \App\cliente::whereRAW("MONTH(created_at) = ".($mes2)." and YEAR(created_at) = ".($year)." and agente_id <> 0")->with("agente")->selectRAW("*, count(*) as total")->groupBy("agente_id");
          @endphp
          @foreach ($sql->get() as $cuenta)
            ['{{$cuenta->agente->name ?? "None"}}',{{$cuenta->total}}],
          @endforeach
          ["Sin vendedor",{{$clientes->count()}}]
        ]);

        var options = {
          title: 'Leads por vendedor {{$mes[$mes2]}} {{$year}}'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart3'));

        chart.draw(data, options);
      }
    </script>
@endsection
