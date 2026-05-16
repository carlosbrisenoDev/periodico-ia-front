@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-body">
                  <form action="/agenda/guardar" method="post">
                  <table class="table table-responsive">
                    <tr>
                      @php
                      $Year = ((Request::has("oyear")) ? Request::get("oyear") : Date("Y"));
                       $Month = (Request::has("omes") ? Request::get("omes") : Date("m"));
                       $eventos = [];
                       for($h= 0;$h <32;$h++){
                         $eventos[$h]=[];
                       }
                       foreach(\App\evento::where('year',$Year)->where('mes',$Month)->get() as $evento){
                         array_push($eventos[$evento->dia],["evento"=>$evento->evento,"id"=>$evento->id]);
                       }
                       $Semana = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado"];
                       $Meses = ["","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
                       $Mes = $Meses[$Month*1];
                       $Hoy = ((Request::has("d")) ? Request::get("d") : Date("d"));
                       $Dias = date("t",mktime(0,0,0,$Month,1,$Year));
                       $usuario = ((Request::has("us")) ? (App\level::whereRAW('md5(id)="'.Request::get('us').'"')->first()) : Auth::user()->level);
                       echo "<input type='hidden' name='mes' id='mes' value='".($Month*1)."'>";
                       echo "<input type='hidden' name='year' id='year' value='$Year'>";
                       echo "<input type='hidden' name='d' id='d' value='$Hoy'>";
                       echo "<input type='hidden' name='user_id' id='user_id' value='".md5($usuario->id)."'>";
                       echo '<center><span class="dropdown">
                       <button id="bmes" class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                         '.$Mes.'
                         <span class="caret"></span>
                       </button>
                       <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';
                       for($i = 1; $i < count($Meses);$i++)
                       {
                         $nMes = $Meses[$i];
                         echo "<li text='$nMes' value='$i'><a href='#' onclick='choseThis(this,\"mes\")'>$nMes</a></li>";
                       }
                       echo '</ul></span>';
                       echo '<span class="dropdown col-md-0">
                       <button id="byear" class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                         '.$Year.'
                         <span class="caret"></span>
                       </button>
                       <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';
                       for($i = 2018; $i < Date("Y")+20;$i++)
                       {
                         echo "<li text='$i' value='$i'><a href='#' onclick='choseThis(this,\"year\")'>$i</a></li>";
                       }
                       echo '</ul></span></center><hr>';
                       echo "<table class='table' style='text-align:center;'>";
                       echo "<tr class='bg-primary'>";
                       for($i = 0; $i < count($Semana);$i++)
                       {
                           echo "<th><center>".$Semana[$i]."</center></th>";
                       }
                       echo "</tr>";
                       echo "<tr>";
                       $inicio =  date("w", mktime(0, 0, 0, $Month, 1, $Year));
                       $break = 0;
                       $j = 1;
                       for($i = 0; $i < $Dias;)
                       {
                           $break++;
                           if($inicio>$i)
                           {
                               echo "<td></td>";
                               $inicio--;
                           } else {
                               $j = ($j==4)?1:$j++;
                               $i++;
                               $all = "";
                               if(isset($eventos[$i]))
                               {
                                 foreach($eventos[$i] as $evento){
                                   $all .= "<div>".$evento["evento"]."</div>";
                                 }
                               }
                               echo "<td class='day ".(($i == $Hoy) ? "selected" : "")." ".(($i == $Hoy && $Month==date("m") && $Year == date("Y")) ? 'hoy' : '')." ".(
                                 ($i >= $Hoy || $Mes != $Meses[Date("m")*1] || $Year != Date("Y")) ? "bg-td-calendar" : "bg-td-calendar-hover"
                               )."' onclick='day($i)' data-toggle='modal' data-target='#exampleModalCenter'><span>$i</span>".$all."</td>";
                           }
                           echo ($break%7==0) ? "</tr><tr>" : "";
                       }
                       echo "</tr>";
                       echo "</table>";
                      @endphp
                  </tr>
                  </table>
                  <hr>
                  <div class="col-md-12 bs-callout bs-callout-info" id="agenda">
                    <div class="clearfix">
                      <div class="pull-left">
                        Agenda de
                          <span class="dropdown col-md-0">
                            <button id="byear" class="btn btn-link dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                              {{$usuario->name}}
                              <span class="caret"></span>
                            </button>
                            @php
                                $data = (Auth::user()->level->name=="Administrador") ?
                                App\level::whereNotIn('name',["Empleado","Franquiciatario"])->get() :
                                App\permisa::where('current_id',Auth::user()->level->id)->get();
                                $i = 0;
                            @endphp
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                              @if (Auth::user()->level->name=="Administrador")
                                @foreach ($data as $us)
                                  <li a="{{md5($us->id)}}"><a href="#" onclick="setLevel(this)">{{$us->name}}</a></li>
                                @endforeach
                              @else
                                  <li a="{{md5(Auth::user()->level->id)}}"><a href="#" onclick="setLevel(this)">{{Auth::user()->level->name}}</a></li>
                                  @foreach ($data as $us)
                                    <li a="{{md5($us->level->id)}}"><a href="#" onclick="setLevel(this)">{{$us->level->name}}</a></li>
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

                    <table class="table table-striped">
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
                  </div>
                  <div class="col-md-12 nopadding">
                    <span class="btn btn-primary printo"><i class="fa fa-print"></i> Imprimir agenda</span>
                  </div>
                  @else
                    No hay eventos programados para este día
                @endif
                </div>
            </div>
        </div>
@endsection
@section('modal')
  <div class="modal fade" id="calendarmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Evento</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form class="" action="/agenda/guardar" method="post">
              <div class="col-md-12">
                <label for="">Evento</label>
                <input required type="text" name="evento" value="" placeholder="Evento" class="form-control">
              </div>
              <div class="col-md-12">
                <input type="hidden" name="year" value="{{$Year}}">
                <input type="hidden" name="mes" value="{{$Month}}">
                <input type="hidden" class="dia" name="dia" value="">
                <label for="">Hora</label>
                <input required type="text" name="hora" value="12:00 PM" class="form-control hora">
              </div>
              <div class="col-md-12">
                <label for="">Termina</label>
                <input required type="date" name="date" value="" class="termina form-control">
              </div>
              <div class="col-md-12">
                <label for="">Invitados</label>
                <div style="max-height:100px;overflow-y:auto;">
                  <table class="listaf table table-stripped">
                  </table>
                </div>
                <select class="form-control invitados">
                  <option value="0">Seleccionar</option>
                  @foreach (\App\user::where("level_id",'<>','0')->get() as $cat)
                    <option value="{{$cat->id}}">{{$cat->name}} ({{$cat->email}})</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-12">
                <label for="">Nota</label>
                <textarea name="nota" style="overflow:hidden;" class="form-control" rows="2" placeholder="Nota"></textarea>
              </div>
              <div class="col-md-12 clearfix">
                <div class="pull-right">
                </br>
                  <button type="submit" class="btn btn-primary" value=""><i class="fa fa-save"></i> Guardar</button>
                </div>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>

@endsection
@section('scripts')
  <script type="text/javascript">
    function choseThis(e,w){
      $("#"+w).val(($(e.parentNode).attr("value")));
      go();
    }
    $(".drop").bind('click',function(){
      if($(this).attr("val")=="si")
      {
        $(".nuevo").fadeIn();
        $(this).attr("val","no");
        $(this).children().removeClass("caret");
        $(this).children().addClass("fa fa-caret-up");
      } else {
        $(".nuevo").fadeOut();
        $(this).attr("val","si");
        $(this).children().removeClass("fa fa-caret-up");
        $(this).children().addClass("caret");
      }
    });
    function day(d){
      $(".modal").modal();
      $(".termina").val(d+"/{{$Month}}/{{$Year}}");
      $(".dia").val(d);
      //go();
    }
    function setLevel(e)
    {
      $("#user_id").val(($(e.parentNode).attr("a")));
      console.log($(e.parentNode).attr("a"));
      go();
    }
    function go()
    {
      location.href = "/{{Auth::user()->level->alias}}/agenda?oyear="+$("#year").val()+"&omes="+$("#mes").val()+"&d="+$("#d").val()+"&us="+$("#user_id").val();
    }
    $(function(){
      $(".invitados").bind("change",function(){
        $(".listaf").prepend($("<tr>").append($("<td>").text($(".invitados option:selected").text())).append($("<td class='drop'>").append($("<i class='fa fa-trash'>"))));
      });
    });
    var drop = function(){
      $(".drop").bind("click",function(){
        var e = $(this);
        var id = e.attr("id");
        e.empty();
        e.append("<i class='fa fa-gear fa-spin'></i>");
      });
    }
  </script>
  <script type="text/javascript">
    $(".printo").bind('click',function(){
      var objeto=document.getElementById('agenda');
      var ventana=window.open("/general/imprimir?oyear="+$("#year").val()+"&omes="+$("#mes").val()+"&d="+$("#d").val()+"&us="+$("#user_id").val(),'_blank');
    });
  </script>
@endsection
