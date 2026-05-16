<div class="row">
  <div class="col-sm-3 col-xs-12">
    <div class="card bordi">
      <div class="card-body">
        <h1 class="card-title count">{{\App\cliente::all()->count()}}</h1>
        <h6 class="card-subtitle mb-2 text-muted">Clientes registrados</h6>
      </div>
    </div>
  </div>
  <div class="col-sm-3 col-xs-12">
    <div class="card bordi">
      <div class="card-body">
        <h1 class="card-title count">{{$z =\App\cliente::where("status","4")->where("nombre","not like","%PRUEBA%")->count()}}</h1>
        <a href="/ventas/inscritos">
          <h6 class="card-subtitle mb-2 text-muted">Alumnos inscritos</h6>
        </a>
      </div>
    </div>
  </div>
  <div class="col-sm-3 col-xs-12">
    <div class="card bordi">
      <div class="card-body">
        <h1 class="card-title count">{{700-$z}}</h1>
        <h6 class="card-subtitle mb-2 text-muted">Punto de equilibrio</h6>
      </div>
    </div>
  </div>
  <div class="col-sm-3 col-xs-12">
    <div class="card bordigreen">
      <div class="card-body">
        @php
        $mes = isset($_REQUEST["mes"]) ? \Carbon\carbon::parse($mes)->format("m") : \Carbon\carbon::now()->format("m");
        $anio = isset($_REQUEST["anio"]) ? $_REQUEST["anio"] : \Carbon\carbon::now()->format("Y");
        $dats = \App\pagos::whereHas("documento",function($q) use($mes){
          $q->whereRAW("MONTH(created_at) = '$mes'");
        })->where("anio",$anio)->where("pagado","<>",NULL);
        $total_pago = 0.0;
        foreach ($dats->get() as $pp) {
          $total_pago += str_replace("$","",str_replace(",","",$pp->pago));
        }
        @endphp
        <h1 class="card-title count text-success">{{count($dats->get())}}</h1>
        <h6 class="card-subtitle mb-2">
          <a href="/creditos/pagos" class="text-success">
            <i class="fas fa-link"></i>
            Pagos recibidos este mes (${{number_format($total_pago,2,".",",")}})
          </a>
        </h6>
      </div>
    </div>
  </div>
</div>
<br>
<div class="row">
  <div class="col-sm-3 col-xs-12">
    <div class="card bordiyellow">
      <div class="card-body">
        @php
        $dats = \App\pagos::where("pagado",NULL)->orderBy("created_at","desc");
        $total_pago = 0.0;
        $con=0;
        foreach ($dats->get() as $pp) {
          if(\Carbon\carbon::parse($pp->anio."-".$pp->mes_en."-1")->subDays(1)->isPast() && $pp->tabla->cartera->cliente->baja == NULL){
            $total_pago += str_replace("$","",str_replace(",","",$pp->pago));
            $con++;
          }
        }
        @endphp
        <h1 class="card-title count text-warning">{{$con}}</h1>
        <h6 class="card-subtitle mb-2">
          <a href="/creditos/notify" class=" text-warning">
            <i class="fas fa-link"></i>
            Pagos pendientes (${{number_format($total_pago,2,".",",")}})
          </a>
        </h6>
      </div>
    </div>
  </div>
  <div class="col-sm-3 col-xs-12">
    <div class="card bordi">
      <div class="card-body">
        <h1 class="card-title count">{{$r =\App\cliente::where("status","4")->where("baja","<>",NULL)->where("nombre","not like","%PRUEBA%")->count()}}</h1>
        <a href="/creditos/bajas">
          <h6 class="card-subtitle mb-2 text-muted">Alumnos con baja temporal</h6>
        </a>
      </div>
    </div>
  </div>
  <div class="col-sm-3 col-xs-12">
    <div class="card bordi">
      <div class="card-body">
        <h1 class="card-title count">{{\App\cliente::whereHas("credito_info",function($query){
          $query->where("status","cartera");
        })->where("status","4")->where("baja",NULL)->where("nombre","not like","%PRUEBA%")->count()}}</h1>
          <h6 class="card-subtitle mb-2 text-muted">En cartera</h6>
      </div>
    </div>
  </div>
  <div class="col-sm-3 col-xs-12">
    <div class="card bordi">
      <div class="card-body">
        <h1 class="card-title count">{{$z-$r}}</h1>
        <h6 class="card-subtitle mb-2 text-muted">Alumnos activos</h6>
      </div>
    </div>
  </div>
</div>
<br>

<hr>
@php
  $semana = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado"];
  $meses = ["","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
@endphp
