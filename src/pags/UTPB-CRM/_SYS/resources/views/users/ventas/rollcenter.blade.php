@extends('users.'.Auth::user()->level->alias.'.home')
@php
  $c = isset($_REQUEST["c"]) ? \App\cliente::find($_REQUEST["c"]) : \App\cliente::where("status",'>',"3")->orderBy("id","asc")->first();
@endphp
@section('scripts')
  <script src="https://sdk.amazonaws.com/js/aws-sdk-2.826.0.min.js"></script>
  <script>

  AWS.config.region = 'us-east-2'; // Región
  AWS.config.credentials = new AWS.CognitoIdentityCredentials({
      IdentityPoolId: 'us-east-2:7f08076b-c8ce-4828-8449-c2c0c7a179ca',
  });

  var ultimos = function(){
    var s3 = new AWS.S3({
      apiVersion: '2006-03-01',
      params: {Bucket: "registrodellamadasedav"}
    });

    var params = { 
     Prefix : "connect/edav/CallRecordings/"
    }

    s3.listObjects(params, function (err, data) {
      if(err)throw err;
      console.log(data);
    });

  }


  function timeConverter(UNIX_timestamp){
    var a = new Date(UNIX_timestamp);
    var months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiempo','Octubre','Noviembre','Diciembre'];
    var year = a.getFullYear();
    var month = months[a.getMonth()];
    var date = a.getDate();
    var hour = a.getHours();
    var min = a.getMinutes();
    var sec = a.getSeconds();
    var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
    return time;
  }

  $(function(){
    ultimos();
  });
  </script>
