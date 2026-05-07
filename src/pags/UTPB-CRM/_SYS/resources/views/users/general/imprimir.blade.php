<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> @yield('title')</title>

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <style media="screen">
    .bs-callout-info {
        border-left-color: #1b809e;
    }
    .bs-callout {
        padding: 20px;
        margin: 20px 0;
        border: 1px solid #337ab7;
        border-left-width: 5px;
        border-radius: 3px;
    }
    </style>
</head>
<body>
        <div id="contenido" class="col-md-12">
          <div class="col-md-12 bs-callout bs-callout-info" id="agenda">
            @php
            $Year = ((Request::has("oyear")) ? Request::get("oyear") : Date("Y"));
             $Month = (Request::has("omes") ? Request::get("omes") : Date("m"));
             $Semana = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado"];
             $Meses = ["","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
             $Mes = $Meses[$Month*1];
             $Hoy = ((Request::has("d")) ? Request::get("d") : Date("d")-1);
             $Dias = date("t",mktime(0,0,0,$Month,1,$Year));
             $usuario = ((Request::has("us")) ? (App\level::whereRAW('md5(id)="'.Request::get('us').'"')->first()) : Auth::user()->level);
              $eventos = App\evento::where('level_id',$usuario->id)->where('dia',$Hoy)->where('mes',$Month)->where('year',$Year)->orderBy("hora")->orderBy("minuto")->get();
            @endphp
            @if (count($eventos))
              <div class="clearfix">
                <div class="pull-left">
                  Agenda de
                    <span class="dropdown col-md-0">
                      <button id="byear" class="btn btn-link dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        {{$usuario->name}}
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li value="{{Auth::user()->level->id}}"><a href="#" onclick="setLevel(this)">{{Auth::user()->level->name}}</a></li>
                        @foreach (App\permisa::where('current_id',Auth::user()->level->id)->get() as $us)
                          <li value="{{$us->id}}"><a href="#" onclick="setLevel(this)">{{$us->level->name}}</a></li>
                        @endforeach
                      </ul>
                    </span>
                </div>
                <div class="pull-right">
                  <div class="col-md-12">
                    {{$Hoy." de ".$Mes." del ".$Year}}
                  </div>
                </div>
              </div>
            <table class="table table-responsive table-striped">
              <tr class="bg-primary2">
                <th>Hora</th>
                <th>Evento</th>
                <th>Nota</th>
              </tr>
              @php
              @endphp
              @foreach ($eventos as $event)
                <tr>
                  <td>{{((($event->hora > 12) ? ($event->hora-12) : $event->hora)).":".(($event->minuto < 10) ? "0".$event->minuto : $event->minuto)." ".((($event->hora > 12) ? "PM": "AM"))}}</td>
                  <td>{{$event->evento}}</td>
                  <td>{{$event->nota}}</td>
                </tr>
              @endforeach
            </table>
          </div>
          </div>
        @else
          No hay eventos programados para este día
      @endif
        </div>
        <script type="text/javascript">
          window.print();
          window.close();
        </script>
</body>
</html>
