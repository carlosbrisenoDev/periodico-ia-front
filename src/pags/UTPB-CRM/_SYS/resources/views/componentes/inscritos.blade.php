@php
  $semana = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado"];
  $meses = ["","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
  $meta = \App\metas::whereRAW("month(created_at)='".date("m")."'")->first();
  $meta_mensual = 40;
  $meta_total = 1000;
  $meta_equilibrio = 500;
  if($meta){
    $meta_mensual = $meta->meta_mensual ?? 40;
    $meta_total = $meta->meta_total ?? 1000;
    $meta_equilibrio = $meta->equilibrio ?? 500;
  }
@endphp
<div class="row">
  <div class="col-sm-3 col-xs-12">
    <div class="card bordi">
      <div class="card-body">
        <h4 class="card-title count">{{\App\cliente::all()->count()}}</h4>
        <h6 class="card-subtitle mb-2 text-muted">Clientes registrados</h6>
      </div>
    </div>
  </div>
  <div class="col-sm-3 col-xs-12">
    <div class="card bordi">
      <div class="card-body">
        <h4 class="card-title count">{{$z =\App\cliente::where("status","4")->where("nombre","not like","%PRUEBA%")->count()}}</h4>
        <a href="/ventas/inscritos">
          <h6 class="card-subtitle mb-2 text-muted">Alumnos inscritos</h6>
        </a>
      </div>
    </div>
  </div>
  {{-- <div class="col-sm-3 col-xs-12">
    <div class="card bordi">
      <div class="card-body">
        <h4 class="card-title count">{{$meta_equilibrio-$z}}</h4>
        <h6 class="card-subtitle mb-2 text-muted">Punto de equilibrio</h6>
      </div>
    </div>
  </div> --}}
  <div class="col-sm-3 col-xs-12">
    <div class="card bordi">
      <div class="card-body">
        <h4 class="card-title count">{{$meta_total-$z}}</h4>
        <h6 class="card-subtitle mb-2 text-muted">Restantes para la meta</h6>
      </div>
    </div>
  </div>

{{-- <div class="col-sm-3 col-xs-12">
  <a target="_blank" href="{{url('/')}}/omnichannel">
    <div class="card"  style="background-color:#25D366;">
      <div class="card-body">
        <h4 class="card-title count leadsn text-white">
          0
        </h4>
        <h6 class="card-subtitle mb-2 text-white"><i class="fa-brands fa-whatsapp text-white"></i> Nuevos Leads</h6>
      </div>
    </div>
  </a>
</div> --}}
<div class="col-sm-3 col-xs-12">
  <div class="card bordi">
    <div class="card-body">
      <h4 class="card-title count">{{$r =\App\cliente::where("status","4")->where("baja","<>",NULL)->where("nombre","not like","%PRUEBA%")->count()}}</h4>
      <a href="/ventas/bajas">
        <h6 class="card-subtitle mb-2 text-muted">Baja temporal</h6>
      </a>
    </div>
  </div>
</div>
<div class="col-sm-3 col-xs-12">
  <div class="card bordi">
    <div class="card-body">
      <h4 class="card-title count">{{$z-$r}}</h4>
      <h6 class="card-subtitle mb-2 text-muted">
        <a href="/ventas/activos">Alumnos activos</a>
      </h6>
    </div>
  </div>
</div>
<div class="col-sm-3 col-xs-12">
  <div class="card bordi">
    <div class="card-body">
      <h4 class="card-title count">{{\App\cliente::doesntHave("cocredito")->where("status","4")->where("baja",NULL)->where("nombre","not like","%PRUEBA%")->count()}}</h4>
        <h6 class="card-subtitle mb-2 text-muted">
          <a href="/creditos/withoutcredito">Sin crédito</a>
        </h6>
    </div>
  </div>
</div>
</div>

<hr>
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-start">
        <h5 class="card-title">{{$meses[date("m")*1]}}</h5>
        <h6 class="card-subtitle mb-2 text-muted">Esta semana</h6>
      </div>
      <div class="float-end">
        <a href="/ventas/calendario">
          Calendario completo
        </a>
      </div>
    </div>
    <hr>
    <div style="display:flex;">
        @for ($i=0; $i < 7; $i++)
            <div style="flex:1;flex-wrap: wrap;border:none;border-right:solid #f2f2f2 1px;">
              <div class="card-body">
                <h5 class="card-title">
                  {{$semana[$i]}}
                </h5>
                <h6 class="card-subtitle mb-2 text-muted">
                  @if (($dia=date("w")*1) == $i)
                      Hoy
                      @php
                        $d = \Carbon\carbon::parse(date("d-m-Y"))->format("d-m-Y");
                      @endphp
                    @else
                      @if ($dia > $i)
                          @php
                            $h = $i == 0 ? 2 : 0;
                          @endphp
                          {{$d = \Carbon\carbon::parse(date("d-m-Y"))->subDays($dia-$i)->format("d-m-Y")}}
                        @elseif ($dia < $i)
                          {{$d = \Carbon\carbon::parse(date("d-m-Y"))->addDays($i-$dia)->format("d-m-Y")}}
                      @endif
                  @endif
                </h6>
                @php
                  $nombres = "";
                  $dat = \Carbon\carbon::parse($d)->toDateString();
                  foreach(App\cliente::selectRAW("*, count(*) as total")
                    ->with("get_comprobante")
                    ->wherehas("get_comprobante",function($query) use($dat){
                      $query->whereDate("created_at",$dat);
                    })->where("nombre","not like","%PRUEBA%")->groupby("agente_id")->get() as $agente)
                      {
                        $nombres .= ($agente->agente?$agente->agente->name:"No-Nam3")."(".$agente->total.") </br>";
                      }
                @endphp
                <h4 class="count" data-bs-toggle="tooltip" data-bs-placement="top" data-html="true"  title="{!!$nombres!!}">
                    {{\App\cliente::where("comprobante","<>",NULL)
                      ->where("nombre","not like","%PRUEBA%")
                      ->with(["get_comprobante"])
                      ->whereHas("get_comprobante",function($query) use($dat){
                        $query->whereDate("created_at",$dat);
                      })
                      ->count()}}
                </h4>
              </div>
            </div>
        @endfor
    </div>
  </div>
</div>