@endsection
@section('content')
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <div class="col-12">
            <div class="float-right">
              <a target="_blank" href="https://unisant.my.connect.aws/ccp-v2">
                Teléfono virtual
                <i class="fas fa-external-link-alt"></i>
              </a>
            </div>
          </div>
          <h5 class="card-title">Roll Center</h5>
          <h6 class="card-subtitle mb-2 text-muted">Cliente
          @if ($c->status >=  4)
              <i class="fas fa-check-circle" style="color:green;" data-bs-toggle="tooltip" data-bs-placement="top" title="Alumno"></i>
          @endif
          </h6>
          <hr>
          <form class="" action="/ventas/nuevo" method="post">
            <div class="row">
              <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                <label for="formGroupExampleInput" class="form-label">Nombre(s)</label>
                <input type="text" class="form-control" name="nombre" required id="formGroupExampleInput" value="{{$c->nombre}}" placeholder="Nombre(s)">
              </div>
              <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                <label for="formGroupExampleInput2" class="form-label">Apellido Paterno</label>
                <input type="text" class="form-control" name="apat" id="formGroupExampleInput2" value="{{$c->apat}}" placeholder="Apellido Paterno">
              </div>
              <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                <label for="formGroupExampleInput2" class="form-label">Apellido Materno</label>
                <input type="text" class="form-control" name="amat" id="formGroupExampleInput2" value="{{$c->amat}}" placeholder="Apellido Materno">
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label for="formGroupExampleInput" class="form-label">Correo electr&oacute;nico</label>
                <input type="text" class="form-control" name="correo" id="formGroupExampleInput" value="{{$c->correo}}" placeholder="alguien@ejemplo.com">
              </div>
              <div class="col-12 col-md-12 col-lg-6">
                <label for="formGroupExampleInput2" class="form-label">N&uacute;mero de contacto</label>
                <input type="text" class="form-control" name="telefono"  id="formGroupExampleInput2" value="{{$c->telefono}}" placeholder="999-99-99-999">
              </div>
            </div>
          </form>
        </div>
        <div class="col-12">
          <h5 class="card-title">Registros</h5>
          <hr>
        </div>
        <div class="col-12">
          <div class="items">

          </div>
          <br>
          <br>
          <br>
        </div>
        <div class="col-12">
          <h5 class="card-title">Encuesta</h5>
          <hr>
          @if (count($c->encuestas) > 0)
            <h6 class="card-subtitle mb-2 text-muted">Encuestas del cliente:</h6>
            @foreach ($c->encuestas as $k)
              <div class="row">
                <div class="col">
                  {{$k->encuesta->nombre}}
                  <hr>
                </div>
              </div>
              <div class="col">
                @php
                  $i = 1;
                @endphp
                @if ($k->respondida == 0)
                  <form action="/encuestas/save" method="post">
                  @foreach ($k->encuesta->preguntas as $p)
                      <div class="col-12">
                        <input type="hidden" name="cliente_encuesta_id" value="{{md5($k->id)}}">
                        {{$i++}}. {{$p->pregunta}}
                        <div class="respuesta">
                          @if ($p->tipo == 1)
                            <input type="radio" name="p_{{$p->id}}" value="Si">  Si
                            <input type="radio" name="p_{{$p->id}}" value="No">  No
                          @endif
                          @if ($p->tipo == 2)
                              <input type="radio" name="p_{{$p->id}}" value="Excelente">  Excelente
                             <input type="radio" name="p_{{$p->id}}" value="Bueno">  Bueno
                             <input type="radio" name="p_{{$p->id}}" value="Regular">  Regular
                          @endif
                          @if ($p->tipo == 3)
                            <input type="radio" name="p_{{$p->id}}" value="Clara"> Clara
                            <input type="radio" name="p_{{$p->id}}" value="Inadecuada"> Inadecuada
                            <input type="radio" name="p_{{$p->id}}" value="Resuelta"> Resuelta
                            <input type="radio" name="p_{{$p->id}}" value="No resuelta">  No resuelta
                          @endif
                          @if ($p->tipo == 4)
                            <input type="radio" name="p_{{$p->id}}" value="Fácil y rápido">  Fácil y rápido
                            <input type="radio" name="p_{{$p->id}}" value="Accesible">  Accesible
                            <input type="radio" name="p_{{$p->id}}" value="Inaccesible">  Inaccesible
                            <input type="radio" name="p_{{$p->id}}" value="No me conecté">  No me conecté
                          @endif
                          @if ($p->tipo == 5)
                            <input type="radio" name="p_{{$p->id}}" value="En tiempo y forma">  En tiempo y forma
                            <input type="radio" name="p_{{$p->id}}" value="Es suficiente la información">  Es suficiente la información
                            <input type="radio" name="p_{{$p->id}}" value="No me han informado">  No me han informado
                          @endif
                          @if ($p->tipo == 0)
                            <input type="text" name="p_{{$p->id}}" class="form-control">
                          @endif
                          <hr>
                        </div>
                      </div>
                  @endforeach
                  <div class="col-12">
                    <input type="submit" class="btn btn-secondary" value="Guardar">
                  </div>
                </form>
                @else
                      @foreach ($k->encuesta->preguntas as $p)
                          {{$i++}}. {{$p->pregunta}}
                          <p class="respuesta">
                            @php
                              $p->cliente_encuesta_id = $k->id;
                            @endphp
                            @if ($p->respuesta == null)
                                No se respondio
                              @else
                                  {{$p->respuesta->respuesta}}
                            @endif
                          </p>
                      @endforeach
                @endif
              </div>
            @endforeach
          @else
            <h6 class="card-subtitle mb-2 text-muted">Sin encuestas</h6>
          @endif
          <hr>
            <div>
              <form  class="row" action="/encuestas/crear" method="post">
                <div class="col-12 col-md-12 col-lg-6">
                    <label for="">Tipo de encuesta:</label>
                    <input type="hidden" name="cid" value="{{md5($c->id)}}">
                    <select class="form-control" name="encuesta">
                      @foreach (\App\encuestas::all() as $en)
                        <option value="{{$en->id}}">{{$en->nombre}}</option>
                      @endforeach
                    </select>
                    <br>
                    <input type="submit" class="btn btn-primary" name="" value="Crear encuesta">
                    <br>
                    <br>
                </div>
                <div class="col-12 col-md-12 col-lg-6">
                  <br>
                </div>
              </form>
            </div>
        </div>
      </div>
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
    .arrow{
      text-align: center;
      line-height: 400px;
    }
    .arrow a{
      color:#C1814C;
    }
    .arrow a:hover{
      color:#C2925D;
    }
    .new{
      text-align: center;
      padding-top:50px;
      padding-bottom:50px;
    }
    .new i{
      color:green;
    }
    .respuesta{
      padding:10px;
      font-weight:normal;
      font-family: sans-serif;
    }
  </style>
@endsection
