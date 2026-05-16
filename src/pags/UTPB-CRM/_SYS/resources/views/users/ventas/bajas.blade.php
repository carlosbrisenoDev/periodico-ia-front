@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-left">
              <h5 class="card-title">Alumnos</h5>
              <h6 class="card-subtitle mb-2 text-muted">Con baja temporal</h6>
            </div>
            <div class="float-right">
              <input type="text" class="form-control  buscar" placeholder="Buscar ...">
            </div>
          </div>
          <hr>
            <table class="table table-sm table-striped table-hover">
              <thead>
                <td>#</td>
                <td>

                </td>
                <td>Nombre</td>
                <td>Paterno</td>
                <td>Materno</td>
                <td>T&eacute;lefono</td>
                <td>Correo</td>
                <td>Agente</td>
              </thead>
              <tbody>
                @foreach (\App\cliente::where("baja","<>",NULL)->get() as $c)
                  <tr>
                    <td style="text-align:center;">
                      <a href="/ventas/cliente?cid={{md5($c->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para ver">
                        UOV-{{\Carbon\Carbon::parse($c->created_at)->format("Y")}}-{{$c->id}}
                      </a>
                    </td>
                    <td>
                      @php
                        $cl = $c;
                      @endphp
                      @include('componentes.iconos')
                    </td>
                    <td>{{empty($c->nombre) ? "Sin nombre" : $c->nombre}}</td>
                    <td>{{empty($c->apat) ? "Sin apat" : $c->apat}}</td>
                    <td>{{empty($c->amat) ? "Sin amat" : $c->amat}}</td>
                    <td>{{empty($c->telefono) ? "Sin templatefono" : $c->telefono}}</td>
                    <td>{{empty($c->correo) ? "Sin correo" : $c->correo}}</td>
                    <td>
                      <a href="/bandeja/nuevo/enviar?a={{$c->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                        {{$c->correo}}
                      </a>
                    </td>
                    <td>
                      {{($cl->agente == null) ? "Sin agente": $cl->agente->name}}
                    </td>
                  </tr>
                @endforeach
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
  <script>
    $(".buscar").bind("keyup",function(){
      $.each($("tbody td"),function(i,e){
        if($(e).html().indexOf($(".buscar").val()) != -1){
          $($(e).parent()).css({"display":"table-row"});
        } else {
          $($(e).parent()).css({"display":"none"});
        }
      });
    });
  </script>
@endsection
