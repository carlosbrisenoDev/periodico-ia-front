@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Alumnos</h5>
          <h6 class="card-subtitle mb-2 text-muted">Lista de alumnos inscritos</h6>
          <hr>
            <table class="table table-sm table-striped table-hover">
              <thead>
                <td>#</td>
                <td></td>
                <td>Nombre</td>
                <td>T&eacute;lefono</td>
                <td>Correo</td>
                <td>Ciudad</td>
                <td>F. Alta</td>
                <td>F. Inscrip</td>
              </thead>
              <tbody>
                @php
                  $i = 1;
                @endphp
                @foreach (\App\inscripciones::all() as $c)
                  @if (!strstr($c->cliente->nombre,"PRUEBA") && $c->cliente->status == 4)
                    <tr>
                      <td>{{$i++}}</td>
                      <td style="text-align:center;">
                        <a href="/ventas/cliente?cid={{md5($c->cliente->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para ver">
                          UOV-{{\Carbon\Carbon::parse($c->created_at)->format("Y")}}-{{$c->id}}
                        </a>
                      </td>
                      <td>{{$c->nombre_completo}}</td>
                      <td>{{$c->tel}}</td>
                      <td>
                        <a href="/bandeja/nuevo/enviar?a={{$c->correo}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Click para enviar un correo electr&oacute;nico">
                          {{$c->correo}}
                        </a>
                      </td>
                      <td>{{$c->estado}}</td>
                      <td>
                        {{\Carbon\Carbon::parse($c->cliente->created_at)->format("Y/m/d")}}
                      </td>
                      <td>
                        {{\Carbon\Carbon::parse($c->created_at)->format("Y/m/d")}}
                      </td>
                    </tr>
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
