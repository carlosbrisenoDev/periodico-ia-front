@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="col-md-12">
    @include('componentes.inscritos2')
  </div>
  <br>
  <div class="row">
    <div class="col-md-6">
        <div class="card card-default large">
            <div class="card-body">
              <h5 class="card-title">Vededores</h5>
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
              <h5 class="card-title">Vendedores</h5>
              <h6 class="card-subtitle mb-2 text-muted">
                Estadística de este mes
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
                Estadística
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
            $sql = \App\cliente::whereRAW(date("m")." = month(created_at) and ".date("Y")." = year(created_at)")->where("agente_id","<>",0)->with("agente")->selectRAW("*, count(*) as total")->where("status",4)->groupBy("agente_id");
          @endphp
          @foreach ($sql->get() as $cuenta)
            ['{{$cuenta->agente->name ?? "None"}}',{{$cuenta->total}}],
          @endforeach
        ]);

        var options = {
          title: 'Top ventas {{Date("M")}}'
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
          ['Vendedor', 'Ventas'],
          @php
            $sql = \App\cliente::where("agente_id","<>",0)->with("agente")->selectRAW("*, count(*) as total")->groupBy("agente_id");
          @endphp
          @foreach ($sql->get() as $cuenta)
            ['{{$cuenta->agente->name ?? "None"}}',{{$cuenta->total}}],
          @endforeach
          ["Sin vendedor",{{\App\cliente::where("agente_id",0)->with("agente")->selectRAW("*, count(*) as total")->groupBy("agente_id")->count()}}]
        ]);

        var options = {
          title: 'Leads por vendedor'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart3'));

        chart.draw(data, options);
      }
    </script>
@endsection
