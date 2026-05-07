@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-left">
              <h5 class="card-title">Calendario</h5>
              @php
                $i = 1;
                $mes = isset($_REQUEST["mes"]) ? $_REQUEST["mes"] : \Carbon\carbon::now()->format("M");
                $mes_n = \Carbon\carbon::parse($mes)->format("m")*1;
                $anio = isset($_REQUEST["anio"]) ? $_REQUEST["anio"] : \Carbon\carbon::now()->format("Y");
                $i = 1;
                $r = 1;
                $semana = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado"];
                $meses = ["","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
              @endphp
              <h6 class="card-subtitle mb-2 text-muted">{{$meses[date("m")*1]}}</h6>
            </div>
            <div class="float-right">

            </div>
          </div>
          <hr>
          <div class="row">
            @foreach ($semana as $key => $value)
              <div class="col text-center bg-dark text-light">
                <h6>{{$value}}</h6>
              </div>
            @endforeach
          </div>
          
          <div class="row">
            @for ($i=0; $i < $j=date('N',mktime(0, 0, 0, $mes_n, 1)); $i++)
              <div class="col"></div>
              @php
                $r++;
              @endphp
            @endfor
            @for ($i=0; $i < cal_days_in_month(CAL_GREGORIAN, $mes_n, $anio); $i)
              @php
                $dat = \Carbon\carbon::parse("$anio-$mes_n-".++$i)->toDateString();
                $count = \App\cliente::
                  whereHas("get_comprobante",function($query) use($dat){
                    $query->whereRAW("date(created_at) = date('$dat')");
                  })
                  ->count();
                $nombres = "";
                foreach(App\cliente::selectRAW("*, count(*) as total")
                  ->with("get_comprobante")
                  ->whereHas("get_comprobante",function($query) use($dat){
                    $query->whereDate("created_at",$dat);
                  })->where("nombre","not like","%PRUEBA%")->groupby("agente_id")->get() as $agente)
                    {
                      if($agente->agente){
                        $nombres .= $agente->agente->name."(".$agente->total.") </br>";
                      }
                    }
                    
              @endphp
              
              <div class="col" style="min-height:150px;display:table-cell;vertical-align:middle;">
                <div class="text-center border" style="padding:5px;margin:5px;height:100%;">
                  <h2 class="text-center">{{$i}}</h2>
                  @if ($count > 0)
                    <small>
                      {!!$nombres!!}
                    </small>
                    <h4 class="badge bg-success text-light">
                      {{$count}}
                    </h4>
                  @endif
                  
                </div>
              </div>
              @if ($r%7==0)
              </div>
                <hr>
              <div class="row">
              @endif
              @php
                $r++;
              @endphp
              
            @endfor
            @for ($i=0; $i < 35-$r; $i++)
              <div class="col"></div>
            @endfor
            
          </div>
          </div>
        </div>
    </div>
    
    <div class="col-12 col-md-12 col-lg-6">

    </div>
  </div>
@endsection
@section('styles')
  <style media="screen">
    hr{
      height:10px;
      background-color:#f6f6f6;
      border:0;
    }
    .text-muted{
      color:#BD773E !important;
    }
  </style>
@endsection
@section('scripts')
  <script>
    $('.count').each(function () {
      var $this = $(this);
      jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
        duration: 1000,
        easing: 'swing',
        step: function () {
          $this.text(Math.ceil(this.Counter));
        }
      });
    });
  </script>
@endsection
