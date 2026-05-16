@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Notificación</h5>
          <h6 class="card-subtitle mb-2 text-muted">Escribe la notificación que deseas enviar vía correo electrónico</h6>
          <hr>
          <div class="row">
            <div class="col-12">
              <label for="">Asunto:</label>
              <input type="text" class="form-control asunto" value="" name="asunto" required placeholder="Asunto">
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <label for="">Mensaje</label></br>
              <textarea name="body" class="form-control editor" style="min-height:600px;width:100%;" id="editor"></textarea>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-6 col-lg-4 col-xl-4">
              <br>
              <div id="not" class="btn btn-primary">Notificar</div>
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <div class="progress">
            <div class="progress-bar enviados" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Alumnos</h5>
          @php
            $i = 1;
            $target = null;
            $title = "";
            $t = \Request::get("target");
            $targets = ["","Alumnos inscritos","Clientes","Clientes con formulario de inscripción","Clientes que solicitaron crédito","Alumnos nuevos (30 días)","Alumnos antiguos (30 días)","Clientes con incripción y crédito","Prueba"];
            switch($t){
              case 1:
                $target = \App\cliente::where("status",">",3)->with("isinscripcion")->where("baja",NULL);
                $title = $targets[1];
                break;
              case 2:
                $target = \App\cliente::where("status","<",4)->orWhere("status",null);
                $title = $targets[2];
                break;
              case 3:
                $target = \App\cliente::where("status","<",4)->has('isinscripcion');
                $title = $targets[3];
                break;
              case 4:
                $target = \App\cliente::where("status","<",4)->has('cocredito');
                $title = $targets[4];
                break;
              case 5:
                $target = \App\cliente::where("status",">",3)->where("baja",NULL)->whereHas("comprobante_pago",function($query){
                  $query->whereRAW("date(created_at) > '".\Carbon\Carbon::now()->subDays(30)->format("Y-m-d")."'");
                });
                $title = $targets[5];
                break;
              case 6:
                $target = \App\cliente::where("status","=",4)->where("baja",NULL)->whereHas("isinscripcion",function($query){
                  $query->whereRAW("date(created_at) < '".\Carbon\Carbon::now()->subDays(30)->format("Y-m-d")."'");
                });
                $title = $targets[6];
                break;
              case 7:
                $target = \App\cliente::where("status","<",4)->has('cocredito')->has("isinscripcion");
                $title = $targets[7];
                break;
              case 8:
                $target = \App\cliente::whereHas("usuario",function($q){
                  $q->where("name","like","%PRUEBA%");
                });
                $title = $targets[8];
                break;
              default:
                $target = \App\cliente::where("status",">",3)->with("isinscripcion")->where("baja",NULL);
                $title = $targets[1];
                break;
            }
          @endphp
          <h6 class="card-subtitle mb-2 text-muted">Lista de <b>{{$title}}</b> </h6>
          <hr>
            <div class="row">
              @foreach ($targets as $key => $value)
                <div class="col" style="padding-right:10px;display:inline-block;">
                  <a href="?target={{$key}}#lista">{{$value}}</a>
                </div>
              @endforeach
            </div>
          <hr>
            <table class="table table-sm table-striped table-hover" id="lista">
              <thead>
                <td>#</td>
                <td></td>
                <td>Nombre</td>
                <td>Correo</td>
                <td>F. Alta</td>
                <td>F. Inscrip</td>
                <td>Agente</td>
                <td></td>
              </thead>
              <tbody>
                @foreach ($target->get() as $cliente)
                    @php
                      $cl = $c = $cliente;
                      $correo = ($c->isinscripcion == NULL) ? $c->correo : $c->isinscripcion->correo;
                      $nombre = ($c->isinscripcion == NULL) ? $c->nombre : $c->isinscripcion->nombre_completo;
                    @endphp
                    @if (!empty($correo))
                      <tr>
                        <td>{{$i++}}</td>

                        <td style="text-align:center;">

                        </td>
                        <td>{{$nombre}}</td>
                        <td>
                          <a class="correo" href="/bandeja/nuevo/enviar?a={{$correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                            {{$correo}}
                          </a>
                        </td>
                        <td>
                          {{\Carbon\Carbon::parse($cliente->created_at)->format("Y/m/d")}}
                        </td>
                        <td>
                          {{\Carbon\Carbon::parse($cliente->reated_at)->format("Y/m/d")}}
                        </td>
                        <td>
                          {{($cliente->agente != null) ? $cliente->agente->name : "Sin agente"}}
                        </td>
                        <td>
                            <i class="fa fa-trash text-danger btn deltry" style="cursor:pointer;"></i>
                        </td>
                      </tr>
                    @endif
                @endforeach
                <tr>
                  <td>{{$i++}}</td>

                  <td style="text-align:center;">

                  </td>
                  <td>Control escolar</td>
                  <td>
                    <a class="correo" href="/bandeja/nuevo/enviar?a={{$correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                      yanetvaldivia95@gmail.com
                    </a>
                  </td>
                  <td>
                    -
                  </td>
                  <td>
                    -
                  </td>
                  <td>
                    No agente
                  </td>
                  <td>
                      <i class="fa fa-trash text-danger btn deltry" style="cursor:pointer;"></i>
                  </td>
                </tr>
              </tbody>
            </table>
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
  <script src="https://cdn.tiny.cloud/1/4eh5se8bzh2rwh4i26sh1a582xzigey103wfcd1h7smr5czs/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script type="text/javascript">
  var tiny;
  var rows = {{$i}}-1;
  var enviados = 0;
  $(".deltry").bind("click",function(){
    $(this).parent().parent().remove();
  })
  $(document).ready(function() {
    tiny = tinymce.init({
      selector: '.editor',
      toolbar_mode: 'floating',
      language:"es_MX",
      plugins: 'image imagetools table link list preview save',
      tollbar:"save"
    });
    function getNext(){
      var m = {
        to:"",
        cc:"",
        cco:"",
        asunto:$(".asunto").val(),
        body:tinymce.activeEditor.getContent()
      }
      var correo = $(".correo:first").text().trim();
      if(correo != ""){
        m.to = correo;
        $(".correo:first").parent().parent().remove();
        return m;
      }
      return null;
    }

    $("#not").bind("click",function(){
      enviar();
    });
    function enviar(){
      var m = getNext();
      if(m != null){
        $.post("/bandeja/enviarnotify",m).done(function(data){
          if(data.indexOf("1") != -1){
            enviados++;
            $(".enviados").css({"width":100/rows*enviados+"%"});
            $(".enviados").attr("aria-valuenow",100/rows*enviados);
            $(".enviados").text(100/rows*enviados+"%");
          }
        });
        setTimeout(enviar,5000);
      } else {
        alert("Fin del envio");
      }
    }
  });
  </script>
@endsection
