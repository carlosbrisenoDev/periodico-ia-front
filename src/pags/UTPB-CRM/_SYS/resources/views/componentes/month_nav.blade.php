@php
$dim2Mes = ["Jan" => "Enero","Feb" => "Febrero","Mar" => "Marzo","Apr" => "Abril","May" => "Mayo","Jun" => "Junio","Jul" => "Julio","Aug" => "Agosto","Sep" => "Septiembre","Nov" => "Noviembre","Oct" => "Octubre","Dec" => "Diciembre",];
$meses_espanol = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre',);
@endphp
<div class="float-right pr-2">
  <div class="row">
    <div class="col pr-1">
      <select class="form-control mes" name="mes" onchange="locateInTime()">
        @foreach ($dim2Mes as $_month => $_mes)
          <option value="{{$_month}}" {{$dim2Mes[$_month] == $meses_espanol[$mes-1] ? "selected" : ""}}>{{$_mes}}</option>
        @endforeach
      </select>
    </div>
    <div class="col p-0">
      <select class="form-control anio" name="anio" onchange="locateInTime()">
        @for ($i=2020; $i < 2030; $i++)
          <option value="{{$i}}" {{$i == date("Y") ? "selected" : ""}}>{{$i}}</option>
        @endfor
      </select>
    </div>
  </div>
</div>
@section('scripts')
  <script type="text/javascript">
    function locateInTime(){
      location.href = "?mes="+$(".mes").val()+"&anio="+$(".anio").val();
    }
  </script>
@endsection
