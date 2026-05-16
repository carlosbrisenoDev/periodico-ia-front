@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-start">
              <h5 class="card-title">Cartera</h5>
              <h6 class="card-subtitle mb-2 text-muted">Clientes</h6>
            </div>
            <div class="float-end">
              <input type="text" class="form-control  buscar" placeholder="Buscar ...">
            </div>
          </div>
          <hr>
          @php
            $i=1;
          @endphp
            <table class="table table-sm table-striped table-hover">
              <thead>
                <td></td>
                <td>#</td>
                <td></td>
                <td>Nombre</td>
                <td>Paterno</td>
                <td>Materno</td>
                <td>Inscrito</td>
                <td>Tentativa</td>
                <td>T&eacute;lefono</td>
                <td>Créditos</td>
                <td>Correo</td>
              </thead>
              <tbody>
                @foreach (\App\cliente::whereHas("isinscripcion")->whereHas("credito_info",function($query){
                  $query->where("status","cartera");
                })->orderBy("created_at","desc")->get() as $c)
                  <tr class="{!! ($c->nombre=="PRUEBA" ? "bg-secondary" : "") !!}">
                    <td>{{$i++}}</td>
                    <td style="text-align:center;">
                      <a href="/creditos/creditos?cid={{md5($c->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para ver">
                        CUOV-{{\Carbon\Carbon::parse($c->created_at)->format("Y")}}-{{$c->id}}
                      </a>
                    </td>
                    <td>
                      @php
                        $cl = $c;
                      @endphp
                      @include('componentes.iconos')
                    </td>
                    <td>
                      {!!count($c->carteras) > 0 ? '<i class="fas fa-check-square" style="color:green;"></i>' : ($c->status >= 4 ? '<i class="far fa-clock text-warning"></i>' : '<i class="fas fa-ban text-danger"></i>')!!}
                       {{$c->nombre}}
                    </td>
                    <td>{{$c->apat}}</td>
                    <td>{{$c->amat}}</td>
                    <td>{{$c->isinscripcion->nombre_completo}}</td>
                    <td>{{$c->isinscripcion->periodo}}</td>
                    <td>{{$c->telefono}}</td>
                    <td>{{count($c->carteras)}}</td>
                    <td>
                      <a href="/bandeja/nuevo/enviar?a={{$c->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                        {{$c->correo}}
                      </a>
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
        if($(e).text().toUpperCase().indexOf($(".buscar").val().toUpperCase()) != -1){
          $($(e).parent()).css({"display":"table-row"});
        } else {
          $($(e).parent()).css({"display":"none"});
        }
      });
    });
  </script>
@endsection
