@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-start">
              <h5 class="card-title">Alumnos</h5>
              <h6 class="card-subtitle mb-2 text-muted">Lista de alumnos inscritos</h6>
            </div>
            <div class="float-end">
              <a href="/alumnos/inscritoscsv/get" class="btn btn-success">
                <i class="fa fa-download"></i> Descargar
              </a>
            </div>
          </div>
          <hr>
            <table class="table table-sm table-striped table-hover">
              <thead>
                <td>#</td>
                <td></td>
                <td></td>
                <td>Nombre</td>
                <td>T&eacute;lefono</td>
                <td>Correo</td>
                <td>F. Alta</td>
                <td>F. Inscrip</td>
              </thead>
              <tbody>
                @php
                  $i = 1;
                @endphp
                @foreach (\App\cliente::where("status",4)->get() as $cr)
                  @php
                    $c = $cr->isinscripcion;
                  @endphp
                  @if (!strstr($cr->nombre,"PRUEBA"))
                    <tr>
                      <td>{{$i++}}</td>
                      @php
                        $cl = $cr;
                      @endphp
                      <td>@include('componentes.iconos')</td>
                      <td style="text-align:center;">
                        <a href="/ventas/cliente?cid={{md5($cr->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para ver">
                          UOV-{{\Carbon\Carbon::parse($cr->created_at)->format("Y")}}-{{$cr->id}}
                        </a>
                      </td>
                      <td>{{($c != NULL) ? $c->nombre_completo : $cr->nombre}}</td>
                      <td>{{($c != null) ? $c->tel : $cr->telefono}}</td>
                      <td>
                        <a href="/bandeja/nuevo/enviar?a={{($c!=null)?$c->correo:$cr->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                          {{($c!=null)? $c->correo: $cr->correo}}
                        </a>
                      </td>
                      <td>
                        {{\Carbon\Carbon::parse($cr->created_at)->format("Y/m/d")}}
                      </td>
                      <td>
                        {{\Carbon\Carbon::parse(($c!=null) ? $c->created_at : $cr->created_at)->format("Y/m/d")}}
                      </td>
                    </tr>
                    @else

                  @endif
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
