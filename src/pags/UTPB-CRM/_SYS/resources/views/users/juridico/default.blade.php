@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default large">
                <div class="card-body">
                  <div class="col-md-12 bs-callout bs-callout-info" id="agenda">
                    <div class="clearfix">
                      <div class="pull-left">
                        @php
                        $Year = ((Request::has("oyear")) ? Request::get("oyear") : Date("Y"));
                         $Month = (Request::has("omes") ? Request::get("omes") : Date("m"));
                         $Semana = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado"];
                         $Meses = ["","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
                         $Mes = $Meses[$Month*1];
                         $Hoy = ((Request::has("d")) ? Request::get("d") : Date("d"));
                         $Dias = date("t",mktime(0,0,0,$Month,1,$Year));
                         $usuario = ((Request::has("us")) ? (App\level::whereRAW('md5(id)="'.Request::get('us').'"')->first()) : Auth::user()->level);
                         @endphp
                        Agenda de
                          <span class="dropdown col-md-0">
                            <button id="byear" class="btn btn-link dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                              {{$usuario->name}}
                              <span class="caret"></span>
                            </button>
                            @php

                              $data = (Auth::user()->level->name=="Alcalde") ?
                                App\level::all() :
                                App\permisa::where('current_id',Auth::user()->level->id)->get();
                                $i = 0;
                            @endphp
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                              @if (Auth::user()->name=="Alcalde")
                                @foreach ($data as $us)
                                  <li value="{{md5($us->id)}}"><a href="#" onclick="setLevel(this)">{{$us->name}}</a></li>
                                @endforeach
                              @else
                                  <li value="{{md5(Auth::user()->level->id)}}"><a href="#" onclick="setLevel(this)">{{Auth::user()->level->name}}</a></li>
                                  @foreach ($data as $us)
                                    <li value="{{md5($us->level->id)}}"><a href="#" onclick="setLevel(this)">{{$us->level->name}}</a></li>
                                  @endforeach
                              @endif
                            </ul>
                          </span>
                      </div>
                      <div class="pull-right">
                        <div class="col-md-12">
                          {{$Hoy." de ".$Mes." del ".$Year}}
                        </div>
                      </div>
                    </div>
                  </form>
                    <hr>
                    @php
                      $eventos = App\evento::where('level_id',$usuario->id)->where('dia',$Hoy)->where('mes',$Month)->where('year',$Year)->orderBy("hora")->orderBy("minuto")->get();
                    @endphp
                    @if (count($eventos))

                    <table class="table table-responsive table-striped">
                      <tr class="bg-primary2">
                        <th>Hora</th>
                        <th>Evento</th>
                        <th>Nota</th>
                        <th></th>
                      </tr>
                      @php
                      @endphp
                      @foreach ($eventos as $event)
                        <tr>
                          <td>{{((($event->hora > 12) ? ($event->hora-12) : $event->hora)).":".(($event->minuto < 10) ? "0".$event->minuto : $event->minuto)." ".((($event->hora > 12) ? "PM": "AM"))}}</td>
                          <td>{{$event->evento}}</td>
                          <td>{{$event->nota}}</td>
                          <td style="width:40px;"><a align="right" href="/agenda/trash/{{md5($event->id)}}" class="btn btn-default"><i class="fa fa-close"></i></a></td>
                        </tr>
                      @endforeach
                    </table>
                  @else
                    No hay eventos programados para este día
                @endif
              </div>
                <div class="col-md-12 bs-callout bs-callout-info">
                      <h3>Reportes</h3>
                      @php
                        $datos = \App\estado::all();
                      @endphp
                      @foreach ($datos as $estado)
                        @if($datos[count($datos)-1] != $estado)
                          <hr>
                          Reportes <i>{{$estado->nombre}}</i>
                          <table class="table table-responsive">
                            @php
                              $reportes = \App\reporte::where('estado_id',$estado->id)
                              ->where('level_id',Auth::user()->level->id)
                              ->get();
                            @endphp
                            @if (count($reportes) > 0)
                              <table class="table table-responsive table-stripped">
                                <tr>
                                  <th><b>Folio</b></th>
                                  <th>Título del reporte</th>
                                  <th>Dirigido a</th>
                                  <th>Prioridad</th>
                                  <th>Estado</th>
                                  <th>Fecha de creación</th>
                                </tr>
                                @foreach ($reportes as $reporte)
                                  <tr>
                                    <td>{{$reporte->id}}</td>
                                    <td><a href="/reportes/modify/{{md5($reporte->id)}}">{{$reporte->nombre}}</a></td>
                                    <td>{{$reporte->level->name}}</td>
                                    <td style="color:{{$reporte->prioridad->color}};">{{$reporte->prioridad->nombre}}</td>
                                    <td>{{$reporte->estado->nombre}}</td>
                                    <td>{{$reporte->full_fecha()}}</td>
                                  </tr>
                                @endforeach
                              </table>
                              @else
                                <h4>No hay resultados</h4>
                            @endif
                          </table>
                        @endif
                      @endforeach
                    </div>
                </div>
            </div>
        </div>
@endsection
